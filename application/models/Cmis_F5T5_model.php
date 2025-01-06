<?php 
	
class Cmis_F5T5_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}


	public function F5T5($payload)
	{
		if(isset($payload) && $payload != null)
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

					if(isset($payload->field_office_id) && !empty($payload->field_office_id))
					{
						$data = array_merge($data, array('field_office_id' => $payload->field_office_id));
					}
					$insert = $this->db->insert('F5T5', $data);
					if($insert)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS INSERTING DATA'
						);

						$p2 = (object)array( "created_by" => $payload->created_by,
								"action" => "Added Data Form 5 Table 5.<br/> Payload: ". json_encode($payload),
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
						$this->db->set('docket_no', strtoupper($payload->docket_no));
					}
					if(isset($payload->petitioner) && !empty($payload->petitioner))
					{
						$this->db->set('petitioner', strtoupper($payload->petitioner));
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
						$this->db->set('reasons', strtoupper($payload->reasons));
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
					if(isset($payload->id))
					{
						$this->db->where('id', $payload->id);
						$requried_params--;
					}
					if($requried_params == 0 )
					{
						$update = $this->db->update('F5T5');
						if($this->db->affected_rows() > 0 )
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'SUCCESS UPDATING DATA'
							);
							$p2 = (object)array( "created_by" => $payload->created_by,
									"action" => "Updated Form 5 Table 5.<br/> Payload: ". json_encode($payload),
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
					if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL" )
					{	
						$this->db->where('field_office', $payload->field_office);
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$this->db->where('Y_M', $payload->Y_M);
					}
					$this->db->where('status',1);
					$this->db->order_by("id","asc");
					$fetch = $this->db->get('F5T5');
					if($fetch->num_rows() > 0)
					{
						foreach($fetch->result() as &$row) {
						    $row->docket_no_display = "<a title='Click to view Docket Record'><span data-id='".$row->docket_no."' class='docket_link'>".$row->docket_no."</span></a>";
						    $row->petitioner = "<a ><span title='Click to open Petitioner FACT SHEET' data-id='".$row->docket_no."'  class='petitioner_link'>".$row->petitioner."</span></a>";
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
					$count = $this->db->get('F5T5');
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
					$get = $this->db->get('F5T5');
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
					$get = $this->db->get('F5T5');
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
				'payload' => 'PLEASE CHECK YOUR DATA'
			);
		}
		return json_encode($response);
	}
}



?>