<?php 
	
class Cmis_F21T5_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function F21T5($payload)
	{
		if($payload != null)
		{
			switch ($payload->method) {
				case 'insert':
					$data = array();
					if(isset($payload->docket_no) && $payload->docket_no != null)
					{
						$data = array_merge($data, array('docket_no' => strtoupper($payload->docket_no)));
					}
					if(isset($payload->petitioner) && $payload->petitioner != null)
					{
						$data = array_merge($data, array('petitioner' => strtoupper($payload->petitioner)));
					}
					if(isset($payload->referring_office) && $payload->referring_office != null)
					{
						$data = array_merge($data, array('referring_office' => $payload->referring_office));
					}
					if(isset($payload->received_date) && $payload->received_date != null)
					{
						$data = array_merge($data, array('received_date' => $payload->received_date));
					}
					if(isset($payload->investigating_officer) && $payload->investigating_officer != null)
					{
						$data = array_merge($data, array('investigating_officer' => $payload->investigating_officer));
					}
					if(isset($payload->reasons) && $payload->reasons != null)
					{
						$data = array_merge($data, array('reasons' => $payload->reasons));
					}
					if(isset($payload->Y_M) && $payload->Y_M != null)
					{
						$data = array_merge($data, array('Y_M' => $payload->Y_M));
					}
					if(isset($payload->source) && $payload->source != null)
					{
						$data = array_merge($data, array('source' => $payload->source));
					}
					if(isset($payload->field_office) && $payload->field_office != null)
					{
						$data = array_merge($data, array('field_office' => $payload->field_office));
					}
					if(isset($payload->status) && $payload->status != null)
					{
						$data = array_merge($data, array('status' => $payload->status));
					}
					if(isset($payload->created_by) && $payload->created_by != null)
					{
						$data = array_merge($data, array('created_by' => $payload->created_by));
						$data = array_merge($data, array('created_date' => date('Y-m-d H:i:s')));
					}
					$insert = $this->db->insert('F21T5', $data);
					if($insert)
					{	
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS INSERTING DATA'
						);
						$p2 = (object)array( "created_by" => $payload->created_by,
								"action" => "Added Data Form 21 Table 5.<br/> Payload: ". json_encode($payload),
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
					$required_param = 1;
					if(isset($payload->docket_no) && $payload->docket_no != null)
					{
						$this->db->set('docket_no', strtoupper($payload->docket_no));
					}
					if(isset($payload->petitioner) && $payload->petitioner != null)
					{
						$this->db->set('petitioner', strtoupper($payload->petitioner));
					}
					if(isset($payload->referring_office) && $payload->referring_office != null)
					{
						$this->db->set('referring_office', $payload->referring_office);
					}
					if(isset($payload->received_date) && $payload->received_date != null)
					{
						$this->db->set('received_date', $payload->received_date);
					}
					if(isset($payload->investigating_officer) && $payload->investigating_officer != null)
					{
						$this->db->set('investigating_officer', $payload->investigating_officer);
					}
					if(isset($payload->reasons) && $payload->reasons != null)
					{
						$this->db->set('reasons', $payload->reasons);
					}
					if(isset($payload->Y_M) && $payload->Y_M != null)
					{
						$this->db->set('Y_M', $payload->Y_M);
					}
					if(isset($payload->source) && $payload->source != null)
					{
						$this->db->set('source', $payload->source);
					}
					if(isset($payload->field_office) && $payload->field_office != null)
					{
						$this->db->set('field_office', $payload->field_office);
					}
					if(isset($payload->status) && $payload->status != null)
					{
						$this->db->set('status', $payload->status);
					}
					if(isset($payload->created_by) && $payload->created_by != null)
					{
						$this->db->set('created_by', $payload->created_by);
					}
					if(isset($payload->id) && $payload->id != null)
					{
						$this->db->where('id', $payload->id);
						$required_param--;
					}
					if($required_param == 0 )
					{
						$update = $this->db->update('F21T5');
						if($this->db->affected_rows() > 0 )
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'SUCCESSFULLY UPDATED DATA!'
							);
							$p2 = (object)array( "created_by" => $payload->created_by,
									"action" => "Updated Form 21 Table 5.<br/> Payload: ". json_encode($payload),
									"module" => "CASELOAD" );
							$this->Cmis_Feedback_model->AuditInsert($p2);
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
							'message' => 'PLEASE FILL UP ALL THE REQUIRED PARAMTER'
						);
					}
					break;
				case 'count':
					if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL" )
					{	
						$this->db->where('field_office', $payload->field_office);
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$this->db->where('Y_M', $payload->Y_M);
					}
					$this->db->where('status',1);
					$this->db->order_by("docket_no","asc");
					$get = $this->db->get('F21T5');
					if($get->num_rows() > 0)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS FETCHING DATA',
							'count' => count($get->result())
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
					$get = $this->db->get('F21T5');
					if($get->num_rows() > 0)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS FETCHING DATA',
							'payload' => $get->result()
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
				case 'fetchByID':
					if(isset($payload->id) && $payload->id != null )
					{
						$this->db->where('id', $payload->id);
					}
					$get = $this->db->get('F21T5');
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
						'message' => 'METHOD CANNOT BE EMPLTY!'
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

}



?>