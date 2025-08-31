<?php 
	
class Cmis_Probationer_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function upsertMasterlist($payload)
	{
		if($payload != null)
		{
			switch ($payload->method) {
				case 'insert':
					$data = array();
					$fields = array('LASTNAME', 'FIRSTNAME', 'MIDDLENAME', 'ALIAS', 'SUPVOFFICE', 'REMARKS', 'SDOCKETNO', 'YEAR', 'REGION', 'STARTMM', 'STARTDD', 'STARTYY', 'ENDMM', 'ENDDD', 'ENDYY', 'STATUS');
					foreach($payload as $key => $value)
					{
						if($value != null && in_array($key, $fields))
						{
							$data = array_merge($data, array($key => $value));
						}
					}
					if (!empty($data)) {
					    $insert = $this->db->insert("masterlist", $data);
					    if ($insert) {
					        // Get the inserted ID
					        $inserted_id = $this->db->insert_id();

					        $response = array(
					            'status' => 'SUCCESS',
					            'message' => 'SUCCESS INSERTING DATA',
					            'inserted_id' => $inserted_id
					        );
					    } else {
					        $response = array(
					            'status' => 'ERROR',
					            'message' => 'ERROR INSERTING DATA!'
					        );
					    }
					} else {
					    $response = array(
					        'status' => 'ERROR',
					        'message' => 'No valid data to insert!'
					    );
					}
					break;
				case 'update':
					$required_param = 1;
					$fields = array('LASTNAME', 'FIRSTNAME', 'MIDDLENAME', 'ALIAS', 'SUPVOFFICE', 'REMARKS', 'SDOCKETNO', 'YEAR', 'REGION', 'STARTMM', 'STARTDD', 'STARTYY', 'ENDMM', 'ENDDD', 'ENDYY', 'STATUS');
					foreach ($payload as $key => $value) {
						if(in_array($key, $fields))
						{
							$this->db->set("".$key."", $value);
						}
					}
					if(isset($payload->id) && $payload->id != null )
					{
						$this->db->where('id', $payload->id);
						$required_param--;
					}
					if($required_param == 0)
					{
						$update = $this->db->update('masterlist');
						if($this->db->affected_rows() > 0)
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'DATA HAS BEEN UPDATED!'
							);
						}
						else
						{
							$response = array(
								'status' => 'ERROR',
								'message' => 'NO DATA HAS BEEN UPDATED!'
							);
						}
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'PLEASE FILL UP ALL THE REQURIED FIELDS'
						);
					}
					break;
				case 'count':
					$fields = array('LASTNAME', 'FIRSTNAME', 'MIDDLENAME', 'ALIAS', 'SUPVOFFICE', 'REMARKS', 'SDOCKETNO', 'YEAR', 'REGION', 'STARTMM', 'STARTDD', 'STARTYY', 'ENDMM', 'ENDDD', 'ENDYY', 'STATUS');
					foreach ($payload as $key => $value) {
						if($value != null && in_array($key, $fields))
						{
							$this->db->where($key, $value);
						}
					}
					$get = $this->db->get('masterlist');
					if($get->num_rows() > 0 )
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'count' => count($get->result())
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'DATA NOT FOUND',
						);
					}
					break;
				case 'fetchAll':
					$fields = array('LASTNAME', 'FIRSTNAME', 'MIDDLENAME', 'ALIAS', 'SUPVOFFICE', 'REMARKS', 'SDOCKETNO', 'YEAR', 'REGION', 'STARTMM', 'STARTDD', 'STARTYY', 'ENDMM', 'ENDDD', 'ENDYY', 'STATUS');
					foreach ($payload as $key => $value) {
						if($value != null && in_array($key, $fields))
						{
							$this->db->where($key, $value);
						}
					}
					$get = $this->db->get('masterlist');
					if($get->num_rows() > 0 )
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'payload' => $get->result()
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'DATA NOT FOUND',
						);
					}
					break;
				case 'fetchByID':
					$required_param = 1;

					if(isset($payload->id) && $payload->id != null )
					{
						$this->db->where('id', $payload->id);
					}
					$get = $this->db->get('masterlist');
					if($get->num_rows() > 0)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS FETCHING DATA',
							'payload' => $get->row()
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'DATA NOT FOUND'
						);
					}
					break;
				default:
					$response = array(
						'status' => 'ERROR',
						'message' => 'METHOD CANNOT BE EMPTY!'
					);
					break;
			}
		}
		else
		{
			$response = array(
				'status' => 'ERROR',
				'message' => 'PLEASE CHECK YOUR DATA'
			);
		}
		return json_encode($response);
	}

	public function upsertMasterlist_request($payload)
	{
		if($payload != null)
		{
			switch ($payload->method) {
				case 'insert':
					// $data = array();
					// $fields = array('FORM_TABLE','LASTNAME', 'FIRSTNAME', 'MIDDLENAME', 'ALIAS', 'SUPVOFFICE', 'REMARKS', 'SDOCKETNO', 'YEAR', 'REGION', 'STARTMM', 'STARTDD', 'STARTYY', 'ENDMM', 'ENDDD', 'ENDYY', 'STATUS');
					$data = array();
					if(isset($payload->FORM_TABLE) && $payload->FORM_TABLE != null)
					{
						$data = array_merge($data, array('FORM_TABLE' => $payload->FORM_TABLE));
					}
					if(isset($payload->LASTNAME) && $payload->LASTNAME != null)
					{
						$data = array_merge($data, array('LASTNAME' => $payload->LASTNAME));
					}
					if(isset($payload->FIRSTNAME) && $payload->FIRSTNAME != null)
					{
						$data = array_merge($data, array('FIRSTNAME' => $payload->FIRSTNAME));
					}
					if(isset($payload->MIDDLENAME) && $payload->MIDDLENAME != null)
					{
						$data = array_merge($data, array('MIDDLENAME' => $payload->MIDDLENAME));
					}
					if(isset($payload->LASTNAME) && $payload->LASTNAME != null)
					{
						$data = array_merge($data, array('ALIAS' => $payload->ALIAS));
					}
					if(isset($payload->ALIAS) && $payload->ALIAS != null)
					{
						$data = array_merge($data, array('LASTNAME' => $payload->LASTNAME));
					}
					if(isset($payload->SUPVOFFICE) && $payload->SUPVOFFICE != null)
					{
						$data = array_merge($data, array('SUPVOFFICE' => $payload->SUPVOFFICE));
					}
					if(isset($payload->REMARKS) && $payload->REMARKS != null)
					{
						$data = array_merge($data, array('REMARKS' => $payload->REMARKS));
					}
					if(isset($payload->SDOCKETNO) && $payload->SDOCKETNO != null)
					{
						$data = array_merge($data, array('SDOCKETNO' => $payload->SDOCKETNO));
					}
					if(isset($payload->YEAR) && $payload->YEAR != null)
					{
						$data = array_merge($data, array('YEAR' => $payload->YEAR));
					}
					if(isset($payload->STARTMM) && $payload->STARTMM != null)
					{
						$data = array_merge($data, array('STARTMM' => $payload->STARTMM));
					}
					if(isset($payload->STARTDD) && $payload->STARTDD != null)
					{
						$data = array_merge($data, array('STARTDD' => $payload->STARTDD));
					}
					if(isset($payload->STARTYY) && $payload->STARTYY != null)
					{
						$data = array_merge($data, array('STARTYY' => $payload->STARTYY));
					}
					if(isset($payload->ENDMM) && $payload->ENDMM != null)
					{
						$data = array_merge($data, array('ENDMM' => $payload->ENDMM));
					}
					if(isset($payload->ENDDD) && $payload->ENDDD != null)
					{
						$data = array_merge($data, array('ENDDD' => $payload->ENDDD));
					}
					if(isset($payload->ENDYY) && $payload->ENDYY != null)
					{
						$data = array_merge($data, array('ENDYY' => $payload->ENDYY));
					}
					if(isset($payload->FIELD_OFFICE) && $payload->FIELD_OFFICE != null)
					{
						$data = array_merge($data, array('FIELD_OFFICE' => $payload->FIELD_OFFICE));
					}
					if(isset($payload->STATUS) && $payload->STATUS != null)
					{
						$data = array_merge($data, array('STATUS' => $payload->STATUS));
					}
					$insert = $this->db->insert("request_masterlist", $data);
					if($insert)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS INSERTING DATA'
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'ERROR INSERTING DATA!'
						);
					}
					break;
				case 'update':
					$required_param = 1;
					$fields = array('LASTNAME', 'FIRSTNAME', 'MIDDLENAME', 'ALIAS', 'SUPVOFFICE', 'REMARKS', 'SDOCKETNO', 'YEAR', 'REGION', 'STARTMM', 'STARTDD', 'STARTYY', 'ENDMM', 'ENDDD', 'ENDYY', 'STATUS', 'REQUEST_STATUS');
					foreach ($payload as $key => $value) {
						if(in_array($key, $fields))
						{
							$this->db->set("".$key."", $value);
						}
					}
					if(isset($payload->id) && $payload->id != null )
					{
						$this->db->where('id', $payload->id);
						$required_param--;
					}
					if($required_param == 0)
					{
						$update = $this->db->update('request_masterlist');
						if($this->db->affected_rows() > 0)
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'DATA HAS BEEN UPDATED!'
							);
						}
						else
						{
							$response = array(
								'status' => 'ERROR',
								'message' => 'NO DATA HAS BEEN UPDATED!'
							);
						}
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'PLEASE FILL UP ALL THE REQURIED FIELDS'
						);
					}
					break;
				case 'count':
					$fields = array('LASTNAME', 'FIRSTNAME', 'MIDDLENAME', 'ALIAS', 'SUPVOFFICE', 'REMARKS', 'SDOCKETNO', 'YEAR', 'REGION', 'STARTMM', 'STARTDD', 'STARTYY', 'ENDMM', 'ENDDD', 'ENDYY', 'STATUS');
					foreach ($payload as $key => $value) {
						if($value != null && in_array($key, $fields))
						{
							$this->db->where($key, $value);
						}
					}
					$get = $this->db->get('request_masterlist');
					if($get->num_rows() > 0 )
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'count' => count($get->result())
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'DATA NOT FOUND',
						);
					}
					break;
				case 'fetchAll':
					$this->db->where('status', 1);
					$this->db->limit(50);
					$get = $this->db->get('request_masterlist');
					if($get->num_rows() > 0 )
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'payload' => $get->result()
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'DATA NOT FOUND',
						);
					}
					break;
				case 'fetchByID':
					$required_param = 1;

					if(isset($payload->id) && $payload->id != null )
					{
						$this->db->where('id', $payload->id);
					}
					$get = $this->db->get('request_masterlist');
					if($get->num_rows() > 0)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS FETCHING DATA',
							'payload' => $get->row()
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'DATA NOT FOUND'
						);
					}
					break;
				case 'fetchByDocket':
					$required_param = 1;

					if(isset($payload->SDOCKETNO) && $payload->SDOCKETNO != null )
					{
						$this->db->where('SDOCKETNO', $payload->SDOCKETNO);
					}
					$get = $this->db->get('request_masterlist');
					if($get->num_rows() > 0)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS FETCHING DATA',
							'payload' => $get->row()
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'DATA NOT FOUND'
						);
					}
					break;
				default:
					$response = array(
						'status' => 'ERROR',
						'message' => 'METHOD CANNOT BE EMPTY!'
					);
					break;
			}
		}
		else
		{
			$response = array(
				'status' => 'ERROR',
				'message' => 'PLEASE CHECK YOUR DATA'
			);
		}
		return json_encode($response);
	}

	public function masterlistRequestSSP()
	{
		$region = strtoupper($_GET['REGION']);
		$firstname = $_GET['FIRSTNAME'];
		$middlename = $_GET['MIDDLENAME'];
		$lastname = $_GET['LASTNAME'];
		$alias = $_GET['ALIAS'];
		$year = $_GET['YEAR'];
		$sdocketno = $_GET['SDOCKETNO'];
		$supervoffice = $_GET['SUPERVOFFICE'];
		$remarks = $_GET['REMARKS'];
		$start_dd = $_GET['START_DD'];
		$start_yy = $_GET['START_YY'];
		$start_mm = $_GET['START_MM'];
		$end_dd =$_GET['END_DD'];
		$end_yy =$_GET['END_YY'];
		$end_mm =$_GET['END_MM'];
		$query = "(SELECT id,FORM_TABLE, FIRSTNAME, MIDDLENAME, LASTNAME, `ALIAS` ,`SUPVOFFICE`, `REMARKS`,`SDOCKETNO`,`YEAR`,`REGION`, CONCAT(STARTYY,'-',STARTMM,'-',STARTDD) AS START_DATE, CONCAT(ENDYY,'-',ENDMM,'-',ENDDD) AS END_DATE, FIELD_OFFICE, STATUS, REQUEST_STATUS  FROM `request_masterlist`  ";
		$text = "WHERE STATUS=1 and 1=1 ";
		if(isset($region) && !empty($region)){
			if($text != ""){
				$text .= "AND REGION = '".utf8_encode($region)."'";
			}else{
				$text .= "REGION = '".utf8_encode($region)."' ";
			}
		}
		if(isset($firstname) && !empty($firstname)){
			if($text != ""){
				$text .= "AND FIRSTNAME LIKE '%".utf8_encode($firstname)."%'";
			}else{
				$text .= "FIRSTNAME LIKE '%".utf8_encode($firstname)."%' ";
			}
		}
		if(isset($lastname) && !empty($lastname)){
			if($text != ""){
				$text .= "AND LASTNAME LIKE '%".utf8_encode($lastname)."%'";
			}else{
				$text .= "LASTNAME LIKE '%".utf8_encode($lastname)."%' ";
			}
		}
		if(isset($middlename) && !empty($middlename)){
			if($text != ""){
				$text .= "AND MIDDLENAME LIKE '%".utf8_encode($middlename)."%'";
			}else{
				$text .= "MIDDLENAME LIKE '%".utf8_encode($middlename)."%' ";
			}
		}
		if(isset($alias) && !empty($alias)){
			if($text != ""){
				$text .= "AND ALIAS LIKE '%".utf8_encode($alias)."%'";
			}else{
				$text .= "ALIAS LIKE '%".utf8_encode($alias)."%' ";
			}
		}
		if(isset($sdocketno) && !empty($sdocketno)){
			if($text != ""){
				$text .= "AND SDOCKETNO LIKE '%".utf8_encode($sdocketno)."%'";
			}else{
				$text .= "SDOCKETNO LIKE '%".utf8_encode($sdocketno)."%' ";
			}
		}
		if(isset($supervoffice) && !empty($supervoffice)){
			if($text != ""){
				$text .= "AND SUPVOFFICE LIKE '%".utf8_encode($supervoffice)."%'";
			}else{
				$text .= "SUPVOFFICE LIKE '%".utf8_encode($supervoffice)."%' ";
			}
		}
		if(isset($remarks) && !empty($remarks)){
			if($text != ""){
				$text .= "AND REMARKS LIKE '%".utf8_encode($remarks)."%'";
			}else{
				$text .= "REMARKS LIKE '%".utf8_encode($remarks)."%' ";
			}
		}
		if(isset($year) && !empty($year)){
			if($text != ""){
				$text .= "AND YEAR LIKE '%".utf8_encode($year)."%'";
			}else{
				$text .= "YEAR LIKE '%".utf8_encode($year)."%' ";
			}
		}
		if(isset($start_mm) && !empty($start_mm)){
			if($text != ""){
				$text .= "AND STARTMM LIKE '%".utf8_encode($start_mm)."%'";
			}else{
				$text .= "STARTMM LIKE '%".utf8_encode($start_mm)."%' ";
			}
		}
		if(isset($start_dd) && !empty($start_dd)){
			if($text != ""){
				$text .= "AND STARTDD LIKE '%".utf8_encode($start_dd)."%'";
			}else{
				$text .= "STARTDD LIKE '%".utf8_encode($start_dd)."%' ";
			}
		}
		if(isset($start_yy) && !empty($start_yy)){
			if($text != ""){
				$text .= "AND STARTYY LIKE '%".utf8_encode($start_yy)."%'";
			}else{
				$text .= "STARTYY LIKE '%".utf8_encode($start_yy)."%' ";
			}
		}
		if(isset($end_mm) && !empty($end_mm)){
			if($text != ""){
				$text .= "AND ENDMM LIKE '%".utf8_encode($end_mm)."%'";
			}else{
				$text .= "ENDMM LIKE '%".utf8_encode($end_mm)."%' ";
			}
		}
		if(isset($end_dd) && !empty($end_dd)){
			if($text != ""){
				$text .= "AND ENDDD LIKE '%".utf8_encode($end_dd)."%'";
			}else{
				$text .= "ENDDD LIKE '%".utf8_encode($end_dd)."%' ";
			}
		}
		if(isset($end_yy) && !empty($end_yy)){
			if($text != ""){
				$text .= "AND ENDYY LIKE '%".utf8_encode($end_yy)."%'";
			}else{
				$text .= "ENDYY LIKE '%".utf8_encode($end_yy)."%' ";
			}
		}
		
		$query .= $text;
		$query .= " ORDER BY id DESC) temp";
		$primaryKey = 'id';
		//echo $query;
		$columns = array(
	        array( 'db' => 'id', 'dt' =>0 ),
	        array( 'db' => 'FORM_TABLE', 'dt' => 1 ),
	        array( 'db' => 'LASTNAME', 'dt' => 2 ),
	        array( 'db' => 'FIRSTNAME', 'dt' => 3 ),
	        array( 'db' => 'MIDDLENAME', 'dt' => 4 ),
	        array( 'db' => 'ALIAS', 'dt' => 5 ),
	        array( 'db' => 'SUPVOFFICE', 'dt' => 6 ),
	        array( 'db' => 'REMARKS', 'dt' => 7 ),
	        array( 'db' => 'SDOCKETNO', 'dt' => 8 ),
	        array( 'db' => 'REGION', 'dt' => 9 ),
	        array( 'db' => 'YEAR', 'dt' => 10 ),
	        array( 'db' => 'START_DATE', 'dt' => 11 ),
	        array( 'db' => 'END_DATE', 'dt' => 12 ),
	        array( 'db' => 'FIELD_OFFICE', 'dt' => 13 ),
	        array(
			    'db' => 'REQUEST_STATUS',
			    'dt' => 14,
			    'formatter' => function( $d, $row ) {
			        if ($row['REQUEST_STATUS'] == 1) {
		                return '<button class="access_ml_write btn btn-xs btn-success btn-migrateRequest" data-id="'.$row['id'].'"
		                data-toggle="modal" 
                        data-target="#modalRequest"
                        > Approve</button>';
		            } elseif ($row['REQUEST_STATUS'] == 2) {
		                return '<span>Approved</span>';
		            } else {
		                return '<span>Rejected</span>'; // Optional: handle other statuses
		            }
			    }
			)
        );
        $this->load->database();
	    $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );

        echo json_encode(
            SSP::simple( $_GET, $sql_details, $query, $primaryKey, $columns)
        );
	}
	public function masterlistSSP()
	{
		$region = strtoupper($_GET['REGION']);
		$firstname = $_GET['FIRSTNAME'];
		$middlename = $_GET['MIDDLENAME'];
		$lastname = $_GET['LASTNAME'];
		$alias = $_GET['ALIAS'];
		$year = $_GET['YEAR'];
		$sdocketno = $_GET['SDOCKETNO'];
		$supervoffice = $_GET['SUPERVOFFICE'];
		$remarks = $_GET['REMARKS'];
		$start_dd = $_GET['START_DD'];
		$start_yy = $_GET['START_YY'];
		$start_mm = $_GET['START_MM'];
		$end_dd =$_GET['END_DD'];
		$end_yy =$_GET['END_YY'];
		$end_mm =$_GET['END_MM'];
		$query = "(SELECT id, FIRSTNAME,MIDDLENAME ,LASTNAME, `ALIAS` ,`SUPVOFFICE`, `REMARKS`,`SDOCKETNO`,`YEAR`,`REGION`, CONCAT(STARTYY,'-',STARTMM,'-',STARTDD) AS START_DATE, CONCAT(ENDYY,'-',ENDMM,'-',ENDDD) AS END_DATE FROM `masterlist`  ";
		$text = "WHERE STATUS=1 and 1=1 ";
		if(isset($region) && !empty($region)){
			if($text != ""){
				$text .= "AND REGION = '".utf8_encode($region)."'";
			}else{
				$text .= "REGION = '".utf8_encode($region)."' ";
			}
		}
		if(isset($firstname) && !empty($firstname)){
			if($text != ""){
				$text .= "AND FIRSTNAME LIKE '%".utf8_encode($firstname)."%'";
			}else{
				$text .= "FIRSTNAME LIKE '%".utf8_encode($firstname)."%' ";
			}
		}
		if(isset($lastname) && !empty($lastname)){
			if($text != ""){
				$text .= "AND LASTNAME LIKE '%".utf8_encode($lastname)."%'";
			}else{
				$text .= "LASTNAME LIKE '%".utf8_encode($lastname)."%' ";
			}
		}
		if(isset($middlename) && !empty($middlename)){
			if($text != ""){
				$text .= "AND MIDDLENAME LIKE '%".utf8_encode($middlename)."%'";
			}else{
				$text .= "MIDDLENAME LIKE '%".utf8_encode($middlename)."%' ";
			}
		}
		if(isset($alias) && !empty($alias)){
			if($text != ""){
				$text .= "AND ALIAS LIKE '%".utf8_encode($alias)."%'";
			}else{
				$text .= "ALIAS LIKE '%".utf8_encode($alias)."%' ";
			}
		}
		if(isset($sdocketno) && !empty($sdocketno)){
			if($text != ""){
				$text .= "AND SDOCKETNO LIKE '%".utf8_encode($sdocketno)."%'";
			}else{
				$text .= "SDOCKETNO LIKE '%".utf8_encode($sdocketno)."%' ";
			}
		}
		if(isset($supervoffice) && !empty($supervoffice)){
			if($text != ""){
				$text .= "AND SUPVOFFICE LIKE '%".utf8_encode($supervoffice)."%'";
			}else{
				$text .= "SUPVOFFICE LIKE '%".utf8_encode($supervoffice)."%' ";
			}
		}
		if(isset($remarks) && !empty($remarks)){
			if($text != ""){
				$text .= "AND REMARKS LIKE '%".utf8_encode($remarks)."%'";
			}else{
				$text .= "REMARKS LIKE '%".utf8_encode($remarks)."%' ";
			}
		}
		if(isset($year) && !empty($year)){
			if($text != ""){
				$text .= "AND YEAR LIKE '%".utf8_encode($year)."%'";
			}else{
				$text .= "YEAR LIKE '%".utf8_encode($year)."%' ";
			}
		}
		if(isset($start_mm) && !empty($start_mm)){
			if($text != ""){
				$text .= "AND STARTMM LIKE '%".utf8_encode($start_mm)."%'";
			}else{
				$text .= "STARTMM LIKE '%".utf8_encode($start_mm)."%' ";
			}
		}
		if(isset($start_dd) && !empty($start_dd)){
			if($text != ""){
				$text .= "AND STARTDD LIKE '%".utf8_encode($start_dd)."%'";
			}else{
				$text .= "STARTDD LIKE '%".utf8_encode($start_dd)."%' ";
			}
		}
		if(isset($start_yy) && !empty($start_yy)){
			if($text != ""){
				$text .= "AND STARTYY LIKE '%".utf8_encode($start_yy)."%'";
			}else{
				$text .= "STARTYY LIKE '%".utf8_encode($start_yy)."%' ";
			}
		}
		if(isset($end_mm) && !empty($end_mm)){
			if($text != ""){
				$text .= "AND ENDMM LIKE '%".utf8_encode($end_mm)."%'";
			}else{
				$text .= "ENDMM LIKE '%".utf8_encode($end_mm)."%' ";
			}
		}
		if(isset($end_dd) && !empty($end_dd)){
			if($text != ""){
				$text .= "AND ENDDD LIKE '%".utf8_encode($end_dd)."%'";
			}else{
				$text .= "ENDDD LIKE '%".utf8_encode($end_dd)."%' ";
			}
		}
		if(isset($end_yy) && !empty($end_yy)){
			if($text != ""){
				$text .= "AND ENDYY LIKE '%".utf8_encode($end_yy)."%'";
			}else{
				$text .= "ENDYY LIKE '%".utf8_encode($end_yy)."%' ";
			}
		}
		
		$query .= $text;
		$query .= " ORDER BY id DESC) temp";
		$primaryKey = 'id';
		//echo $query;
		$columns = array(
	        array( 'db' => 'id', 'dt' =>0 ),
	        array( 'db' => 'FIRSTNAME', 'dt' => 2 ),
	        array( 'db' => 'MIDDLENAME', 'dt' => 3 ),
	        array( 'db' => 'LASTNAME', 'dt' => 1 ),
	        array( 'db' => 'ALIAS', 'dt' => 4 ),
	        array( 'db' => 'SUPVOFFICE', 'dt' => 5 ),
	        array( 'db' => 'REMARKS', 'dt' => 6 ),
	        array( 'db' => 'SDOCKETNO', 'dt' => 7 ),
	        array( 'db' => 'REGION', 'dt' => 8 ),
	        array( 'db' => 'YEAR', 'dt' => 9 ),
	        array( 'db' => 'START_DATE', 'dt' => 10 ),
	        array( 'db' => 'END_DATE', 'dt' => 11 ),
	        array( 'db' => 'id', 'dt' => 12,'formatter' => function( $d, $row ) {
	        	return '<button class="access_ml_write btn btn-xs btn-primary btn-edit" data-id="'.$row['id'].'"><i class="fa fa-pencil"></i> Edit</button>'; 
	        })
	        //array( 'db' => 'YEAR', 'dt' => 6 ),
	       
        );
        $this->load->database();
	    $sql_details = array(
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database,
            'host' => $this->db->hostname
        );

        echo json_encode(
            SSP::simple( $_GET, $sql_details, $query, $primaryKey, $columns)
        );
	}
	public function masterlist_json()
	{
	    $this->load->database();
	    $this->db->select("id, FIRSTNAME, MIDDLENAME, LASTNAME, `ALIAS`, `SUPVOFFICE`, `REMARKS`, `SDOCKETNO`, `YEAR`, `REGION`, 
	        CONCAT(STARTYY,'-',STARTMM,'-',STARTDD) AS START_DATE, 
	        CONCAT(ENDYY,'-',ENDMM,'-',ENDDD) AS END_DATE");
	    $this->db->from('masterlist');
	    $this->db->where('STATUS', 1);

	    // Read JSON input from POST
	    $json = file_get_contents('php://input');
	    $input = json_decode($json, true);

	    // Filter fields
	    $filters = [
	        'REGION' => 'REGION',
	        'FIRSTNAME' => 'FIRSTNAME',
	        'MIDDLENAME' => 'MIDDLENAME',
	        'LASTNAME' => 'LASTNAME',
	        'ALIAS' => 'ALIAS',
	        'SDOCKETNO' => 'SDOCKETNO',
	        'SUPERVOFFICE' => 'SUPVOFFICE',
	        'REMARKS' => 'REMARKS',
	        'YEAR' => 'YEAR',
	        'START_DD' => 'STARTDD',
	        'START_MM' => 'STARTMM',
	        'START_YY' => 'STARTYY',
	        'END_DD' => 'ENDDD',
	        'END_MM' => 'ENDMM',
	        'END_YY' => 'ENDYY'
	    ];

	    foreach ($filters as $key => $column) {
	        if (!empty($input[$key])) {
	            $this->db->like($column, utf8_encode($input[$key]));
	        }
	    }

	    // Apply pagination
	    $limit = isset($input['limit']) ? (int)$input['limit'] : 100; // default 100
	    $offset = isset($input['offset']) ? (int)$input['offset'] : 0;
	    $this->db->limit($limit, $offset);

	    $this->db->order_by('id', 'DESC');
	    $query = $this->db->get();
	    echo json_encode($query->result_array());
	}



}


?>