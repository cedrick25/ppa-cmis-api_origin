<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Session-backed file token and view/download proxy.
 * Upload, list, and delete stay on the file service and send the bearer token from the browser.
 */
class File_proxy extends CI_Controller {

	public function token() {
		$this->load->library('cmis_file_token');
		$token = $this->cmis_file_token->currentToken();
		$this->output->set_header('Cache-Control: no-store');
		$this->output->set_header('X-Content-Type-Options: nosniff');
		$this->output->set_content_type('application/json');
		if ($token === null) {
			$this->output->set_status_header(401);
			$this->output->set_output(json_encode(array('status' => 'FAILED')));
			return;
		}
		$this->output->set_output(json_encode(array('fileToken' => $token)));
	}

	public function end() {
		$this->load->library('cmis_file_token');
		$this->cmis_file_token->destroy();
		$this->output->set_status_header(204);
		$this->output->set_header('Cache-Control: no-store');
	}

	public function view($id = '') {
		$this->load->library('cmis_file_token');
		$this->cmis_file_token->stream($id, false);
	}

	public function download($id = '') {
		$this->load->library('cmis_file_token');
		$this->cmis_file_token->stream($id, true);
	}
}
