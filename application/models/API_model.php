<?php 
	
	class API_model extends CI_Model
	{


		public function __construct() {
	        header('Access-Control-Allow-Origin: *');
	    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
	    	parent::__construct();
		}

		function backup(){
			$fileName='db_backup.sql.zip';
			ini_set('memory_limit', '-1');
			ini_set('max_execution_time', '360');
		    // Load the DB utility class
		    $this->load->dbutil();
			  $prefs = array(
		                      // List of tables to omit from the backup
		        'format'        => 'txt',                       // gzip, zip, txt
		        'filename'      => 'db_backup.sql',              // File name - NEEDED ONLY WITH ZIP FILES
		        'add_drop'      => TRUE,                        // Whether to add DROP TABLE statements to backup file
		        'add_insert'    => TRUE,                        // Whether to add INSERT data to backup file
		        'newline'       => "\n"                         // Newline character used in backup file
		);
		    // Backup your entire database and assign it to a variable
		    $backup =& $this->dbutil->backup();
		   
		    // Load the file helper and write the file to your server
		    $this->load->helper('file');
		    write_file(FCPATH.'/downloads/'.$fileName, $backup);

		    // Load the download helper and send the file to your desktop
		    $this->load->helper('download');
		    force_download($fileName, $backup);
		}

		function full_restore(){

			#$fileName='db_backup.zip';
			ini_set('memory_limit', '-1');
		    // Load the DB utility class
		    $sql_contents = file_get_contents($_FILES['fileToUpload']['tmp_name']);
		    #var_dump($_FILES);
		    echo "LIST OF QUERIES BEING EXECUTED";
		    $sql_contents = explode(";", $sql_contents);

		    foreach($sql_contents as $query)
		    {

		        $pos = strpos($query,'ci_sessions');
		        var_dump($pos);
		        echo $query;
		        if($pos == false)
		        {
		            #$result = $this->db->query($query);
		        }
		        else
		        {
		            continue;
		        }

		    }
		}

		function generateSQL($table_name,$csv_data){
			$table_name = $table_name;
            $csv_data   = $csv_data;
            $csv_array    = explode("\n",$csv_data);
            $column_names = explode(",",$csv_array[0]);
 
            // Generate base query
            $base_query = "INSERT INTO `$table_name` (";
            $first      = true;
            foreach($column_names as $column_name)  
            {
                if(!$first)
                    $base_query .= ", ";    
                $column_name = trim($column_name);
                $base_query .= "`$column_name`";
                $first = false;
            }
            $base_query .= ") ";

            $last_data_row = count($csv_array) - 1;
            for($counter = 1; $counter < $last_data_row; $counter++)
            {
                $value_query = "VALUES (";
                $first = true;
                $data_row = explode(",",$csv_array[$counter]);
                $value_counter = 0;
                foreach($data_row as $data_value)   
                {
                    if(!$first)
                        $value_query .= ", ";   
                    $data_value = trim($data_value);
                    $value_query .= "'$data_value'";
                    $first = false;
                }
                $value_query .= ")";
        
                // Combine generated queries to generate final query
                $query = $base_query .$value_query .";";
            	return $query;
                
            }
		}

		function backupDate(){
			$start = $_POST['start'];
			$end = $_POST['end'];

			$fileName=$start."_".$end."_backup.sql";
			#echo $fileName;
			$this->load->dbutil();
			$this->load->library('zip');
			
			$query = $this->db->query("SELECT * FROM F5T1 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);
			#echo $result;
			$q = $this->generateSQL("F5T1",$result);
			#echo $q;

			$this->zip->add_data("F5T1.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T2_ACTED WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);

			$q =  $q ."\n". $this->generateSQL("F5T2_ACTED",$result);
			echo $q;
			#echo $result;
			$this->zip->add_data("F5T2_ACTED.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T2_NOTACTED WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");

			
			$result =  $this->dbutil->csv_from_result($query);

			$q =  $q ."\n". $this->generateSQL("F5T2_ACTED",$result);

			
			#echo $result;
			$this->zip->add_data("F5T2_NOTACTED.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T2_RCV WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);

			$q =  $q ."\n". $this->generateSQL("F5T2_NOTACTED",$result);

			#echo $result;
			$this->zip->add_data("F5T2_RCV.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T3 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);
			#echo $result;
			$q =  $q ."\n". $this->generateSQL("F5T2_RCV",$result);

			$this->zip->add_data("F5T3.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T4 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);

			$q =  $q ."\n". $this->generateSQL("F5T4",$result);
			#echo $result;
			$this->zip->add_data("F5T4.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T5 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);
			#echo $result;

			$q =  $q ."\n". $this->generateSQL("F5T5",$result);
			$this->zip->add_data("F5T5.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T6_CMPLTD WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);

			$q =  $q ."\n". $this->generateSQL("F5T6_CMPLTD",$result);
			#echo $result;
			$this->zip->add_data("F5T6_CMPLTD.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T7 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);
			$q =  $q ."\n". $this->generateSQL("F5T7",$result);
			#echo $result;
			$this->zip->add_data("F5T7.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T8 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);
			$q =  $q ."\n". $this->generateSQL("F5T8",$result);
			#echo $result;
			$this->zip->add_data("F5T8.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T9 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);
			$q =  $q ."\n". $this->generateSQL("F5T9",$result);
			#echo $result;
			$this->zip->add_data("F5T9.csv", $result);

			$query = $this->db->query("SELECT * FROM F5T10 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);
			#echo $result;
			$this->zip->add_data("F5T10.csv", $result);
			$q =  $q ."\n". $this->generateSQL("F5T10",$result);

			$query = $this->db->query("SELECT * FROM F5T11 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);
			#echo $result;
			$this->zip->add_data("F5T11.csv", $result);
			$q =  $q ."\n". $this->generateSQL("F5T11",$result);
			$query = $this->db->query("SELECT * FROM F5T12 WHERE Y_M  >= '".$start."' and Y_M <= '".$end."'");
			
			$result =  $this->dbutil->csv_from_result($query);
			$this->zip->add_data("F5T12.csv", $result);
			$q =  $q ."\n". $this->generateSQL("F5T12",$result);
			#echo $result;
			#$this->zip->download("backup.zip");


			$this->load->helper('download');
			force_download($fileName, $q);
			/*ini_set('memory_limit', '-1');
		    // Load the DB utility class
		    $this->load->dbutil();

		    // Backup your entire database and assign it to a variable
		    $backup =& $this->dbutil->backup();

		    // Load the file helper and write the file to your server
		    $this->load->helper('file');
		    write_file(FCPATH.'/downloads/'.$fileName, $backup);

		    // Load the download helper and send the file to your desktop
		    $this->load->helper('download');
		    force_download($fileName, $backup);*/
		}
	

		public function getDatetime($payload){
			header('Content-Type: application/json');
			if(isset($payload->basehourly)){
				$d = date("Y-m-d H:");
				#$d = substr($d,0,-1);
				$d .= "00";
				$data = array(
							"date" => $d
							);
			}else if(isset($payload->nexthourly)){
				$d = date("Y-m-d H", strtotime("+1 hours"));

				$d .= ":00";
				$data = array(
							"date" => $d
							);
			}else if(isset($payload->nexthour)){
				$d = date("H", strtotime("+1 hours"));

				$d .= ":00";
				$data = array(
							"date" => $d
							);
			}else{
				$d = date("Y-m-d H:i", strtotime('-4 minutes'));
				$d = substr($d,0,-1);
				$d .= "0";
				$data = array(
							"date" => $d
							);
			}
			
			
			$response = array('status' => 'SUCCESS',
				 'message' => 'Retrieving Station Success', 
				 'payload' =>	$data,
				 'request' => $payload);
			return json_encode($response);
	
			
		}

		
		



		public function authenticate($payload){
			header('Content-Type: application/json');

			header('Access-Control-Allow-Origin: *');
	    	header("Access-Control-Allow-Methods: GET, POST");

	    	$referer = "";
	    	if(isset($_SERVER['HTTP_REFERER'])){
	    		$referer = parse_url($_SERVER['HTTP_REFERER']);
				$referer = $referer['host'];	
	    	}
	    	
	    	if($referer !== 'eppcmis.probation.gov.ph' && $referer !== '192.168.1.36'  && $referer !== '192.168.1.33'  && $referer !== '192.168.1.38'  && $referer !== '192.168.1.35' && $referer!=='192.168.100.3' && $referer!=='192.168.254.167' && $referer!=='192.168.1.112' && $referer!=='192.168.100.122' && $referer!=='192.168.1.109' && $referer!=='192.168.1.73' && $referer!=='192.168.1.184' && $referer!=='192.168.1.191' && $referer!=='192.168.100.14' && $referer!=='192.168.1.155' && $referer!=='192.168.1.108' && $referer!=='192.168.1.105' && $referer !== '192.168.100.4' && $referer !== '192.168.254.115' && $referer !== '192.168.1.224' && $referer !== '127.0.0.1' && $referer !== 'ks' && $referer !== '202.90.136.122'&& $referer !== '127.0.0.1'){
			    die('Unauthorized access');
			}
	    	

			if(!isset($payload->USERNAME) && !isset($payload->PASSWORD)){
				$response = array('status' => 'FAILED',
									  'message' => 'INVALID PARAMATERS');
					return json_encode($response);
			}

			$this->db->where(array('USER_NAME'=>$payload->USERNAME,'USER_PASS'=>md5($payload->PASSWORD) ));
		    $query = $this->db->get('USERS');

			if($query){
				if($query->num_rows() > 0){
					$user = $query->row();
						$response = array('status' => 'SUCCESS',
										 'message' => 'LOGIN SUCCESS',
										 'payload' => array("USER_ID"=>$user->USER_ID,
										 					"USER_LEVEL_ID" => $user->USER_LEVEL_ID,
										 					"USER_STATUS" => $user->USER_STATUS,
										 					"USER_NAME" => $user->USER_NAME,
										 					"USER_EMAIL" => $user->USER_EMAIL,
										 					"USER_FULLNAME" => $user->USER_FULLNAME,
										 					"USER_CONTACT" => $user->USER_CONTACT,
										 					"FIELD_OFFICE" => $user->FIELD_OFFICE)
										 		
										 );

						$p2 = (object)array( "created_by" => $user->USER_ID,
											"action" => "LOGGED IN",
											"module" => "AUTHENTICATION" );
						$this->Cmis_Feedback_model->AuditInsert($p2);

						$p3 = (object)array( "USER_ID" => $user->USER_ID,
											"USER_SESSION" => "1",
											);
						$this->UpdateUser($p3);


						return json_encode($response);
				}else{
						$response = array('status' => 'FAILED',
										  'message' => 'USERNAME or PASSWORD Didn\'t Match!');
						return json_encode($response);
				}

				
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR LOGGING-IN');
					return json_encode($response);
			}	
		}



		public function authenticateSSO($payload){
			header('Content-Type: application/json');

			header('Access-Control-Allow-Origin: *');
	    	header("Access-Control-Allow-Methods: GET, POST");

	    	$referer = "";
	    	if(isset($_SERVER['HTTP_REFERER'])){
	    		$referer = parse_url($_SERVER['HTTP_REFERER']);
				$referer = $referer['host'];	
	    	}
	    	
	    	if($referer !== 'eppcmis.probation.gov.ph' && $referer !== '192.168.1.36'  && $referer !== '192.168.1.33'  && $referer !== '192.168.1.38'  && $referer !== '192.168.1.35' && $referer !== '192.168.100.3' && $referer!=='192.168.254.167' && $referer!=='192.168.1.112' && $referer!=='192.168.100.122' && $referer!=='192.168.1.109' && $referer!=='192.168.1.73' && $referer!=='192.168.1.33' && $referer!=='192.168.1.73' && $referer!=='192.168.1.184' && $referer!=='192.168.1.191' && $referer!=='192.168.100.14' && $referer!=='192.168.1.155' && $referer !== '192.168.254.115' && $referer !== '192.168.1.224' && $referer !== '127.0.0.1' && $referer !== 'ks' && $referer !== '202.90.136.122'){
			    die('Unauthorized access');
			}
	    	

			if(!isset($payload->key) ){
				$response = array('status' => 'FAILED',
									  'message' => 'INVALID PARAMATERS');
					return json_encode($response);
			}

			$key = $payload->key;
			$key_raw =explode(".", $key);
			$USER_EMAIL = base64_decode($key_raw[0]);
			$USER_PASS = base64_decode($key_raw[1]);

			// $this->db->where(array('USER_EMAIL'=>$USER_EMAIL,'USER_PASS'=>md5($USER_PASS) ));
			$this->db->where(array('USER_EMAIL'=>$USER_EMAIL));
		    $query = $this->db->get('USERS');

			if($query){
				if($query->num_rows() > 0){
					$user = $query->row();
						$response = array('status' => 'SUCCESS',
										 'message' => 'LOGIN SUCCESS',
										 'payload' => array("USER_ID"=>$user->USER_ID,
										 					"USER_LEVEL_ID" => $user->USER_LEVEL_ID,
										 					"USER_STATUS" => $user->USER_STATUS,
										 					"USER_NAME" => $user->USER_NAME,
										 					"USER_FULLNAME" => $user->USER_FULLNAME,
										 					"FIELD_OFFICE" => $user->FIELD_OFFICE)
										 		
										 );

						$p2 = (object)array( "created_by" => $user->USER_ID,
											"action" => "LOGGED IN",
											"module" => "AUTHENTICATION" );
						$this->Cmis_Feedback_model->AuditInsert($p2);
						

						$p3 = (object)array( "USER_ID" => $user->USER_ID,
											"USER_SESSION" => "1",
											);
						$this->UpdateUser($p3);

						return json_encode($response);
				}else{
						$response = array('status' => 'FAILED',
										  'message' => 'USERNAME or PASSWORD Didn\'t Match!');
						return json_encode($response);
				}

				
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR LOGGING-IN');
					return json_encode($response);
			}	
		}



		public function getAllUserType($payload){
			header('Content-Type: application/json');

		    $query = $this->db->query("CALL getAllUserType(1)");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving User Tyle List Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving User Tyle List');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}

		public function getDeletedList($payload){
			header('Content-Type: application/json');
			$table = $payload->table;
		    $query = $this->db->query("SELECT * FROM ".$table." WHERE status = 0");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving getDeletedList Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving getDeletedList List');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}
		public function getAllForms($payload){
			header('Content-Type: application/json');
		    $query = $this->db->query("SELECT * FROM FORMS");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving getAllForms Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving getAllForms List');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}

		public function getFormByPage($payload){
			header('Content-Type: application/json');
			$form_page = "";
			if(isset($payload->form_page)){
				$form_page = $payload->form_page;
			}
			#echo "SELECT * FROM USER_LEVEL WHERE USER_LEVEL_ID = '".$USER_ID."'";
		    $query = $this->db->query("SELECT * FROM FORMS WHERE form_page = '".$form_page."'");
		    #echo $query;
			if($query){
				if($query->num_rows() > 0){
					$data = $query->row();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving User Forms Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving User Forms');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}


		public function UpdateForm($payload){
			header('Content-Type: application/json');
			
			$update = array();
			if(isset($payload->form_CAPTION) && ($payload->form_CAPTION != "")){
				$update = array_merge($update,  array("form_CAPTION"=>$payload->form_CAPTION));
			}

			$this->db->reconnect();
			$this->db->where('form_ID',$payload->form_ID);
			if($this->db->update('FORMS', $update)){
				$response = array('status' => 'SUCCESS',
							 'message' => 'SUCCESSFULLY UPDATING FORMS'
							 );
				return json_encode($response);
			}else{
				$response = array('status' => 'ERROR',
							  'message' => 'FAILED FORMS STATION');
							  #'error_code' => mysqli_error($this->con));
				return json_encode($response);
			}
		}


		public function restoreDeleted($payload){
			header('Content-Type: application/json');
			$table = $payload->table;
		    $query = $this->db->query("UPDATE ".$table." SET status = 1 WHERE id = '".$payload->id."'");

			if($query){
				
				$response = array('status' => 'SUCCESS',
					 'message' => 'UPDATE Success');
					
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}

		public function getAllUserTypes($payload){
			header('Content-Type: application/json');

		    $query = $this->db->query("SELECT * FROM USER_LEVEL");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving User Tyle List Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving User Tyle List');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}

		public function getAllUserTypeModules($payload){
			header('Content-Type: application/json');

		    $query = $this->db->query("Select * from USER_LEVEL_MODULES");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving User Tyle List Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving User Tyle List');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}

		public function getAllUserList($payload){
			header('Content-Type: application/json');
			$USER_ID = "";
			if(isset($payload->USER_ID)){
				$USER_ID = $payload->USER_ID;
			}
		    $query = $this->db->query("CALL getAllUserList('".$USER_ID."')");

			if($query){
				if($query->num_rows() > 1){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving User List Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else if($query->num_rows() > 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving User List Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving User List');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}

		public function getAuditTrail($payload){
			header('Content-Type: application/json');
			$USER_ID = "";
		    $query = $this->db->query("CALL getAuditTrail()");

			if($query){
				if($query->num_rows() >= 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving User List Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving User List');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}

		public function getUserByID($payload){
			header('Content-Type: application/json');
			$USER_ID = "";
			if(isset($payload->USER_ID)){
				$USER_ID = $payload->USER_ID;
			}
		    $query = $this->db->query("CALL getAllUserList('".$USER_ID."')");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->row();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving User List Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving User List');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}


		public function getUserTypeByID($payload){
			header('Content-Type: application/json');
			$USER_ID = "";
			if(isset($payload->USER_ID)){
				$USER_ID = $payload->USER_ID;
			}
			#echo "SELECT * FROM USER_LEVEL WHERE USER_LEVEL_ID = '".$USER_ID."'";
		    $query = $this->db->query("SELECT * FROM USER_LEVEL WHERE USER_LEVEL_ID = '".$USER_ID."'");
		    #echo $query;
			if($query){
				if($query->num_rows() > 0){
					$data = $query->row();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving User Level Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving User Level');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}

		public function getUserTypeByModulesByID($payload){
			header('Content-Type: application/json');
			$LEVEL_ID = "";
			if(isset($payload->LEVEL_ID)){
				$LEVEL_ID = $payload->LEVEL_ID;
			}
		    $query = $this->db->query("SELECT * FROM USER_LEVEL_RIGHTS WHERE USER_LEVEL_ID = '".$LEVEL_ID."'");
		    #echo $query;
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => 'Retrieving getUserTypeByModulesByID Success', 
							 'payload' =>	$data);
					return json_encode($response);
				}else{
						$response = array('status' => 'ERROR',
							  'message' => 'Fail Retrieving User Level');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}


		public function checkUserEmailExist($payload){
			header('Content-Type: application/json');
			
		    $query = $this->db->query("SELECT * FROM USERS WHERE USER_EMAIL = '".$payload->USER_EMAIL."'");
		    #echo $query;
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => ' Success');
					return json_encode($response);
				}else{
						$response = array('status' => 'FAILED',
							  'message' => 'Fail');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}

	

		public function AddUser($payload){
			header('Content-Type: application/json');

			$query = $this->db->query("SELECT * FROM USERS WHERE USER_EMAIL = '".$payload->USER_EMAIL."'");
		    #echo $query;
			if($query){
				if($query->num_rows() > 0){
						$response = array('status' => 'SUCCESS',
					 	'message' => ' Email exist');
					return json_encode($response);
				}else{
					$datenow = date("Y-m-d H:i:s");
					$data = array(	"USER_FULLNAME" => $payload->USER_FULLNAME,
									// "USER_FNAME" => $payload->USER_FNAME,
									
									// "USER_LNAME" => $payload->USER_LNAME,
									"USER_NAME" => $payload->USER_NAME,
									"USER_CONTACT" => $payload->USER_CONTACT,
									"USER_EMAIL" => $payload->USER_EMAIL,
									"USER_PASS" =>	md5($payload->USER_PASS),
									"USER_LEVEL_ID" => $payload->USER_LEVEL_ID,
									"FIELD_OFFICE" => $payload->FIELD_OFFICE,
									"USER_EXPIRY" => $payload->USER_EXPIRY,
									"CREATED_DATE" => $datenow,
									"CREATED_BY" => $payload->CREATED_BY,
									"USER_STATUS" => $payload->STATUS
									);

					if(isset($payload->USER_FNAME) && ($payload->USER_FNAME != "")){
						$data = array_merge($data,  array("USER_FNAME"=>$payload->USER_FNAME));
					}
					if(isset($payload->USER_MNAME) && ($payload->USER_MNAME != "")){
						$data = array_merge($data,  array("USER_MNAME"=>$payload->USER_MNAME));
					}
					if(isset($payload->USER_LNAME) && ($payload->USER_LNAME != "")){
						$data = array_merge($data,  array("USER_LNAME"=>$payload->USER_LNAME));
					}

					if($this->db->insert('USERS', $data)){
						$response = array('status' => 'SUCCESS',
										'message' => 'USER ADDED SUCCESSFULLY',
										 'USER_ID' =>  $this->db->insert_id()
										 );
						return json_encode($response);
					}else{
						$response = array('status' => 'ERROR',
										  'message' => 'FAILED ADDING USER');
						return json_encode($response);
					}
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
			
		}

		public function AddUserType($payload){
			header('Content-Type: application/json');

			$datenow = date("Y-m-d H:i:s");
			$data = array(	"USER_LEVEL_NAME" => $payload->USER_LEVEL_NAME,
							"STATUS" => $payload->USER_STATUS
							);
			

			if($this->db->insert('USER_LEVEL', $data)){
				$response = array('status' => 'SUCCESS',
								'message' => 'USER ADDED SUCCESSFULLY',
								'USER_ID' =>  $this->db->insert_id()
								 );
				$LEVEL_ID = $this->db->insert_id();
				$this->db->reconnect();
				$insert = array();
				foreach ($payload->checkbox as $key => $value) {
					#var_dump($value);

					 array_push($insert,  array("USER_LEVEL_ID"=>$LEVEL_ID,"ACCESS_RIGHTS"=>$value->STATUS,"USER_LEVEL_MODULE_ID"=>$value->USER_LEVEL_MODULE_ID));
					#$insert = array_merge($insert,  ));
				}
				$this->db->insert_batch('USER_LEVEL_RIGHTS', $insert);

				#var_dump($insert);


				return json_encode($response);
			}else{
				$response = array('status' => 'ERROR',
								  'message' => 'FAILED ADDING USER');
				return json_encode($response);
			}
		}

		public function UpdateUserType($payload){
			header('Content-Type: application/json');
			
			$update = array();
			if(isset($payload->STATUS) && ($payload->STATUS != "")){
				$update = array_merge($update,  array("STATUS"=>$payload->STATUS));
			}
			if(isset($payload->USER_LEVEL_NAME) && ($payload->USER_LEVEL_NAME != "")){
				$update = array_merge($update,  array("USER_LEVEL_NAME"=>$payload->USER_LEVEL_NAME));
			}

			
			$this->db->reconnect();
			$this->db->where('USER_LEVEL_ID',$payload->USER_LEVEL_ID);
			if($this->db->update('USER_LEVEL', $update)){
				$response = array('status' => 'SUCCESS',
							 'message' => 'SUCCESSFULLY UPDATING USER'
							 );

				$this->db->reconnect();
				$this->db->query("DELETE FROM USER_LEVEL_RIGHTS WHERE USER_LEVEL_ID='".$payload->USER_LEVEL_ID."'");
				$this->db->reconnect();
				$insert = array();
				foreach ($payload->checkbox as $key => $value) {
					#var_dump($value);

					 array_push($insert,  array("USER_LEVEL_ID"=>$payload->USER_LEVEL_ID,"ACCESS_RIGHTS"=>$value->STATUS,"USER_LEVEL_MODULE_ID"=>$value->USER_LEVEL_MODULE_ID));
					#$insert = array_merge($insert,  ));
				}
				$this->db->insert_batch('USER_LEVEL_RIGHTS', $insert);



				return json_encode($response);
			}else{
				$response = array('status' => 'ERROR',
							  'message' => 'FAILED USER STATION');
							  #'error_code' => mysqli_error($this->con));
				return json_encode($response);
			}
		}

		public function UpdateUserByEmail($payload){
			header('Content-Type: application/json');
			
			$update = array();
			if(isset($payload->USER_STATUS) && ($payload->USER_STATUS != "")){
				$update = array_merge($update,  array("USER_STATUS"=>$payload->USER_STATUS));
			}
			if(isset($payload->USER_FULLNAME) && ($payload->USER_FULLNAME != "")){
				$update = array_merge($update,  array("USER_FULLNAME"=>$payload->USER_FULLNAME));
			}
			if(isset($payload->USER_FNAME) && ($payload->USER_FNAME != "")){
				$update = array_merge($update,  array("USER_FNAME"=>$payload->USER_FNAME));
			}
			if(isset($payload->USER_MNAME) && ($payload->USER_MNAME != "")){
				$update = array_merge($update,  array("USER_MNAME"=>$payload->USER_MNAME));
			}
			if(isset($payload->USER_LNAME) && ($payload->USER_LNAME != "")){
				$update = array_merge($update,  array("USER_LNAME"=>$payload->USER_LNAME));
			}

			if(isset($payload->USER_CONTACT) && ($payload->USER_CONTACT != "")){
				$update = array_merge($update,  array("USER_CONTACT"=>$payload->USER_CONTACT));
			}


			if(isset($payload->USER_PASS) && ($payload->USER_PASS != "")){
				$update = array_merge($update,  array("USER_PASS"=>md5($payload->USER_PASS)));
			}

			if(isset($payload->USER_LEVEL_ID) && ($payload->USER_LEVEL_ID != "")){
				$update = array_merge($update,  array("USER_LEVEL_ID"=>$payload->USER_LEVEL_ID));
			}

			if(isset($payload->FIELD_OFFICE) && ($payload->FIELD_OFFICE != "")){
				$update = array_merge($update,  array("FIELD_OFFICE"=>$payload->FIELD_OFFICE));
			}

			$this->db->reconnect();
			$this->db->where('USER_EMAIL',$payload->USER_EMAIL);
			if($this->db->update('USERS', $update)){
				$response = array('status' => 'SUCCESS',
							 'message' => 'SUCCESSFULLY UPDATING USER'
							 );
				return json_encode($response);
			}else{
				$response = array('status' => 'ERROR',
							  'message' => 'FAILED USER STATION');
							  #'error_code' => mysqli_error($this->con));
				return json_encode($response);
			}
		}

		public function UpdateUser($payload){
			header('Content-Type: application/json');
			
			$update = array();
			if(isset($payload->USER_STATUS) && ($payload->USER_STATUS != "")){
				$update = array_merge($update,  array("USER_STATUS"=>$payload->USER_STATUS));
			}
			if(isset($payload->USER_ID) && ($payload->USER_ID != "")){
				$update = array_merge($update,  array("USER_ID"=>$payload->USER_ID));
			}

			if(isset($payload->USER_NAME) && ($payload->USER_NAME != "")){
				$update = array_merge($update,  array("USER_NAME"=>$payload->USER_NAME));
			}

			if(isset($payload->USER_FULLNAME) && ($payload->USER_FULLNAME != "")){
				$update = array_merge($update,  array("USER_FULLNAME"=>$payload->USER_FULLNAME));
			}

			if(isset($payload->USER_CONTACT) && ($payload->USER_CONTACT != "")){
				$update = array_merge($update,  array("USER_CONTACT"=>$payload->USER_CONTACT));
			}

			if(isset($payload->USER_EMAIL) && ($payload->USER_EMAIL != "")){
				$update = array_merge($update,  array("USER_EMAIL"=>$payload->USER_EMAIL));
			}

			if(isset($payload->USER_PASS) && ($payload->USER_PASS != "")){
				$update = array_merge($update,  array("USER_PASS"=>md5($payload->USER_PASS)));
			}

			if(isset($payload->USER_LEVEL_ID) && ($payload->USER_LEVEL_ID != "")){
				$update = array_merge($update,  array("USER_LEVEL_ID"=>$payload->USER_LEVEL_ID));
			}

			if(isset($payload->FIELD_OFFICE) && ($payload->FIELD_OFFICE != "")){
				$update = array_merge($update,  array("FIELD_OFFICE"=>$payload->FIELD_OFFICE));
			}
			if(isset($payload->USER_SESSION) && ($payload->USER_SESSION != "")){
				$update = array_merge($update,  array("USER_SESSION"=>$payload->USER_SESSION));
			}

			$this->db->reconnect();
			$this->db->where('USER_ID',$payload->USER_ID);
			if($this->db->update('USERS', $update)){
				$response = array('status' => 'SUCCESS',
							 'message' => 'SUCCESSFULLY UPDATING USER'
							 );
				return json_encode($response);
			}else{
				$response = array('status' => 'ERROR',
							  'message' => 'FAILED USER STATION');
							  #'error_code' => mysqli_error($this->con));
				return json_encode($response);
			}
		}


		public function doLogout($payload){
			header('Content-Type: application/json');
			
			$update = array();
			
			$update = array_merge($update,  array("USER_SESSION"=>"0"));
			
			$this->db->reconnect();
			$this->db->where('USER_EMAIL',$payload->USER_EMAIL);
			if($this->db->update('USERS', $update)){
				$response = array('status' => 'SUCCESS',
							 'message' => 'SUCCESSFULLY UPDATING USER'
							 );
				return json_encode($response);
			}else{
				$response = array('status' => 'ERROR',
							  'message' => 'FAILED USER STATION');
							  #'error_code' => mysqli_error($this->con));
				return json_encode($response);
			}
		}
		public function isActive($payload){
			$query = $this->db->query("SELECT * FROM USERS WHERE USER_ID = '".$payload->USER_ID."' and USER_SESSION='1'");
		    #echo $query;
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result();
						$response = array('status' => 'SUCCESS',
							 'message' => ' Success');
					return json_encode($response);
				}else{
						$response = array('status' => 'FAILED',
							  'message' => 'Fail');
					return json_encode($response);
			
				}
			}else{
					$response = array('status' => 'ERROR',
									  'message' => 'ERROR FETCHING RECORDS');
					return json_encode($response);
			}
		}


		public function caseload_reports($payload)
		{
			if($payload != null)
			{
				switch ($payload->method) {
					case 'view_caseload':
						$query = $this->db->query("SELECT * FROM caseload_reports WHERE created_by = '".$payload->user_id."'");
						if($query){
							if($query->num_rows() > 0){
								$data = $query->result();
									$response = array('status' => 'SUCCESS',
										 'message' => 'Retrieving view_caseload Success', 
										 'payload' =>	$data);
								return json_encode($response);
							}else{
									$response = array('status' => 'ERROR',
										  'message' => 'Fail Retrieving view_caseload Level');
								return json_encode($response);
						
							}
						}else{
								$response = array('status' => 'ERROR',
												  'message' => 'ERROR FETCHING RECORDS');
								return json_encode($response);
						}
						break;
					case 'fwd_caseload':
						$query = $this->db->query("SELECT cr.*, crf.*, u.USER_FULLNAME, u.FIELD_OFFICE FROM caseload_report_forwarding crf, caseload_reports cr, USERS u

							WHERE crf.forwarded_by = '".$payload->user_id."' and crf.report_id = cr.report_id and crf.forwarded_to = u.USER_ID");
						if($query){
							if($query->num_rows() > 0){
								$data = $query->result();
									$response = array('status' => 'SUCCESS',
										 'message' => 'Retrieving view_caseload Success', 
										 'payload' =>	$data);
								return json_encode($response);
							}else{
									$response = array('status' => 'ERROR',
										  'message' => 'Fail Retrieving view_caseload Level');
								return json_encode($response);
						
							}
						}else{
								$response = array('status' => 'ERROR',
												  'message' => 'ERROR FETCHING RECORDS');
								return json_encode($response);
						}
						break;
					case 'rcv_caseload':
						$query = $this->db->query("SELECT cr.*, crf.*, u.USER_FULLNAME, u.FIELD_OFFICE  FROM caseload_report_forwarding crf, caseload_reports cr, USERS u
							WHERE crf.forwarded_to = '".$payload->user_id."' and crf.report_id = cr.report_id   and crf.forwarded_by = u.USER_ID");
						if($query){
							if($query->num_rows() > 0){
								$data = $query->result();
									$response = array('status' => 'SUCCESS',
										 'message' => 'Retrieving view_caseload Success', 
										 'payload' =>	$data);
								return json_encode($response);
							}else{
									$response = array('status' => 'ERROR',
										  'message' => 'Fail Retrieving view_caseload Level');
								return json_encode($response);
						
							}
						}else{
								$response = array('status' => 'ERROR',
												  'message' => 'ERROR FETCHING RECORDS');
								return json_encode($response);
						}
						break;
					case 'fwd':
							$datenow = date("Y-m-d H:i:s");
							$data = array(	"report_id" => $payload->report_id,
											"forwarded_by" => $payload->forwarded_by,
											"forwarded_to" => $payload->forwarded_to,
											"forwarded_date" => $datenow,
											"status" => '1'
											);
							

							if($this->db->insert('caseload_report_forwarding', $data)){
								$response = array('status' => 'SUCCESS',
												'message' => 'REPORTS ADDED SUCCESSFULLY',
												'USER_ID' =>  $this->db->insert_id()
												 );
							}else{
								$response = array('status' => 'ERROR',
												'message' => 'REPORTS ADDED FAILED',
												 );
							}
						break;
					case 'insert':
							$datenow = date("Y-m-d H:i:s");
							$data = array(	"report_name" => $payload->report_name,
											"report_YM" => $payload->report_YM,
											"report_field" => $payload->report_field,
											"report_content" => $payload->report_content,
											"report_status" => $payload->report_status,
											"created_by" => $payload->created_by,
											"created_date" => $datenow
											);
							

							if($this->db->insert('caseload_reports', $data)){
								$response = array('status' => 'SUCCESS',
												'message' => 'REPORTS ADDED SUCCESSFULLY',
												'USER_ID' =>  $this->db->insert_id()
												 );
							}else{
								$response = array('status' => 'ERROR',
												'message' => 'REPORTS ADDED FAILED',
												 );
							}
						break;
					case 'update_caseload':
						break;
					case 'view_single': 
						$query = $this->db->query("SELECT * FROM caseload_reports WHERE report_id = '".$payload->id."'");
							if($query){
								if($query->num_rows() > 0){
									$data = $query->row();
										$response = array('status' => 'SUCCESS',
											 'message' => 'Retrieving view_caseload Success', 
											 'payload' =>	$data);
									return json_encode($response);
								}else{
										$response = array('status' => 'ERROR',
											  'message' => 'Fail Retrieving view_caseload Level');
									return json_encode($response);
							
								}
							}else{
									$response = array('status' => 'ERROR',
													  'message' => 'ERROR FETCHING RECORDS');
									return json_encode($response);
							}
						break;
				}
			}else
			{
				$response = array(
					'status' => 'ERROR',
					'message' => 'PLEASE CHECK YOUR DATA!'
				);
			}
			return json_encode($response);
		}

		public function migrate_f21($payload)
		{
			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");

			//@F21


			//F21 T1-T2 -> T1
			echo "\n\nTransferring to F21 T1 Started....";

			$query = $this->db->query("SELECT * FROM F21T1 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT *, petitioner_name as `petitioner`, received_date as `date_rcv`, investigating_officer_name as `investigating_officer`  FROM F21T2_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			#var_dump($array1);


			$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T1', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T1 Done....";





			//F21 T3 & T4-> T3
			echo "\n\nTransferring to F21 T3 Started....";

			$query = $this->db->query("SELECT * FROM F21T3 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT * FROM F21T4 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T3', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T3 Done....";



			echo "\n\nTransferring to F21 T5 Started....";

			$query = $this->db->query("SELECT * FROM F21T5 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T6_RCV WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT * FROM F21T6_CMPLTD WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', referring_office = '".$value['referring_office'] ."' ,received_date = '".$value['received_date'] ."' ,reasons = '".$value['reasons'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						// echo $query_insert;
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T5', start_date='".$datenow."'";
							$this->db->query($sql1);
							// echo $sql1;
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T5 Done....";

			


			echo "\n\nTransferring to F21 T7 Pardon Started....";

			$query = $this->db->query("SELECT * FROM F21T7_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T8_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			//$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}


			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			// var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T7_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T7_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T7_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T7 PARDON Done....";

			echo "\n\nTransferring to F21 T7 PAROL Started....";

			$query = $this->db->query("SELECT * FROM F21T7_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T8_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			//$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}

			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T7_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T7_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T7_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T7 PAROL Done....";


			echo "\n\nTransferring to F21 T10 PAROL Started....";

			$query = $this->db->query("SELECT * FROM F21T10_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
				}
			}

			$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T10_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T10_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T10_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T10 PAROL Done....";

			echo "\n\nTransferring to F21 T10 PARDON Started....";

			$query = $this->db->query("SELECT * FROM F21T10_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			
			$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
				}
			}


			$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T10_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T10_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T10_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T10 PARDON Done....";


			echo "\n\nTransferring to F21 T12 PAROL Started....";

			$query = $this->db->query("SELECT * FROM F21T12_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T12_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T12_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T12_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T12 PAROL Done....";


			echo "\n\nTransferring to F21 T12 PARDON Started....";

			$query = $this->db->query("SELECT * FROM F21T12_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T12_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T12_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T12_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T12 PARDON Done....";


			echo "\n\nTransferring to F21 T14 PAROL Started....";

			$sql = "SELECT * FROM F21T14_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'";
			#echo "\n".$sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				#echo $query->num_rows();
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$sql = "SELECT * FROM F21T15_RCV_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'";
			#echo "\n".$sql;
			$query = $this->db->query($sql);

			#$array1 = array();
			if($query){
				#echo $query->num_rows();
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$sql = "SELECT * FROM F21T15_TERM_PAROL WHERE field_office = '".$field_office."' and  status = '1' and Y_M = '".$Y_M."'";
			#echo "\n".$sql;
			$query = $this->db->query($sql);

			$array2 = array();
			if($query){
				#echo $query->num_rows();
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$sql = "SELECT docket_no FROM F21T14_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'";
				#echo "\n".$sql;
				$query_check = $this->db->query($sql);

				
				if($query_check){
					#echo $query_check->num_rows();

					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T14_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', reasons='".$value['reasons']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T14_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T14 PAROL Done....";

			echo "\n\nTransferring to F21 T14 PARDON Started....";

			$query = $this->db->query("SELECT * FROM F21T14_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T15_RCV_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			#$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F21T15_TERM_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T14_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T14_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T14 PARDON Done....";

		}
		public function migrate_f5($payload)
		{
			// $field_office = $_GET['field'];
			// $Y_M = $_GET['date'];
			// $datenow = date("Y-m-d H:i:s");
			$field_office = $payload->field_office;
			$Y_M = $payload->Y_M;
			$datenow = date("Y-m-d H:i:s");

			// echo "\nTransferring to F5 T1 Started....";
			//F5 T2 (INSERT TO T1)
			$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner`,received_date as `date_rcv`,investigating_officer_name as `investigating_officer`,field_office FROM F5T2_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$this->db->reconnect();
			$query = $this->db->query("SELECT docket_no,petitioner,date_rcv,investigating_officer,field_office FROM F5T1 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			// var_dump($array1);
			// exit();
			$this->db->reconnect();
			//F5 T2 (INSERT TO T1)
			$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$this->db->reconnect();
			$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_NOTACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);

			$result = $this->check_diff_multi($array1, $array2);
			
			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			#var_dump($result);

			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";

						$this->db->reconnect();
						$sql = "INSERT INTO F5T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T1', start_date='".$datenow."'";
							$this->db->query($sql1);
						}
					}
				}
			}
			echo "\n\nTransferring to F5 T1 Done....";


			//F5 T2 ACTED to T3
			echo "\n\nTransferring from F5 T2 Acted to F5 T3 Started....";

			$query = $this->db->query("SELECT * FROM F5T2_ACTED WHERE field_office = '".$field_office."' and transfer_date is NULL and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{


						$this->db->reconnect();
						$query_check2 = $this->db->query("SELECT docket_no FROM F5T4 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$Y_M."' AND status = 1");

						
						if($query_check2){
							if($query_check2->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{


								//INSERT
								$datenow = date("Y-m-d H:i:s");
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner_name']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['ppo_recommendation'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest_date'] ."' , investigating_officer=NULL, status=1,source=2,created_by=0,created_date='".$datenow."'";
								#echo $sql;
								$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T3', start_date='".$datenow."'";
							$this->db->query($sql1);
						}
							}
						}

					}
				}
			}


			//F5 T4 & T3
			echo "\n\nTransferring to F5 T3 Started....";

			$query = $this->db->query("SELECT * FROM F5T3 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$this->db->reconnect();
			$array2 = array();
			$query = $this->db->query("SELECT * FROM F5T4 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$result = $this->check_diff_multi($array1, $array2);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T3', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T3 Done....";
			//


			//F5 T5 & T6
			echo "\n\nTransferring to F5 T5 Started....";

			$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T5 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T6_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			#var_dump($array1);




			$query = $this->db->query("SELECT docket_no,petitioner FROM F5T6_CMPLTD WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);


			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', received_date = '".$value['received_date'] ."' , investigating_officer='".$value['investigating_officer']."', reasons='".$value['reasons']."', referring_office='".$value['referring_office']."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T5', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T5 Done....";


			//F5T7(FEB) = F5T7(JAN) + F5T8(JAN) - F5T11 (JAN)
			echo "\n\nTransferring to F5 T7 Started....";

			$query = $this->db->query("SELECT * FROM F5T7 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F5T8 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			#var_dump($array1);


			$query = $this->db->query("SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and disposed_decision != 'Extension of Probation Period' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T7 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T7 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."',case_classification='".$value['case_classification']."', received_date = '".$value['received_date'] ."' , supervising_officer='".$value['supervising_officer']."', probation_start='".$value['probation_start']."', probation_end='".$value['probation_end']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T7', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T7 Done....";



			//F5T10(FEB) = F5T9(JAN) + F5T10(JAN) - F5T11(JAN)
			$this->db->reconnect();
			echo "\n\nTransferring to F5 T10 Started....";
			$sql = "SELECT * FROM F5T10 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date`  FROM F5T9 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$this->db->reconnect();
			$array2 = array();
			$sql = "SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			$query = $this->db->query($sql);

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$result = $this->check_diff_multi($array1, $array2);
			
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T10 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T10 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T10', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T10 Done....";
			//


			//F5 T9 ACTED to F5 T10
			/*$this->db->reconnect();
			echo "\n\nTransferring to F5 T10 Started....";
			echo "\n\n".$field_office;
			$sql = "SELECT * FROM F5T9 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."'";
			#echo $sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			var_dump($array1);
			foreach($array1 as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T10 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						$this->db->reconnect();
						$query_check2 = $this->db->query("SELECT docket_no FROM F5T11 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$Y_M."'");
						if($query_check2){
							if($query_check2->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								$datenow=date("Y-m-d H:i:s");
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T10 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', submitted_decision = '".$value['disposed_decision'] ."' ,submitted_date = '".$value['disposed_date'] ."' ,supervising_officer = NULL, status=1,source=2,created_by=0,created_date='".$datenow."'";
								#echo $sql;
								$query_insert = $this->db->query($sql);
							}
						}
						


					}
				}
			}
			echo "\n\nTransferring to F5 T10 Done....";
			*/


			//F5 T12 & T13
			$this->db->reconnect();
			echo "\n\nTransferring to F5 T12 Started....";
			$sql = "SELECT * FROM F5T12 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$sql = "SELECT * FROM F5T13_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			#var_dump($array1);

			$this->db->reconnect();
			$array2 = array();
			$sql = "SELECT * FROM F5T13_TERM WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$result = $this->check_diff_multi($array1, $array2);
			
			#var_dump($array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T12 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T12 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' ,case_classification = '".$value['case_classification'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T12', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T10 Done....";
			//
		}

		public function migrate_f5t1($payload){
			
			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");

			echo "\nTransferring to F5 T1 Started....";
			//F5 T2 (INSERT TO T1)
			$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner`,received_date as `date_rcv`,investigating_officer_name as `investigating_officer`,field_office FROM F5T2_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$this->db->reconnect();
			$query = $this->db->query("SELECT docket_no,petitioner,date_rcv,investigating_officer,field_office FROM F5T1 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			// var_dump($array1);
			// exit();
			$this->db->reconnect();
			//F5 T2 (INSERT TO T1)
			$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$this->db->reconnect();
			$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_NOTACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);

			$result = $this->check_diff_multi($array1, $array2);
			
			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			#var_dump($result);

			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";

						$this->db->reconnect();
						$sql = "INSERT INTO F5T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T1', start_date='".$datenow."'";
							$this->db->query($sql1);
						}
					}
				}
			}
			echo "\n\nTransferring to F5 T1 Done....";
		}
		public function migrate_f5t3($payload){
			
			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			//F5 T2 ACTED to T3

			echo "\n\nTransferring from F5 T2 Acted to F5 T3 Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";
			
			$query = $this->db->query("SELECT * FROM F5T2_ACTED WHERE field_office = '".$field_office."' and transfer_date is NULL and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{


						$this->db->reconnect();
						$query_check2 = $this->db->query("SELECT docket_no FROM F5T4 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$Y_M."' AND status = 1");

						
						if($query_check2){
							if($query_check2->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{


								//INSERT
								$datenow = date("Y-m-d H:i:s");
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner_name']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['ppo_recommendation'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest_date'] ."' , investigating_officer=NULL, status=1,source=2,created_by=0,created_date='".$datenow."'";
								#echo $sql;
								$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T3', start_date='".$datenow."'";
							$this->db->query($sql1);
						}
							}
						}

					}
				}
			}


			//F5 T4 & T3
			echo "\n\nTransferring to F5 T3 Started....";

			$query = $this->db->query("SELECT * FROM F5T3 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$this->db->reconnect();
			$array2 = array();
			$query = $this->db->query("SELECT * FROM F5T4 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$result = $this->check_diff_multi($array1, $array2);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T3', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T3 Done....";
			//
		}
		public function migrate_f5t5($payload){
			
			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
				//F5 T5 & T6
			echo "\n\nTransferring to F5 T5 Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T5 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T6_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			#var_dump($array1);




			$query = $this->db->query("SELECT docket_no,petitioner FROM F5T6_CMPLTD WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);


			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', received_date = '".$value['received_date'] ."' , investigating_officer='".$value['investigating_officer']."', reasons='".$value['reasons']."', referring_office='".$value['referring_office']."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T5', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T5 Done....";
		}
		public function migrate_f5t7($payload){
			
			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");

			//F5T7(FEB) = F5T7(JAN) + F5T8(JAN) - F5T11 (JAN)
			echo "\n\nTransferring to F5 T7 Started....";
			$this->db->reconnect();
			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F5T7 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F5T8 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			#var_dump($array1);

			$this->db->reconnect();
			$query = $this->db->query("SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and disposed_decision != 'Extension of Probation Period' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T7 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T7 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."',case_classification='".$value['case_classification']."', received_date = '".$value['received_date'] ."' , supervising_officer='".$value['supervising_officer']."', probation_start='".$value['probation_start']."', probation_end='".$value['probation_end']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						// echo $query_insert;
						// var_dump($query_insert);
						// $error = $this->db->error();
						// echo $error['message'];
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T7', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T7 Done....";
			
		}
		public function migrate_f5t10($payload){
			
			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			//F5T10(FEB) = F5T9(JAN) + F5T10(JAN) - F5T11(JAN)
			$this->db->reconnect();
			echo "\n\nTransferring to F5 T10 Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";
			$sql = "SELECT * FROM F5T10 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date`  FROM F5T9 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$this->db->reconnect();
			$array2 = array();
			$sql = "SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			$query = $this->db->query($sql);

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$result = $this->check_diff_multi($array1, $array2);
			
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T10 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T10 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T10', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T10 Done....";
			//
			
		}
		public function migrate_f5t12($payload){
			
			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			//F5 T12 & T13
			$this->db->reconnect();
			echo "\n\nTransferring to F5 T12 Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";
			$sql = "SELECT * FROM F5T12 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$sql = "SELECT * FROM F5T13_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			#var_dump($array1);

			$this->db->reconnect();
			$array2 = array();
			$sql = "SELECT * FROM F5T13_TERM WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$result = $this->check_diff_multi($array1, $array2);
			
			#var_dump($array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T12 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T12 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' ,case_classification = '".$value['case_classification'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T12', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T12 Done....";
			//
			
		}
		public function migrate_f21t1($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			

			//F21 T1-T2 -> T1
			echo "\n\nTransferring to F21 T1 Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T1 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT *, petitioner_name as `petitioner`, received_date as `date_rcv`, investigating_officer_name as `investigating_officer`  FROM F21T2_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			#var_dump($array1);


			$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T1', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T1 Done....";

		}
		public function migrate_f21t3($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			
			//F21 T3 & T4-> T3
			echo "\n\nTransferring to F21 T3 Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T3 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT * FROM F21T4 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T3', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T3 Done....";

		}
		public function migrate_f21t5($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			
			echo "\n\nTransferring to F21 T5 Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T5 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T6_RCV WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT * FROM F21T6_CMPLTD WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', referring_office = '".$value['referring_office'] ."' ,received_date = '".$value['received_date'] ."' ,reasons = '".$value['reasons'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						// echo $query_insert;
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T5', start_date='".$datenow."'";
							$this->db->query($sql1);
							// echo $sql1;
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T5 Done....";

		}
		public function migrate_f21t7_pardon($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");			
			echo "\n\nTransferring to F21 T7 Pardon Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T7_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T8_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			//$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}


			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			// var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T7_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T7_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T7_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T7 PARDON Done....";
			
		}
		public function migrate_f21t7_parol($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			
			echo "\n\nTransferring to F21 T7 PAROL Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T7_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T8_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			//$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}

			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T7_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T7_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T7_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T7 PAROL Done....";

		}
		public function migrate_f21t10_pardon($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			
			echo "\n\nTransferring to F21 T10 PARDON Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T10_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			
			$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
				}
			}


			$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T10_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T10_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T10_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T10 PARDON Done....";

		}
		public function migrate_f21t10_parol($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			echo "\n\nTransferring to F21 T10 PAROL Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T10_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
				}
			}

			$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T10_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T10_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T10_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T10 PAROL Done....";

			
		}
		public function migrate_f21t12_pardon($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			
			echo "\n\nTransferring to F21 T12 PARDON Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T12_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T12_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T12_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T12_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T12 PARDON Done....";

		}
		public function migrate_f21t12_parol($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			
			echo "\n\nTransferring to F21 T12 PAROL Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T12_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T12_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T12_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T12_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T12 PAROL Done....";

		}
		public function migrate_f21t14_pardon($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			
			echo "\n\nTransferring to F21 T14 PARDON Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$query = $this->db->query("SELECT * FROM F21T14_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T15_RCV_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			#$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F21T15_TERM_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T14_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T14_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T14 PARDON Done....";

		}
		public function migrate_f21t14_parol($payload){

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");
			
			echo "\n\nTransferring to F21 T14 PAROL Started....";

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			$sql = "SELECT * FROM F21T14_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'";
			#echo "\n".$sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				#echo $query->num_rows();
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$sql = "SELECT * FROM F21T15_RCV_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'";
			#echo "\n".$sql;
			$query = $this->db->query($sql);

			#$array1 = array();
			if($query){
				#echo $query->num_rows();
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$sql = "SELECT * FROM F21T15_TERM_PAROL WHERE field_office = '".$field_office."' and  status = '1' and Y_M = '".$Y_M."'";
			#echo "\n".$sql;
			$query = $this->db->query($sql);

			$array2 = array();
			if($query){
				#echo $query->num_rows();
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$sql = "SELECT docket_no FROM F21T14_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'";
				#echo "\n".$sql;
				$query_check = $this->db->query($sql);

				
				if($query_check){
					#echo $query_check->num_rows();

					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T14_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', reasons='".$value['reasons']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T14_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T14 PAROL Done....";

		}
		public function migrate_offline()
		{
			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");

			// echo "\nTransferring to F5 T1 Started....";
			//F5 T2 (INSERT TO T1)
			$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner`,received_date as `date_rcv`,investigating_officer_name as `investigating_officer`,field_office FROM F5T2_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$this->db->reconnect();
			$query = $this->db->query("SELECT docket_no,petitioner,date_rcv,investigating_officer,field_office FROM F5T1 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			// var_dump($array1);
			// exit();
			$this->db->reconnect();
			//F5 T2 (INSERT TO T1)
			$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$this->db->reconnect();
			$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_NOTACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);

			$result = $this->check_diff_multi($array1, $array2);
			
			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";

			#var_dump($result);

			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";

						$this->db->reconnect();
						$sql = "INSERT INTO F5T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T1', start_date='".$datenow."'";
							$this->db->query($sql1);
						}
					}
				}
			}
			echo "\n\nTransferring to F5 T1 Done....";


			//F5 T2 ACTED to T3
			echo "\n\nTransferring from F5 T2 Acted to F5 T3 Started....";

			$query = $this->db->query("SELECT * FROM F5T2_ACTED WHERE field_office = '".$field_office."' and transfer_date is NULL and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{


						$this->db->reconnect();
						$query_check2 = $this->db->query("SELECT docket_no FROM F5T4 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$Y_M."' AND status = 1");

						
						if($query_check2){
							if($query_check2->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{


								//INSERT
								$datenow = date("Y-m-d H:i:s");
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner_name']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['ppo_recommendation'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest_date'] ."' , investigating_officer=NULL, status=1,source=2,created_by=0,created_date='".$datenow."'";
								#echo $sql;
								$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T3', start_date='".$datenow."'";
							$this->db->query($sql1);
						}
							}
						}

					}
				}
			}


			//F5 T4 & T3
			echo "\n\nTransferring to F5 T3 Started....";

			$query = $this->db->query("SELECT * FROM F5T3 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$this->db->reconnect();
			$array2 = array();
			$query = $this->db->query("SELECT * FROM F5T4 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$result = $this->check_diff_multi($array1, $array2);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T3', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T3 Done....";
			//


			//F5 T5 & T6
			echo "\n\nTransferring to F5 T5 Started....";

			$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T5 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T6_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			#var_dump($array1);




			$query = $this->db->query("SELECT docket_no,petitioner FROM F5T6_CMPLTD WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);


			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', received_date = '".$value['received_date'] ."' , investigating_officer='".$value['investigating_officer']."', reasons='".$value['reasons']."', referring_office='".$value['referring_office']."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T5', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T5 Done....";


			//F5T7(FEB) = F5T7(JAN) + F5T8(JAN) - F5T11 (JAN)
			echo "\n\nTransferring to F5 T7 Started....";

			$query = $this->db->query("SELECT * FROM F5T7 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F5T8 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			#var_dump($array1);


			$query = $this->db->query("SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and disposed_decision != 'Extension of Probation Period' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T7 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T7 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."',case_classification='".$value['case_classification']."', received_date = '".$value['received_date'] ."' , supervising_officer='".$value['supervising_officer']."', probation_start='".$value['probation_start']."', probation_end='".$value['probation_end']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T7', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T7 Done....";



			//F5T10(FEB) = F5T9(JAN) + F5T10(JAN) - F5T11(JAN)
			$this->db->reconnect();
			echo "\n\nTransferring to F5 T10 Started....";
			$sql = "SELECT * FROM F5T10 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date`  FROM F5T9 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$this->db->reconnect();
			$array2 = array();
			$sql = "SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			$query = $this->db->query($sql);

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$result = $this->check_diff_multi($array1, $array2);
			
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T10 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T10 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T10', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T10 Done....";
			//


			//F5 T9 ACTED to F5 T10
			/*$this->db->reconnect();
			echo "\n\nTransferring to F5 T10 Started....";
			echo "\n\n".$field_office;
			$sql = "SELECT * FROM F5T9 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."'";
			#echo $sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			var_dump($array1);
			foreach($array1 as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T10 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						$this->db->reconnect();
						$query_check2 = $this->db->query("SELECT docket_no FROM F5T11 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$Y_M."'");
						if($query_check2){
							if($query_check2->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								$datenow=date("Y-m-d H:i:s");
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T10 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', submitted_decision = '".$value['disposed_decision'] ."' ,submitted_date = '".$value['disposed_date'] ."' ,supervising_officer = NULL, status=1,source=2,created_by=0,created_date='".$datenow."'";
								#echo $sql;
								$query_insert = $this->db->query($sql);
							}
						}
						


					}
				}
			}
			echo "\n\nTransferring to F5 T10 Done....";
			*/


			//F5 T12 & T13
			$this->db->reconnect();
			echo "\n\nTransferring to F5 T12 Started....";
			$sql = "SELECT * FROM F5T12 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$sql = "SELECT * FROM F5T13_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			#var_dump($array1);

			$this->db->reconnect();
			$array2 = array();
			$sql = "SELECT * FROM F5T13_TERM WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
			#echo $sql;
			$query = $this->db->query($sql);

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$result = $this->check_diff_multi($array1, $array2);
			
			#var_dump($array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T12 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T12 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' ,case_classification = '".$value['case_classification'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
						#echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F5T12', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T10 Done....";
			//

			

			//@F21


			//F21 T1-T2 -> T1
			echo "\n\nTransferring to F21 T1 Started....";

			$query = $this->db->query("SELECT * FROM F21T1 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT *, petitioner_name as `petitioner`, received_date as `date_rcv`, investigating_officer_name as `investigating_officer`  FROM F21T2_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			#var_dump($array1);


			$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T1', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T1 Done....";





			//F21 T3 & T4-> T3
			echo "\n\nTransferring to F21 T3 Started....";

			$query = $this->db->query("SELECT * FROM F21T3 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT * FROM F21T4 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T3', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T3 Done....";



			echo "\n\nTransferring to F21 T5 Started....";

			$query = $this->db->query("SELECT * FROM F21T5 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T6_RCV WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT * FROM F21T6_CMPLTD WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', referring_office = '".$value['referring_office'] ."' ,received_date = '".$value['received_date'] ."' ,reasons = '".$value['reasons'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						// echo $query_insert;
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T5', start_date='".$datenow."'";
							$this->db->query($sql1);
							// echo $sql1;
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T5 Done....";

			


			echo "\n\nTransferring to F21 T7 Pardon Started....";

			$query = $this->db->query("SELECT * FROM F21T7_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T8_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}



			$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			//$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}


			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			// var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T7_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T7_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T7_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T7 PARDON Done....";

			echo "\n\nTransferring to F21 T7 PAROL Started....";

			$query = $this->db->query("SELECT * FROM F21T7_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T8_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			//$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}

			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T7_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T7_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T7_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T7 PAROL Done....";


			echo "\n\nTransferring to F21 T10 PAROL Started....";

			$query = $this->db->query("SELECT * FROM F21T10_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
				}
			}

			$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T10_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T10_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T10_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T10 PAROL Done....";

			echo "\n\nTransferring to F21 T10 PARDON Started....";

			$query = $this->db->query("SELECT * FROM F21T10_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			
			$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
				}
			}


			$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T10_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T10_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T10_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T10 PARDON Done....";


			echo "\n\nTransferring to F21 T12 PAROL Started....";

			$query = $this->db->query("SELECT * FROM F21T12_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T12_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T12_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T12_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T12 PAROL Done....";


			echo "\n\nTransferring to F21 T12 PARDON Started....";

			$query = $this->db->query("SELECT * FROM F21T12_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T12_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T12_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T12_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T12 PARDON Done....";


			echo "\n\nTransferring to F21 T14 PAROL Started....";

			$sql = "SELECT * FROM F21T14_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'";
			#echo "\n".$sql;
			$query = $this->db->query($sql);

			$array1 = array();
			if($query){
				#echo $query->num_rows();
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$sql = "SELECT * FROM F21T15_RCV_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'";
			#echo "\n".$sql;
			$query = $this->db->query($sql);

			#$array1 = array();
			if($query){
				#echo $query->num_rows();
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$sql = "SELECT * FROM F21T15_TERM_PAROL WHERE field_office = '".$field_office."' and  status = '1' and Y_M = '".$Y_M."'";
			#echo "\n".$sql;
			$query = $this->db->query($sql);

			$array2 = array();
			if($query){
				#echo $query->num_rows();
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$sql = "SELECT docket_no FROM F21T14_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'";
				#echo "\n".$sql;
				$query_check = $this->db->query($sql);

				
				if($query_check){
					#echo $query_check->num_rows();

					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T14_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', reasons='".$value['reasons']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T14_PAROL', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T14 PAROL Done....";

			echo "\n\nTransferring to F21 T14 PARDON Started....";

			$query = $this->db->query("SELECT * FROM F21T14_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			$query = $this->db->query("SELECT * FROM F21T15_RCV_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			#$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}


			$query = $this->db->query("SELECT * FROM F21T15_TERM_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F21T14_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='manual', status='1', form_table='F21T14_PARDON', start_date='".$datenow."'";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F21 T14 PARDON Done....";

		}

		public function migrate_offline_checker()
		{

			$field_office = $_GET['field'];
			$Y_M = $_GET['date'];
			$datenow = date("Y-m-d H:i:s");

			$curr_date = strtotime(date($Y_M."-01"));
			#echo $curr_date;
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
			echo "\nTransferring to date: ".$date_transfer."\n";
			
			echo "\n\nTransferring to F5 T7 Started....";

			$query = $this->db->query("SELECT * FROM F5T7 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
				}
			}

			$query = $this->db->query("SELECT * FROM F5T8 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

			
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}

			#var_dump($array1);

			$query = $this->db->query("SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and disposed_decision != 'Extension of Probation Period' and Y_M = '".$Y_M."' AND status = 1");

			$array2 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array2, $value);
					}
					
				}
			}
			#var_dump($array2);
			$result = $this->check_diff_multi($array1, $array2);
			
			#var_dump($result);
			foreach($result as $key => $value){
				$this->db->reconnect();
				$query_check = $this->db->query("SELECT docket_no FROM F5T7 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

				
				if($query_check){
					if($query_check->num_rows() > 0){
						echo "\n".$value['docket_no']." ALREADY EXISTS...";
					}else{
						//INSERT
						echo "\nINSERTING ".$value['docket_no']."...";
						$this->db->reconnect();
						$sql = "INSERT INTO F5T7 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."',case_classification='".$value['case_classification']."', received_date = '".$value['received_date'] ."' , supervising_officer='".$value['supervising_officer']."', probation_start='".$value['probation_start']."', probation_end='".$value['probation_end']."', status=1,source=2,created_by=0";
						// echo $sql;
						$query_insert = $this->db->query($sql);
						$this->db->close();

						//inserting to audit trail cron
						if ($query_insert) {
							$this->db->reconnect();
							$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T7', start_date='".$datenow."' AND status = 1";
							$this->db->query($sql1);
						}

					}
				}
			}
			echo "\n\nTransferring to F5 T7 Done....";
		}

		public function check_diff_multi($array1, $array2){
					    foreach($array2 as $key=>$val){
					    	foreach($array1 as $key2=>$val2){
					    		$val['docket_no'] = preg_replace('/\s+/', '', $val['docket_no']);
					    		$val2['docket_no'] = preg_replace('/\s+/', '', $val2['docket_no']);
					    		echo "\n".$val['docket_no'] ." ".$val2['docket_no'];
					    		if(strtoupper($val['docket_no']) == strtoupper($val2['docket_no'])){
					    			unset($array1[$key2]);
					    		}
					    	}
					    }
					    return $array1;
					}

		public function migrate_cron_per_region(){

			$payload = (object)array("REGION"=>$_GET['REGION']);
			$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
			$datenow = date("Y-m-d H:i:s");
			echo $datenow;
			if($fieldOffice->status == "SUCCESS"){
				foreach($fieldOffice->payload as $keyFO => $valFO){
					$field_office = $valFO->NAME;
					$Y_M = $_GET['date'];
					echo "\nTransferring to F5 T1 Started....";
					echo $datenow;
					echo "------";
					$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner`,received_date as `date_rcv`,investigating_officer_name as `investigating_officer`,field_office FROM F5T2_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}
					$this->db->reconnect();
					$query = $this->db->query("SELECT docket_no,petitioner,date_rcv,investigating_officer,field_office FROM F5T1 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$this->db->reconnect();
					//F5 T2 (INSERT TO T1)
					$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$this->db->reconnect();
					$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_NOTACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}

					$result = $this->check_diff_multi($array1, $array2);

					$curr_date = strtotime(date($Y_M."-01"));
					#echo $curr_date;
					$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
					echo "\nTransferring to date: ".$date_transfer."\n";

					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T1', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T1 Done....";


					//F5 T2 ACTED to T3
					echo "\n\nTransferring from F5 T2 Acted to F5 T3 Started....";

					$query = $this->db->query("SELECT * FROM F5T2_ACTED WHERE field_office = '".$field_office."' and transfer_date is NULL and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}
					foreach($array1 as $key => $value){
						
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{

								$this->db->reconnect();
								$query_check2 = $this->db->query("SELECT docket_no FROM F5T4 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$Y_M."' AND status = 1");

								if($query_check2){
									if($query_check2->num_rows() > 0){
										echo "\n".$value['docket_no']." ALREADY EXISTS...";
									}else{

										//INSERT
										$datenow = date("Y-m-d H:i:s");
										echo "\nINSERTING ".$value['docket_no']."...";
										$this->db->reconnect();
										$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner_name']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['ppo_recommendation'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest_date'] ."' , investigating_officer=NULL, status=1,source=2,created_by=0,created_date='".$datenow."'";
										$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T3', start_date='".$datenow."'";
									$this->db->query($sql1);
								}
									}
								}

							}
						}
					}


					//F5 T4 & T3
					echo "\n\nTransferring to F5 T3 Started....";

					$query = $this->db->query("SELECT * FROM F5T3 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$this->db->reconnect();
					$array2 = array();
					$query = $this->db->query("SELECT * FROM F5T4 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$result = $this->check_diff_multi($array1, $array2);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T3', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T3 Done....";
					//

					//F5 T5 & T6
					echo "\n\nTransferring to F5 T5 Started....";

					$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T5 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T6_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					#var_dump($array1);

					$query = $this->db->query("SELECT docket_no,petitioner FROM F5T6_CMPLTD WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);


					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', received_date = '".$value['received_date'] ."' , investigating_officer='".$value['investigating_officer']."', reasons='".$value['reasons']."', referring_office='".$value['referring_office']."', status=1,source=2,created_by=0";
								#echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T5', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T5 Done....";


					//F5T7(FEB) = F5T7(JAN) + F5T8(JAN) - F5T11 (JAN)
					echo "\n\nTransferring to F5 T7 Started....";

					$query = $this->db->query("SELECT * FROM F5T7 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
						}
					}

					$query = $this->db->query("SELECT * FROM F5T8 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					#var_dump($array1);

					$query = $this->db->query("SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and disposed_decision != 'Extension of Probation Period' and Y_M = '".$Y_M."' AND status = 1");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T7 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T7 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."',case_classification='".$value['case_classification']."', received_date = '".$value['received_date'] ."' , supervising_officer='".$value['supervising_officer']."', probation_start='".$value['probation_start']."', probation_end='".$value['probation_end']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T7', start_date='".$datenow."' AND status = 1";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T7 Done....";



					//F5T10(FEB) = F5T9(JAN) + F5T10(JAN) - F5T11(JAN)
					$this->db->reconnect();
					echo "\n\nTransferring to F5 T10 Started....";
					$sql = "SELECT * FROM F5T10 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					#echo $sql;
					$query = $this->db->query($sql);

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date`  FROM F5T9 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$this->db->reconnect();
					$array2 = array();
					$sql = "SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					$query = $this->db->query($sql);

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$result = $this->check_diff_multi($array1, $array2);
					
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T10 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T10 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
								#echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T10', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T10 Done....";
					//

					//F5 T9 ACTED to F5 T10
					/*$this->db->reconnect();
					echo "\n\nTransferring to F5 T10 Started....";
					echo "\n\n".$field_office;
					$sql = "SELECT * FROM F5T9 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."'";
					#echo $sql;
					$query = $this->db->query($sql);

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}
					var_dump($array1);
					foreach($array1 as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T10 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								$this->db->reconnect();
								$query_check2 = $this->db->query("SELECT docket_no FROM F5T11 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$Y_M."'");
								if($query_check2){
									if($query_check2->num_rows() > 0){
										echo "\n".$value['docket_no']." ALREADY EXISTS...";
									}else{
										$datenow=date("Y-m-d H:i:s");
										echo "\nINSERTING ".$value['docket_no']."...";
										$this->db->reconnect();
										$sql = "INSERT INTO F5T10 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', submitted_decision = '".$value['disposed_decision'] ."' ,submitted_date = '".$value['disposed_date'] ."' ,supervising_officer = NULL, status=1,source=2,created_by=0,created_date='".$datenow."'";
										#echo $sql;
										$query_insert = $this->db->query($sql);
									}
								}
								


							}
						}
					}
					echo "\n\nTransferring to F5 T10 Done....";
					*/


					//F5 T12 & T13
					$this->db->reconnect();
					echo "\n\nTransferring to F5 T12 Started....";
					$sql = "SELECT * FROM F5T12 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					#echo $sql;
					$query = $this->db->query($sql);

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$sql = "SELECT * FROM F5T13_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					#echo $sql;
					$query = $this->db->query($sql);

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}
					#var_dump($array1);

					$this->db->reconnect();
					$array2 = array();
					$sql = "SELECT * FROM F5T13_TERM WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					#echo $sql;
					$query = $this->db->query($sql);

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$result = $this->check_diff_multi($array1, $array2);
					
					#var_dump($array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T12 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T12 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' ,case_classification = '".$value['case_classification'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
								#echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T12', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T10 Done....";
					//

					//@F21

					//F21 T1-T2 -> T1
					//@F21

					//2022-02-27
					//F21 T1-T2 -> T1
					echo "\n\nTransferring to F21 T1 Started....";

					$query = $this->db->query("SELECT * FROM F21T1 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}



					$query = $this->db->query("SELECT *, petitioner_name as `petitioner`, received_date as `date_rcv`, investigating_officer_name as `investigating_officer`  FROM F21T2_RCV WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					#var_dump($array1);

					$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T1', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T1 Done....";


					//F21 T3 & T4-> T3
					echo "\n\nTransferring to F21 T3 Started....";

					$query = $this->db->query("SELECT * FROM F21T3 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T4 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T3', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T3 Done....";

					echo "\n\nTransferring to F21 T5 Started....";

					$query = $this->db->query("SELECT * FROM F21T5 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T6_RCV WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T6_CMPLTD WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."' and status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', referring_office = '".$value['referring_office'] ."' ,received_date = '".$value['received_date'] ."' ,reasons = '".$value['reasons'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T5', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T5 Done....";


					echo "\n\nTransferring to F21 T7 Pardon Started....";

					$query = $this->db->query("SELECT * FROM F21T7_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T8_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}



					$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					//$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}

					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T7_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."' and status = 1");
						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T7_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T7_PARDON', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T7 PARDON Done....";

					echo "\n\nTransferring to F21 T7 PAROL Started....";

					$query = $this->db->query("SELECT * FROM F21T7_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T8_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}



					$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					//$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}

					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T7_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1' and status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T7_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T7_PAROL', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T7 PAROL Done....";


					echo "\n\nTransferring to F21 T10 PAROL Started....";

					$query = $this->db->query("SELECT * FROM F21T10_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					
					$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
						}
					}

					$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T10_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1' and status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T10_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T10_PAROL', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T10 PAROL Done....";

					echo "\n\nTransferring to F21 T10 PARDON Started....";

					$query = $this->db->query("SELECT * FROM F21T10_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					
					$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
						}
					}

					$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T10_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1' and status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T10_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T10_PARDON', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T10 PARDON Done....";


					echo "\n\nTransferring to F21 T12 PAROL Started....";

					$query = $this->db->query("SELECT * FROM F21T12_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T12_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1' and status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T12_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T12_PAROL', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T12 PAROL Done....";


					echo "\n\nTransferring to F21 T12 PARDON Started....";

					$query = $this->db->query("SELECT * FROM F21T12_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T12_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1' and status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T12_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T12_PARDON', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T12 PARDON Done....";


					echo "\n\nTransferring to F21 T14 PAROL Started....";

					$query = $this->db->query("SELECT * FROM F21T14_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							echo "\F21T14_PAROL\n";
							$data = $query->result_array();
							var_dump($data);
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T15_RCV_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					#$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							echo "\nF21T15_RCV_PAROL\n";
							$data = $query->result_array();
							var_dump($data);
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T15_TERM_PAROL WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T14_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T14_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', reasons='".$value['reasons']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T14_PAROL', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T14 PAROL Done....";

					echo "\n\nTransferring to F21 T14 PARDON Started....";

					$query = $this->db->query("SELECT * FROM F21T14_PARDON WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T15_RCV_PARDON WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

					#$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T15_TERM_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and status = '1' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T14_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T14_PARDON', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T14 PARDON Done....";

				}
			}
		}

		public function migrate_offline_cron()
		{
			$payload = (object)array("USER_LEVEL_ID"=>0,"FIELD_OFFICE"=>0);
			$fieldOffice = json_decode($this->Pis_model->getAllFieldOffices($payload));
			$datenow = date("Y-m-d H:i:s");
			#var_dump($fieldOffice);
			if($fieldOffice->status == "SUCCESS"){
				foreach($fieldOffice->payload as $keyFO => $valFO){
					#echo $valFO->NAME;
				
				/**/
					
					$field_office = $valFO->NAME;
					$Y_M = $_GET['date'];
					echo "\nTransferring to F5 T1 Started....";
					$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner`,received_date as `date_rcv`,investigating_officer_name as `investigating_officer`,field_office FROM F5T2_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}
					$this->db->reconnect();
					$query = $this->db->query("SELECT docket_no,petitioner,date_rcv,investigating_officer,field_office FROM F5T1 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$this->db->reconnect();
					//F5 T2 (INSERT TO T1)
					$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_ACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$this->db->reconnect();
					$query = $this->db->query("SELECT docket_no,petitioner_name as `petitioner` FROM F5T2_NOTACTED WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}

					$result = $this->check_diff_multi($array1, $array2);

					$curr_date = strtotime(date($Y_M."-01"));
					#echo $curr_date;
					$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
					echo "\nTransferring to date: ".$date_transfer."\n";

					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T1', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T1 Done....";


					//F5 T2 ACTED to T3
					echo "\n\nTransferring from F5 T2 Acted to F5 T3 Started....";

					$query = $this->db->query("SELECT * FROM F5T2_ACTED WHERE field_office = '".$field_office."' and transfer_date is NULL and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}
					foreach($array1 as $key => $value){
						
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{

								$this->db->reconnect();
								$query_check2 = $this->db->query("SELECT docket_no FROM F5T4 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$Y_M."' and status = 1");

								if($query_check2){
									if($query_check2->num_rows() > 0){
										echo "\n".$value['docket_no']." ALREADY EXISTS...";
									}else{

										//INSERT
										$datenow = date("Y-m-d H:i:s");
										echo "\nINSERTING ".$value['docket_no']."...";
										$this->db->reconnect();
										$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner_name']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['ppo_recommendation'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest_date'] ."' , investigating_officer=NULL, status=1,source=2,created_by=0,created_date='".$datenow."'";
										$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T3', start_date='".$datenow."'";
									$this->db->query($sql1);
								}
									}
								}

							}
						}
					}


					//F5 T4 & T3
					echo "\n\nTransferring to F5 T3 Started....";

					$query = $this->db->query("SELECT * FROM F5T3 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$this->db->reconnect();
					$array2 = array();
					$query = $this->db->query("SELECT * FROM F5T4 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$result = $this->check_diff_multi($array1, $array2);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T3', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T3 Done....";
					//

					//F5 T5 & T6
					echo "\n\nTransferring to F5 T5 Started....";

					$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T5 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT docket_no,petitioner,received_date ,investigating_officer,field_office,referring_office,reasons FROM F5T6_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					#var_dump($array1);

					$query = $this->db->query("SELECT docket_no,petitioner FROM F5T6_CMPLTD WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);


					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', petitioner = '".$value['petitioner']."',docket_no='".$value['docket_no']."', received_date = '".$value['received_date'] ."' , investigating_officer='".$value['investigating_officer']."', reasons='".$value['reasons']."', referring_office='".$value['referring_office']."', status=1,source=2,created_by=0";
								#echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T5', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T5 Done....";


					//F5T7(FEB) = F5T7(JAN) + F5T8(JAN) - F5T11 (JAN)
					echo "\n\nTransferring to F5 T7 Started....";

					$query = $this->db->query("SELECT * FROM F5T7 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
						}
					}

					$query = $this->db->query("SELECT * FROM F5T8 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					#var_dump($array1);

					$query = $this->db->query("SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and disposed_decision != 'Extension of Probation Period' and Y_M = '".$Y_M."' AND status = 1");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T7 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T7 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."',case_classification='".$value['case_classification']."', received_date = '".$value['received_date'] ."' , supervising_officer='".$value['supervising_officer']."', probation_start='".$value['probation_start']."', probation_end='".$value['probation_end']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T7', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T7 Done....";



					//F5T10(FEB) = F5T9(JAN) + F5T10(JAN) - F5T11(JAN)
					$this->db->reconnect();
					echo "\n\nTransferring to F5 T10 Started....";
					$sql = "SELECT * FROM F5T10 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					#echo $sql;
					$query = $this->db->query($sql);

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date`  FROM F5T9 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$this->db->reconnect();
					$array2 = array();
					$sql = "SELECT * FROM F5T11 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					$query = $this->db->query($sql);

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$result = $this->check_diff_multi($array1, $array2);
					
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T10 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T10 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
								#echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T10', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T10 Done....";
					//

					//F5 T9 ACTED to F5 T10
					/*$this->db->reconnect();
					echo "\n\nTransferring to F5 T10 Started....";
					echo "\n\n".$field_office;
					$sql = "SELECT * FROM F5T9 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."'";
					#echo $sql;
					$query = $this->db->query($sql);

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}
					var_dump($array1);
					foreach($array1 as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T10 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								$this->db->reconnect();
								$query_check2 = $this->db->query("SELECT docket_no FROM F5T11 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$Y_M."'");
								if($query_check2){
									if($query_check2->num_rows() > 0){
										echo "\n".$value['docket_no']." ALREADY EXISTS...";
									}else{
										$datenow=date("Y-m-d H:i:s");
										echo "\nINSERTING ".$value['docket_no']."...";
										$this->db->reconnect();
										$sql = "INSERT INTO F5T10 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', submitted_decision = '".$value['disposed_decision'] ."' ,submitted_date = '".$value['disposed_date'] ."' ,supervising_officer = NULL, status=1,source=2,created_by=0,created_date='".$datenow."'";
										#echo $sql;
										$query_insert = $this->db->query($sql);
									}
								}
								


							}
						}
					}
					echo "\n\nTransferring to F5 T10 Done....";
					*/


					//F5 T12 & T13
					$this->db->reconnect();
					echo "\n\nTransferring to F5 T12 Started....";
					$sql = "SELECT * FROM F5T12 WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					#echo $sql;
					$query = $this->db->query($sql);

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$sql = "SELECT * FROM F5T13_RCV WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					#echo $sql;
					$query = $this->db->query($sql);

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}
					#var_dump($array1);

					$this->db->reconnect();
					$array2 = array();
					$sql = "SELECT * FROM F5T13_TERM WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' AND status = 1";
					#echo $sql;
					$query = $this->db->query($sql);

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$result = $this->check_diff_multi($array1, $array2);
					
					#var_dump($array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F5T12 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' AND status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F5T12 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', probationer = '".$value['probationer']."',docket_no='".$value['docket_no']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' ,case_classification = '".$value['case_classification'] ."' ,supervising_officer = '".$value['supervising_officer'] ."', status=1,source=2,created_by=0";
								#echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F5T12', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F5 T10 Done....";
					//

					//@F21

					//F21 T1-T2 -> T1
					//@F21

					//2022-02-27
					//F21 T1-T2 -> T1
					echo "\n\nTransferring to F21 T1 Started....";

					$query = $this->db->query("SELECT * FROM F21T1 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}



					$query = $this->db->query("SELECT *, petitioner_name as `petitioner`, received_date as `date_rcv`, investigating_officer_name as `investigating_officer`  FROM F21T2_RCV WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					#var_dump($array1);

					$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T1 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T1 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', date_rcv = '".$value['date_rcv'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T1', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T1 Done....";


					//F21 T3 & T4-> T3
					echo "\n\nTransferring to F21 T3 Started....";

					$query = $this->db->query("SELECT * FROM F21T3 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T2_ACTED WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T4 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T3 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T3 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', psir_rec = '".$value['psir_rec'] ."' ,psir_date = '".$value['psir_date'] ."' ,manifest = '".$value['manifest'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T3', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T3 Done....";

					echo "\n\nTransferring to F21 T5 Started....";

					$query = $this->db->query("SELECT * FROM F21T5 WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T6_RCV WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T6_CMPLTD WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T5 WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T5 SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', petitioner = '".$value['petitioner']."', referring_office = '".$value['referring_office'] ."' ,received_date = '".$value['received_date'] ."' ,reasons = '".$value['reasons'] ."' , investigating_officer='".$value['investigating_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T5', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T5 Done....";


					echo "\n\nTransferring to F21 T7 Pardon Started....";

					$query = $this->db->query("SELECT * FROM F21T7_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T8_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}



					$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					//$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}

					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T7_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and status = '1' and Y_M = '".$date_transfer."'");
						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T7_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T7_PARDON', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T7 PARDON Done....";

					echo "\n\nTransferring to F21 T7 PAROL Started....";

					$query = $this->db->query("SELECT * FROM F21T7_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T8_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}



					$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					//$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}

					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T7_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T7_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', case_classification = '".$value['case_classification'] ."' ,received_date = '".$value['received_date'] ."' ,probation_start = '".$value['probation_start'] ."' , probation_end = '".$value['probation_end'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T7_PAROL', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T7 PAROL Done....";


					echo "\n\nTransferring to F21 T10 PAROL Started....";

					$query = $this->db->query("SELECT * FROM F21T10_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					
					$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
						}
					}

					$query = $this->db->query("SELECT * FROM F21T11_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T10_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T10_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T10_PAROL', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T10 PAROL Done....";

					echo "\n\nTransferring to F21 T10 PARDON Started....";

					$query = $this->db->query("SELECT * FROM F21T10_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					
					$query = $this->db->query("SELECT *, disposed_decision as `submitted_decision`,disposed_date as `submitted_date` FROM F21T9_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
						}
					}

					$query = $this->db->query("SELECT * FROM F21T11_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T10_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T10_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T10_PARDON', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T10 PARDON Done....";


					echo "\n\nTransferring to F21 T12 PAROL Started....";

					$query = $this->db->query("SELECT * FROM F21T12_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T13_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T12_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T12_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T12_PAROL', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T12 PAROL Done....";


					echo "\n\nTransferring to F21 T12 PARDON Started....";

					$query = $this->db->query("SELECT * FROM F21T12_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T13_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T12_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = '1'");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T12_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', submitted_decision = '".$value['submitted_decision'] ."' ,submitted_date = '".$value['submitted_date'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T12_PARDON', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T12 PARDON Done....";


					echo "\n\nTransferring to F21 T14 PAROL Started....";

					$query = $this->db->query("SELECT * FROM F21T14_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							echo "\F21T14_PAROL\n";
							$data = $query->result_array();
							var_dump($data);
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T15_RCV_PAROL WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					#$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							echo "\nF21T15_RCV_PAROL\n";
							$data = $query->result_array();
							var_dump($data);
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T15_TERM_PAROL WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T14_PAROL WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T14_PAROL SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', reasons='".$value['reasons']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T14_PAROL', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T14 PAROL Done....";

					echo "\n\nTransferring to F21 T14 PARDON Started....";

					$query = $this->db->query("SELECT * FROM F21T14_PARDON WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

					$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}

					$query = $this->db->query("SELECT * FROM F21T15_RCV_PARDON WHERE field_office = '".$field_office."' and Y_M = '".$Y_M."' and status = '1'");

					#$array1 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array1, $value);
							}
							
						}
					}


					$query = $this->db->query("SELECT * FROM F21T15_TERM_PARDON WHERE field_office = '".$field_office."' and status = '1' and Y_M = '".$Y_M."'");

					$array2 = array();
					if($query){
						if($query->num_rows() > 0){
							$data = $query->result_array();
							foreach ($data as $key => $value) {
								array_push($array2, $value);
							}
							
						}
					}
					#var_dump($array2);
					$result = $this->check_diff_multi($array1, $array2);
					#var_dump($result);
					foreach($result as $key => $value){
						$this->db->reconnect();
						$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = 1");

						
						if($query_check){
							if($query_check->num_rows() > 0){
								echo "\n".$value['docket_no']." ALREADY EXISTS...";
							}else{
								//INSERT
								echo "\nINSERTING ".$value['docket_no']."...";
								$this->db->reconnect();
								$sql = "INSERT INTO F21T14_PARDON SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', probationer = '".$value['probationer']."', referral_office = '".$value['referral_office'] ."' ,received_date = '".$value['received_date'] ."' , case_classification = '".$value['case_classification'] ."' , supervising_officer='".$value['supervising_officer']."', status=1,source=2,created_by=0";
								// echo $sql;
								$query_insert = $this->db->query($sql);
								$this->db->close();

								//inserting to audit trail cron
								if ($query_insert) {
									$this->db->reconnect();
									$sql1 = "INSERT INTO audit_trail_carryover SET field_office = '".$field_office."', Y_M = '".$date_transfer."', docket_no='".$value['docket_no']."', origin='cron', status='1', form_table='F21T14_PARDON', start_date='".$datenow."'";
									$this->db->query($sql1);
								}

							}
						}
					}
					echo "\n\nTransferring to F21 T14 PARDON Done....";

				}
			}
			
		}	
	



		public function check_database()
		{
		    
		    ini_set('display_errors', 'On');
		    
		    //  Load the database config file.
		    if(file_exists($file_path = APPPATH.'config/database.php'))
		    {
		        include($file_path);
		    }
		    
		    $config = $db[$active_group];
		    
		    //  Check database connection if using mysqli driver
		    if( $config['dbdriver'] === 'mysqli' )
		    {
		        $mysqli = new mysqli( $config['hostname'] , $config['username'] , $config['password'] , $config['database'] );
		        if( !$mysqli->connect_error )
		        {
		            $mysqli->close();
		            return true;
		        }
		        
		        $mysqli->close();
		    }
		    
		    return false;
		} 



		public function cleanUpByFO(){

			$field_office = $_GET['field'];
			echo "\n\nCleanup to F5 T10 Started....\n";
			$query = $this->db->query("SELECT * FROM F5T11 where field_office = '".$field_office."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				$this->db->reconnect();

				//var_dump($array1);
				$Y_M = $value['Y_M'];
				$docket_no = $value['docket_no'];
				$field_office = $value['field_office'];

				$sql = "DELETE FROM F5T10 WHERE docket_no = '".$docket_no."' and Y_M > '".$Y_M."' and field_office = '".$field_office."'";

				echo $sql;
				$this->db->query($sql);
				echo "\n";

				/*$curr_date = strtotime(date($Y_M."-01"));
				#echo $curr_date;
				$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
				echo "\nDeleting date: ".$date_transfer."\n";*/

				//$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = 1");

				
			}


			echo "\n\n\Cleanup to F5 T3 Started....\n";
			$query = $this->db->query("SELECT * FROM F5T4 where field_office = '".$field_office."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				$this->db->reconnect();

				//var_dump($array1);
				$Y_M = $value['Y_M'];
				$docket_no = $value['docket_no'];
				$field_office = $value['field_office'];

				$sql = "DELETE FROM F5T3 WHERE docket_no = '".$docket_no."' and Y_M > '".$Y_M."' and field_office = '".$field_office."'";

				echo $sql;
				$this->db->query($sql);
				echo "\n";
				/*$curr_date = strtotime(date($Y_M."-01"));
				#echo $curr_date;
				$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
				echo "\nDeleting date: ".$date_transfer."\n";*/

				//$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = 1");

				
			}


			echo "\n\n\Cleanup to F5 T1 Started....\n";
			$query = $this->db->query("SELECT * FROM F5T2_ACTED where field_office = '".$field_office."'");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				$this->db->reconnect();

				//var_dump($array1);
				$Y_M = $value['Y_M'];
				$docket_no = $value['docket_no'];
				$field_office = $value['field_office'];

				$sql = "DELETE FROM F5T1 WHERE docket_no = '".$docket_no."' and Y_M > '".$Y_M."' and field_office = '".$field_office."'";

				echo $sql;
				$this->db->query($sql);
				echo "\n";

				
			}


		}



		public function cleanUpDuplicates(){
			$payload = (object)array("USER_LEVEL_ID"=>0,"FIELD_OFFICE"=>0);
			$fieldOffice = json_decode($this->Pis_model->getAllFieldOffices($payload));
			$datenow = date("Y-m-d H:i:s");
			#var_dump($fieldOffice);
			if($fieldOffice->status == "SUCCESS"){
				foreach($fieldOffice->payload as $keyFO => $valFO){
					$field_office = $valFO->NAME;
					$Y_M = $_GET['date'];

					$sql = "DELETE FROM F5T1 
							 WHERE id NOT IN (SELECT * 
	                    FROM (SELECT MIN(n1.id)
	                            FROM F5T1 n1
	                        	WHERE n1.Y_M = '".$Y_M."' and n1.field_office = '".$field_office."'
	                        GROUP BY n1.docket_no, n1.field_office) x)
	                        AND Y_M = '".$Y_M."' and field_office = '".$field_office."'";
	                echo $sql."\n";
	                $this->db->reconnect();
					$query = $this->db->query($sql);

				}
			}
		}

		public function cleanUp(){


			echo "\n\nCleanup to F5 T10 Started....\n";
			$query = $this->db->query("SELECT * FROM F5T11");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				$this->db->reconnect();

				//var_dump($array1);
				$Y_M = $value['Y_M'];
				$docket_no = $value['docket_no'];
				$field_office = $value['field_office'];

				$sql = "DELETE FROM F5T10 WHERE docket_no = '".$docket_no."' and Y_M > '".$Y_M."' and field_office = '".$field_office."'";

				echo $sql;
				$this->db->query($sql);
				echo "\n";

				/*$curr_date = strtotime(date($Y_M."-01"));
				#echo $curr_date;
				$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
				echo "\nDeleting date: ".$date_transfer."\n";*/

				//$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = 1");
				
			}


			echo "\n\nCleanup to F5 T7 Started....\n";
			$query = $this->db->query("SELECT * FROM F5T9");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				$this->db->reconnect();

				//var_dump($array1);
				$Y_M = $value['Y_M'];
				$docket_no = $value['docket_no'];
				$field_office = $value['field_office'];

				$sql = "DELETE FROM F5T7 WHERE docket_no = '".$docket_no."' and Y_M > '".$Y_M."' and field_office = '".$field_office."'";

				echo $sql;
				$this->db->query($sql);
				echo "\n";

				/*$curr_date = strtotime(date($Y_M."-01"));
				#echo $curr_date;
				$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
				echo "\nDeleting date: ".$date_transfer."\n";*/

				//$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = 1");
				
			}


			echo "\n\nCleanup to F5 T5 Started....\n";
			$query = $this->db->query("SELECT * FROM F5T6_CMPLTD");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
				}
			}
			foreach($array1 as $key => $value){
				$this->db->reconnect();

				$Y_M = $value['Y_M'];
				$docket_no = $value['docket_no'];
				$field_office = $value['field_office'];

				$sql = "DELETE FROM F5T5 WHERE docket_no = '".$docket_no."' and Y_M > '".$Y_M."' and field_office = '".$field_office."'";

				echo $sql;
				$this->db->query($sql);
				echo "\n";
			}


			echo "\n\n\Cleanup to F5 T3 Started....\n";
			$query = $this->db->query("SELECT * FROM F5T4");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				$this->db->reconnect();

				//var_dump($array1);
				$Y_M = $value['Y_M'];
				$docket_no = $value['docket_no'];
				$field_office = $value['field_office'];

				$sql = "DELETE FROM F5T3 WHERE docket_no = '".$docket_no."' and Y_M > '".$Y_M."' and field_office = '".$field_office."'";

				echo $sql;
				$this->db->query($sql);
				echo "\n";
				/*$curr_date = strtotime(date($Y_M."-01"));
				#echo $curr_date;
				$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));
				echo "\nDeleting date: ".$date_transfer."\n";*/

				//$query_check = $this->db->query("SELECT docket_no FROM F21T14_PARDON WHERE field_office = '".$field_office."' and docket_no ='".$value['docket_no']."' and Y_M = '".$date_transfer."' and status = 1");

				
			}



			echo "\n\n\Cleanup to F5 T1 Started....\n";
			$query = $this->db->query("SELECT * FROM F5T2_ACTED");

			$array1 = array();
			if($query){
				if($query->num_rows() > 0){
					$data = $query->result_array();
					foreach ($data as $key => $value) {
						array_push($array1, $value);
					}
					
				}
			}
			foreach($array1 as $key => $value){
				$this->db->reconnect();

				//var_dump($array1);
				$Y_M = $value['Y_M'];
				$docket_no = $value['docket_no'];
				$field_office = $value['field_office'];

				$sql = "DELETE FROM F5T1 WHERE docket_no = '".$docket_no."' and Y_M > '".$Y_M."' and field_office = '".$field_office."'";

				echo $sql;
				$this->db->query($sql);
				echo "\n";

				
			}


		}








	}



?>