<?php 
	
class Cmis_F5T6_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}


	//AUTOMATION
	public function upsertSF5T6_RCV($payload)
	{
		if($payload != null)
		{
			$check = $this->checkExistF5T6_RCV($payload);
			$response = array();
			
			if($check['status'] == 'SUCCESS'){
				$payload->method = "update";
				$response = json_decode($this->F5T6_RCV($payload));
			}else{
				$payload->method = "insert";
				#echo "INSERT";
				$response = json_decode($this->F5T6_RCV($payload));
				//exist do_insert
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

	public function checkExistF5T6_RCV($payload){
		if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL" )
		{	
			$this->db->where('field_office', $payload->field_office);
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->where('Y_M', $payload->Y_M);
		}
		$this->db->where('status',1);
		$this->db->where('docket_no',$payload->docket_no);
		$this->db->order_by("id","asc");
		$get = $this->db->get('F5T6_RCV');
		if($get->num_rows() > 0)
		{
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA',
				'count' => $get->num_rows()
			);
		}
		else
		{
			$response = array(
				'status' => 'ERROR',
				'message' => 'DATA NOT FOUND'
			);
		}
		return $response;
	}


	public function F5T6_RCV($payload)
	{
		if($payload != null)
		{
			switch ($payload->method) {
				case 'insert':
					$data = array();
					if(isset($payload->docket_no) && !empty($payload->docket_no))
					{
						$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
						$data = array_merge($data, array('docket_no' => strtoupper($payload->docket_no)));
					}
					if(isset($payload->petitioner) && !empty($payload->petitioner))
					{
						$data = array_merge($data, array('petitioner' => $payload->petitioner));
					}
					if(isset($payload->referring_office) && !empty($payload->referring_office))
					{
						$data = array_merge($data, array('referring_office' => $payload->referring_office));
					}
					if(isset($payload->received_date) && !empty($payload->received_date))
					{
						$data = array_merge($data, array('received_date' => $payload->received_date));
					}
					if(isset($payload->investigating_officer) && !empty($payload->investigating_officer))
					{
						$data = array_merge($data, array('investigating_officer' => $payload->investigating_officer));
					}
					if(isset($payload->reasons) && !empty($payload->reasons))
					{
						$data = array_merge($data, array('reasons' => $payload->reasons));
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$data = array_merge($data, array('Y_M' => $payload->Y_M));
					}
					if(isset($payload->source) && !empty($payload->source))
					{
						$data = array_merge($data, array('source' => $payload->source));
					}
					if(isset($payload->status) && !empty($payload->status))
					{
						$data = array_merge($data, array('status' => $payload->status));
					}

					if(isset($payload->field_office_id) && $payload->field_office_id != null)
					{
						$data = array_merge($data, array('field_office_id' => $payload->field_office_id));
					}


					if(isset($payload->created_date) && !empty($payload->created_date))
					{
						$data = array_merge($data, array('created_date' => $payload->created_date));
					}else{
						$data = array_merge($data, array('created_date' => date("Y-m-d H:i:s")));
					}
					if(isset($payload->created_by) && !empty($payload->created_by))
					{
						$data = array_merge($data, array('created_by' => $payload->created_by));
					}
					if(isset($payload->field_office) && !empty($payload->field_office))
					{
						$data = array_merge($data, array('field_office' => $payload->field_office));
					}
					$insert = $this->db->insert('F5T6_RCV', $data);
					if($insert)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS INSERTING DATA'
						);

						$p2 = (object)array( "created_by" => $payload->created_by,
								"action" => "Added Data Form 5 Table 6 recieved.<br/> Payload: ". json_encode($payload),
								"module" => "CASELOAD" );
						$this->Cmis_Feedback_model->AuditInsert($p2);

					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'ERROR INSERTING DATA'
						);
					}
					break;
				case 'update':
					$requried_params = 1;
					if(isset($payload->docket_no) && !empty($payload->docket_no))
					{
						$this->db->set('docket_no', $payload->docket_no);
					}
					if(isset($payload->petitioner) && !empty($payload->petitioner))
					{
						$this->db->set('petitioner', $payload->petitioner);
					}
					if(isset($payload->referring_office) && !empty($payload->referring_office))
					{
						$this->db->set('referring_office', $payload->referring_office);
					}
					if(isset($payload->received_date) && !empty($payload->received_date))
					{
						$this->db->set('received_date', $payload->received_date);
					}
					if(isset($payload->investigating_officer) && !empty($payload->investigating_officer))
					{
						$this->db->set('investigating_officer', $payload->investigating_officer);
					}
					if(isset($payload->reasons) && !empty($payload->reasons))
					{
						$this->db->set('reasons', $payload->reasons);
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$this->db->set('Y_M', $payload->Y_M);
					}
					if(isset($payload->source) && !empty($payload->source))
					{
						$this->db->set('source', $payload->source);
					}
					if(isset($payload->status) )
					{
						$this->db->set('status', $payload->status);
					}
					if(isset($payload->created_date) && !empty($payload->created_date))
					{
						$this->db->set('created_date', $payload->created_date);
					}
					if(isset($payload->created_by) && !empty($payload->created_by))
					{
						$this->db->set('created_by', $payload->created_by);
					}
					if(isset($payload->field_office) && !empty($payload->field_office))
					{
						$this->db->set('field_office', $payload->field_office);
					}

					if(isset($payload->field_office_id) && $payload->field_office_id != null)
					{
						$this->db->set('field_office_id', $payload->field_office_id);
					}


					if(isset($payload->id))
					{
						$this->db->where('id', $payload->id);
						$requried_params--;
					}else{
						$this->db->where('Y_M', $payload->Y_M);
						$this->db->where('docket_no', $payload->docket_no);
						$requried_params--;
					}
					if($requried_params == 0 )
					{
						$update = $this->db->update('F5T6_RCV');
						if($this->db->affected_rows() > 0 )
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'SUCCESS UPDATING DATA'
							);
							$p2 = (object)array( "created_by" => $payload->created_by,
									"action" => "Updated Form 5 Table 6 received.<br/> Payload: ". json_encode($payload),
									"module" => "CASELOAD" );
							$this->Cmis_Feedback_model->AuditInsert($p2);
						}
						else
						{
							$response = array(
								'status' => 'ERROR',
								'message' => 'NO DATA HAS BEEN UPDATED'
							);
						}
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'INCOMPLETE PARAMETER'
						);
					}
					break;
				case 'fetchAll':
					if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL")
					{	
						$this->db->where('field_office', $payload->field_office);
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$this->db->where('Y_M', $payload->Y_M);
					}
					$this->db->where('status',1);
					$this->db->order_by("docket_no","asc");
					$fetch = $this->db->get('F5T6_RCV');
					if($fetch->num_rows() > 0)
					{
						foreach($fetch->result() as &$row) {
						    // $row->docket_no_display = "<a title='Click to view Docket Record'><span data-id='".$row->docket_no."' class='docket_link'>".$row->docket_no."</span></a>";
						    // $row->petitioner = "<a ><span title='Click to open Petitioner FACT SHEET' data-id='".$row->docket_no."'  class='petitioner_link'>".$row->petitioner."</span></a>";

						    $row->docket_no_display = $row->docket_no;
						    $row->petitioner = $row->petitioner;
						}
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'payload' => $fetch->result()
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
				case 'count':
					$count = $this->db->get('F5T6_RCV');
					if($count->num_rows() > 0)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'count' => count($count->result())
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
				case 'fetchByDocketNoAndY_M':
					if(isset($payload->docket_no) && !empty($payload->docket_no))
					{
						$this->db->where('docket_no', $payload->docket_no);
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$this->db->where('Y_M', $payload->Y_M);
					}
					$get = $this->db->get('F5T6_RCV');
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
							'message' => 'NO DATA FOUND'
						);
					}
					break;
				case 'fetchByID':
					if(isset($payload->id) && !empty($payload->id))
					{
						$this->db->where('id', $payload->id);
					}
					$get = $this->db->get('F5T6_RCV');
					if($get->num_rows() > 0 )
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'payload' => $get->row()
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'NO DATA FOUND'
						);
					}
					break;
				default:
					$response = array(
						'status' => 'ERROR',
						'message' => 'METHOD CANNOT BE EMPTY'
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
	public function F5T6_CMPLTD($payload)
	{
		if($payload != null)
		{
			switch ($payload->method) {
				case 'insert':
					$data = array();
					if(isset($payload->docket_no) && !empty($payload->docket_no))
					{
						$data = array_merge($data, array('docket_no' => strtoupper($payload->docket_no)));
					}
					if(isset($payload->petitioner) && !empty($payload->petitioner))
					{
						$data = array_merge($data, array('petitioner' => $payload->petitioner));
					}
					if(isset($payload->completed_date) && !empty($payload->completed_date))
					{
						$data = array_merge($data, array('completed_date' => $payload->completed_date));
					}
					if(isset($payload->reasons) && !empty($payload->reasons))
					{
						$data = array_merge($data, array('reasons' => $payload->reasons));
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$data = array_merge($data, array('Y_M' => $payload->Y_M));
					}
					if(isset($payload->source) && !empty($payload->source))
					{
						$data = array_merge($data, array('source' => $payload->source));
					}
					if(isset($payload->status) && !empty($payload->status))
					{
						$data = array_merge($data, array('status' => $payload->status));
					}
					if(isset($payload->created_date) && !empty($payload->created_date))
					{
						$data = array_merge($data, array('created_date' => $payload->created_date));
					}
					if(isset($payload->created_by) && !empty($payload->created_by))
					{
						$data = array_merge($data, array('created_by' => $payload->created_by));
					}
					if(isset($payload->field_office) && !empty($payload->field_office))
					{
						$data = array_merge($data, array('field_office' => $payload->field_office));
					}
					if(isset($payload->field_office_id) && $payload->field_office_id != null)
					{
						$data = array_merge($data, array('field_office_id' => $payload->field_office_id));
					}
					$insert = $this->db->insert('F5T6_CMPLTD', $data);
					if($insert)
					{


					//DELETE NEXT MONTH
					$curr_date = strtotime(date($payload->Y_M."-01"));
					$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));

					$query = $this->db->query("DELETE FROM F5T5 WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");


						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS INSERTING DATA'
						);

						$p2 = (object)array( "created_by" => $payload->created_by,
								"action" => "Added Data Form 5 Table 6 completed.<br/> Payload: ". json_encode($payload),
								"module" => "CASELOAD" );
						$this->Cmis_Feedback_model->AuditInsert($p2);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'ERROR INSERTING DATA'
						);
					}	
					break;
				case 'update':
					$requried_params = 1;
					if(isset($payload->docket_no) && !empty($payload->docket_no))
					{
						$this->db->set('docket_no', $payload->docket_no);
					}
					if(isset($payload->petitioner) && !empty($payload->petitioner))
					{
						$this->db->set('petitioner', $payload->petitioner);
					}
					if(isset($payload->completed_date) && !empty($payload->completed_date))
					{
						$this->db->set('completed_date', $payload->completed_date);
					}
					if(isset($payload->reasons) && !empty($payload->reasons))
					{
						$this->db->set('reasons', $payload->reasons);
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$this->db->set('Y_M', $payload->Y_M);
					}
					if(isset($payload->source) && !empty($payload->source))
					{
						$this->db->set('source', $payload->source);
					}
					if(isset($payload->status))
					{
						$this->db->set('status', $payload->status);
					}
					if(isset($payload->created_date) && !empty($payload->created_date))
					{
						$this->db->set('created_date', $payload->created_date);
					}
					if(isset($payload->created_by) && !empty($payload->created_by))
					{
						$this->db->set('created_by', $payload->created_by);
					}
					if(isset($payload->field_office) && !empty($payload->field_office))
					{
						$this->db->set('field_office', $payload->field_office);
					}

					if(isset($payload->field_office_id) && $payload->field_office_id != null)
					{
						$this->db->set('field_office_id', $payload->field_office_id);
					}
					if(isset($payload->id))
					{
						$this->db->where('id', $payload->id);
						$requried_params--;
					}
					if($requried_params == 0 )
					{
						$update = $this->db->update('F5T6_CMPLTD');
						if($this->db->affected_rows() > 0 )
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'SUCCESS UPDATING DATA'
							);
							$p2 = (object)array( "created_by" => $payload->created_by,
									"action" => "Updated Form 5 Table 6 completed.<br/> Payload: ". json_encode($payload),
									"module" => "CASELOAD" );
							$this->Cmis_Feedback_model->AuditInsert($p2);
						}
						else
						{
							$response = array(
								'status' => 'ERROR',
								'message' => 'NO DATA HAS BEEN UPDATED'
							);
						}
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'INCOMPLETE PARAMETER'
						);
					}
					break;
				case 'fetchAll':
					if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL")
					{	
						$this->db->where('field_office', $payload->field_office);
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$this->db->where('Y_M', $payload->Y_M);
					}
					$this->db->where('status',1);
					$this->db->order_by("docket_no","asc");
					$fetch = $this->db->get('F5T6_CMPLTD');
					if($fetch->num_rows() > 0)
					{
						foreach($fetch->result() as &$row) {
						    // $row->docket_no_display = "<a title='Click to view Docket Record'><span data-id='".$row->docket_no."' class='docket_link'>".$row->docket_no."</span></a>";
						    // $row->petitioner = "<a ><span title='Click to open Petitioner FACT SHEET' data-id='".$row->docket_no."'  class='petitioner_link'>".$row->petitioner."</span></a>";
						    
						    $row->docket_no_display = $row->docket_no;
						    $row->petitioner = $row->petitioner;
						}
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'payload' => $fetch->result()
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
				case 'count':
					$fetch = $this->db->get('F5T6_CMPLTD');
					if($fetch->num_rows() > 0)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'count' => count($fetch->result())
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
				case 'fetchByDocketNoAndY_M':
					if(isset($payload->docket_no) && !empty($payload->docket_no))
					{
						$this->db->where('docket_no', $payload->docket_no);
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$this->db->where('Y_M', $payload->Y_M);
					}
					$get = $this->db->get('F5T6_CMPLTD');
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
							'message' => 'NO DATA FOUND'
						);
					}
					break;
				case 'fetchByID':
					if(isset($payload->id) && !empty($payload->id))
					{
						$this->db->where('id', $payload->id);
					}
					$get = $this->db->get('F5T6_CMPLTD');
					if($get->num_rows() > 0 )
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'payload' => $get->row()
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'NO DATA FOUND'
						);
					}
					break;
				default:
					# code...
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
}



?>