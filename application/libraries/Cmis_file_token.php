<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Server-side CMIS file-token session.
 *
 * After CMIS authenticates its own user, this asks the PPIS user service for the
 * existing HMAC file token and keeps it in a file keyed by an HttpOnly cookie.
 * The token is not written into URLs or HTML.
 */
class Cmis_file_token {

	const COOKIE = 'cmis_file_sid';
	const TTL = 7200;

	private $CI;

	public function __construct() {
		$this->CI =& get_instance();
	}

	public function establish($user) {
		try {
			$this->issueAndStore($user);
		} catch (Throwable $e) {
			log_message('error', 'CMIS file token was not issued');
		}
	}

	public function currentToken() {
		$record = $this->readRecord();
		if ($record === null) {
			return null;
		}
		return $record['token'];
	}

	public function destroy() {
		$sid = $this->cookieSid();
		if ($sid !== null) {
			$path = $this->sessionPath($sid);
			if (is_file($path)) {
				@unlink($path);
			}
		}
		$this->clearCookie();
	}

	public function stream($id, $download) {
		$id = (string) $id;
		if (!preg_match('/^[0-9]+$/', $id)) {
			$this->deny(400);
		}
		$token = $this->currentToken();
		if ($token === null) {
			$this->deny(401);
		}
		$base = $this->fileServiceBase();
		if ($base === '') {
			$this->deny(502);
		}

		$action = $download ? 'download' : 'view';
		$url = $base.'/file/'.$action.'/'.$id;
		$contentType = '';
		$disposition = '';
		$headersSent = false;

		$this->clearOutputBuffers();
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$token));
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($ch, CURLOPT_TIMEOUT, 120);
		curl_setopt($ch, CURLOPT_HEADERFUNCTION, function ($ch, $header) use (&$contentType, &$disposition) {
			$parts = explode(':', $header, 2);
			if (count($parts) === 2) {
				$name = strtolower(trim($parts[0]));
				$value = trim($parts[1]);
				if ($name === 'content-type') {
					$contentType = $value;
				} elseif ($name === 'content-disposition') {
					$disposition = $value;
				}
			}
			return strlen($header);
		});
		curl_setopt($ch, CURLOPT_WRITEFUNCTION, function ($ch, $chunk) use (&$headersSent, &$contentType, &$disposition, $download) {
			$code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
			if ($code !== 200) {
				return strlen($chunk);
			}
			if (!$headersSent) {
				$this->sendSuccessHeaders($contentType, $disposition, $download);
				$headersSent = true;
			}
			echo $chunk;
			return strlen($chunk);
		});
		curl_exec($ch);
		$errno = curl_errno($ch);
		$status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
		curl_close($ch);

		if ($headersSent) {
			exit;
		}
		if ($errno || $status === 0) {
			$this->deny(502);
		}
		if ($status === 200) {
			$this->sendSuccessHeaders($contentType, $disposition, $download);
			exit;
		}
		if ($status === 401 || $status === 403 || $status === 404) {
			$this->deny($status);
		}
		$this->deny(502);
	}

	private function issueAndStore($user) {
		if (!is_object($user) || !isset($user->USER_ID) || (string) $user->USER_STATUS === '0') {
			return;
		}
		$subject = (string) $user->USER_ID;
		if (!preg_match('/^[0-9]+$/', $subject)) {
			return;
		}

		$officeId = $this->resolveOfficeId(isset($user->FIELD_OFFICE) ? $user->FIELD_OFFICE : '');
		if ($officeId === null) {
			log_message('error', 'CMIS file token skipped because the user office could not be resolved');
			return;
		}

		$role = $this->roleFor($user);
		$permissions = $this->filePermissions($role['roleId']);
		if ($permissions === null) {
			log_message('error', 'CMIS file token skipped because user rights could not be read');
			return;
		}
		$name = $this->userName($user);
		if ($name === '') {
			log_message('error', 'CMIS file token skipped because the user name is empty');
			return;
		}

		$token = $this->requestToken(array(
			'subject' => $subject,
			'officeId' => $officeId,
			'roleId' => $role['roleId'],
			'role' => $role['role'],
			'name' => $name,
			'canCreate' => $permissions['canCreate'],
			'canUpdate' => $permissions['canUpdate'],
			'canDelete' => $permissions['canDelete'],
		));
		if ($token === null) {
			return;
		}
		$this->store($token, $subject);
	}

	/**
	 * Form 5 module 2 (access_f5_write) is the CMIS right that shows Add, Edit,
	 * Delete, and the attachment Upload control. Module 1 only opens the file list.
	 * The file service requires canCreate for the first version and canUpdate for
	 * a later version; both use that same upload control. Delete follows the same
	 * write right. A missing module row leaves the control visible, so it is granted.
	 * ACCESS_RIGHTS "0" is an explicit denial.
	 */
	private function filePermissions($levelId) {
		$writeDenied = false;
		$query = $this->CI->db->query(
			'SELECT USER_LEVEL_MODULE_ID, ACCESS_RIGHTS FROM USER_LEVEL_RIGHTS WHERE USER_LEVEL_ID = ?',
			array($levelId)
		);
		if (!$query) {
			return null;
		}
		foreach ($query->result() as $row) {
			if ((string) $row->USER_LEVEL_MODULE_ID === '2' && (string) $row->ACCESS_RIGHTS === '0') {
				$writeDenied = true;
			}
		}
		$allowed = !$writeDenied;
		return array(
			'canCreate' => $allowed,
			'canUpdate' => $allowed,
			'canDelete' => $allowed,
		);
	}

	private function roleFor($user) {
		$roleId = isset($user->USER_LEVEL_ID) ? trim((string) $user->USER_LEVEL_ID) : '';
		if (!preg_match('/^[0-9]+$/', $roleId)) {
			$roleId = '0';
		}
		$role = '';
		$query = $this->CI->db->query(
			'SELECT USER_LEVEL_NAME FROM USER_LEVEL WHERE USER_LEVEL_ID = ? LIMIT 1',
			array($roleId)
		);
		if ($query && $query->num_rows() > 0) {
			$role = trim((string) $query->row()->USER_LEVEL_NAME);
		}
		if ($role === '') {
			$role = 'CMIS '.$roleId;
		}
		$role = preg_replace('/[\r\n\t]+/', ' ', $role);
		if (strlen($role) > 200) {
			$role = substr($role, 0, 200);
		}
		return array('roleId' => $roleId, 'role' => $role);
	}

	private function userName($user) {
		$name = isset($user->USER_FULLNAME) ? trim((string) $user->USER_FULLNAME) : '';
		if ($name === '' && isset($user->USER_NAME)) {
			$name = trim((string) $user->USER_NAME);
		}
		$name = preg_replace('/[\r\n\t]+/', ' ', $name);
		if (function_exists('mb_strlen') && mb_strlen($name, 'UTF-8') > 200) {
			$name = mb_substr($name, 0, 200, 'UTF-8');
		} elseif (strlen($name) > 200) {
			$name = substr($name, 0, 200);
		}
		return trim($name);
	}

	private function resolveOfficeId($fieldOffice) {
		$name = trim((string) $fieldOffice);
		if ($name === '') {
			return null;
		}
		if (preg_match('/^[0-9]+$/', $name)) {
			return $name;
		}
		$pis = $this->CI->load->database('pis', TRUE);
		$query = $pis->query(
			'SELECT ID FROM field_office WHERE NAME = ? AND ENABLED = 1 LIMIT 1',
			array($name)
		);
		if ($query && $query->num_rows() > 0) {
			$id = (string) $query->row()->ID;
			if (preg_match('/^[0-9]+$/', $id)) {
				return $id;
			}
		}
		return null;
	}

	private function requestToken($payload) {
		$key = $this->CI->config->item('cmis_file_service_key');
		$base = $this->CI->config->item('ppis_user_service');
		if (!is_string($key) || $key === '' || !is_string($base) || $base === '') {
			log_message('error', 'CMIS file token skipped because the user-service setting is missing');
			return null;
		}
		$url = rtrim($base, '/').'/authenticate/cmis-file-token';
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'X-CMIS-Service-Key: '.$key,
		));
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		curl_setopt($ch, CURLOPT_TIMEOUT, 8);
		$body = curl_exec($ch);
		$errno = curl_errno($ch);
		$http = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($errno || $http !== 200) {
			log_message('error', 'CMIS file token request failed HTTP '.$http);
			return null;
		}
		$decoded = json_decode($body, true);
		if (!is_array($decoded) || empty($decoded['fileToken']) || !is_string($decoded['fileToken'])) {
			log_message('error', 'CMIS file token response did not include a token');
			return null;
		}
		return $decoded['fileToken'];
	}

	private function store($token, $userId) {
		$dir = $this->sessionDir();
		if (!is_dir($dir)) {
			mkdir($dir, 0700, true);
		}
		$this->destroy();
		$this->sweep($dir);
		$sid = bin2hex(random_bytes(32));
		$exp = time() + self::TTL;
		$payload = json_encode(array(
			'token' => $token,
			'user_id' => (string) $userId,
			'exp' => $exp,
		));
		$path = $this->sessionPath($sid);
		if (file_put_contents($path, $payload, LOCK_EX) === false) {
			log_message('error', 'CMIS file token session could not be stored');
			return;
		}
		@chmod($path, 0600);
		setcookie(self::COOKIE, $sid, array(
			'expires' => $exp,
			'path' => '/',
			'secure' => $this->isHttps(),
			'httponly' => true,
			'samesite' => 'Lax',
		));
	}

	private function readRecord() {
		$sid = $this->cookieSid();
		if ($sid === null) {
			return null;
		}
		$path = $this->sessionPath($sid);
		if (!is_file($path)) {
			$this->clearCookie();
			return null;
		}
		$decoded = json_decode((string) file_get_contents($path), true);
		if (!is_array($decoded) || empty($decoded['token']) || empty($decoded['exp']) || (int) $decoded['exp'] < time()) {
			@unlink($path);
			$this->clearCookie();
			return null;
		}
		return $decoded;
	}

	private function cookieSid() {
		if (!isset($_COOKIE[self::COOKIE])) {
			return null;
		}
		$sid = (string) $_COOKIE[self::COOKIE];
		if (!preg_match('/^[a-f0-9]{64}$/', $sid)) {
			return null;
		}
		return $sid;
	}

	private function sessionDir() {
		return APPPATH.'cache/cmis_file_sessions';
	}

	private function sessionPath($sid) {
		return $this->sessionDir().'/'.$sid;
	}

	private function sweep($dir) {
		$cutoff = time() - (self::TTL * 4);
		foreach (glob($dir.'/*') as $file) {
			if (is_file($file) && filemtime($file) < $cutoff) {
				@unlink($file);
			}
		}
	}

	private function clearCookie() {
		setcookie(self::COOKIE, '', array(
			'expires' => time() - 3600,
			'path' => '/',
			'secure' => $this->isHttps(),
			'httponly' => true,
			'samesite' => 'Lax',
		));
	}

	private function fileServiceBase() {
		$configured = $this->CI->config->item('ppis_file_service');
		if (!is_string($configured) || $configured === '') {
			return '';
		}
		return rtrim($configured, '/');
	}

	private function sendSuccessHeaders($contentType, $disposition, $download) {
		$contentType = $this->headerValue($contentType);
		if (!preg_match('#^[A-Za-z0-9!#$&^_.+-]+/[A-Za-z0-9!#$&^_.+-]+#', $contentType)) {
			$contentType = 'application/octet-stream';
		}
		$disposition = $this->headerValue($disposition);
		if ($download) {
			if (stripos($disposition, 'attachment') !== 0) {
				$disposition = 'attachment';
			}
		} else {
			$disposition = 'inline';
		}
		http_response_code(200);
		header('Content-Type: '.$contentType);
		header('Content-Disposition: '.$disposition);
		header('X-Content-Type-Options: nosniff');
		header('Cache-Control: private, no-store');
	}

	private function deny($status) {
		$this->clearOutputBuffers();
		$messages = array(
			400 => 'The file request is not valid.',
			401 => 'Sign in again to open this file.',
			403 => 'You are not allowed to open this file.',
			404 => 'The file was not found.',
		);
		$message = isset($messages[$status]) ? $messages[$status] : 'The file could not be retrieved.';
		http_response_code($status);
		header('Content-Type: text/plain; charset=UTF-8');
		header('Cache-Control: no-store');
		header('X-Content-Type-Options: nosniff');
		echo $message;
		exit;
	}

	private function headerValue($value) {
		return str_replace(array("\r", "\n"), '', (string) $value);
	}

	private function clearOutputBuffers() {
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
	}

	private function isHttps() {
		if (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') {
			return true;
		}
		return isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower((string) $_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https';
	}
}
