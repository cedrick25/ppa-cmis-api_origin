<?php 
	
class Cmis_F21T9_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function F21T9($payload)
	{
		if($payload != null)
		{
			switch ($payload->method) {
				case 'insert':
					if(isset($payload->table) && $payload->table != null )
					{
						$data = array();
						if(isset($payload->docket_no) && $payload->docket_no != null)
						{
							$data = array_merge($data, array('docket_no' => $payload->docket_no));
						}
						if(isset($payload->probationer) && $payload->probationer != null)
						{
							$data = array_merge($data, array('probationer' => $payload->probationer));
						}
						if(isset($payload->disposed_decision) && $payload->disposed_decision != null)
						{
							$data = array_merge($data, array('disposed_decision' => $payload->disposed_decision));
						}
						if(isset($payload->disposed_date) && $payload->disposed_date != null)
						{
							$data = array_merge($data, array('disposed_date' => $payload->disposed_date));
						}
						if(isset($payload->transfer) && $payload->transfer != null)
						{
							$data = array_merge($data, array('transfer' => $payload->transfer));
						}
						if(isset($payload->Y_M) && $payload->Y_M != null)
						{
							$data = array_merge($data, array('Y_M' => $payload->Y_M));
						}
						if(isset($payload->source) && $payload->source != null)
						{
							$data = array_merge($data, array('source' => $payload->source));
						}

						if(isset($payload->submitted_report) && $payload->submitted_report != null)
						{
							$data = array_merge($data, array('submitted_report' => $payload->submitted_report));
						}

						if(isset($payload->others) && $payload->others != null)
						{
							$data = array_merge($data, array('others' => $payload->others));
						}

						if(isset($payload->created_by) && $payload->created_by != null)
						{
							$data = array_merge($data, array('created_by' => $payload->created_by));
							$data = array_merge($data, array('created_date' => date('Y-m-d H:i:s')));
						}
						if(isset($payload->status) && $payload->status != null)
						{
							$data = array_merge($data, array('status' => $payload->status));
						}
						if(isset($payload->field_office) && $payload->field_office != null)
						{
							$data = array_merge($data, array('field_office' => $payload->field_office));
						}
						$insert = $this->db->insert("".$payload->table."", $data);
						if($insert)
						{

							$p2 = (object)array( "created_by" => $payload->created_by,
									"action" => "Added Data Form 21 Table 9.<br/> Payload: ". json_encode($payload),
									"module" => "CASELOAD" );
							$this->Cmis_Feedback_model->AuditInsert($p2);	
							//DELETE NEXT MONTH
							$curr_date = strtotime(date($payload->Y_M."-01"));
							$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));

							$query = $this->db->query("DELETE FROM F21T7_PARDON WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");

							$query = $this->db->query("DELETE FROM F21T7_PAROL WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");

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
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'TABLE CANNOT BE EMPTY!'
						);
					}
					break;
				case 'update':
					if(isset($payload->table) && $payload->table != null )
					{
						$required_param = 1;
						if(isset($payload->docket_no) && $payload->docket_no != null)
						{
							$this->db->set('docket_no', $payload->docket_no);
						}
						if(isset($payload->probationer) && $payload->probationer != null)
						{
							$this->db->set('probationer', $payload->probationer);
						}
						if(isset($payload->disposed_decision) && $payload->disposed_decision != null)
						{
							$this->db->set('disposed_decision', $payload->disposed_decision);
						}
						if(isset($payload->disposed_date) && $payload->disposed_date != null)
						{
							$this->db->set('disposed_date', $payload->disposed_date);
						}
						if(isset($payload->transfer) && $payload->transfer != null)
						{
							$this->db->set('transfer', $payload->transfer);
						}
						if(isset($payload->Y_M) && $payload->Y_M != null)
						{
							$this->db->set('Y_M', $payload->Y_M);
						}
						if(isset($payload->source) && $payload->source != null)
						{
							$this->db->set('source', $payload->source);
						}
						if(isset($payload->created_by) && $payload->created_by != null)
						{
							$this->db->set('created_by', $payload->created_by);
						}
						if(isset($payload->status) && $payload->status != null)
						{
							$this->db->set('status', $payload->status);
						}

						if(isset($payload->others) && $payload->others != null)
						{
							$this->db->set('others', $payload->others);
						}else{
							$this->db->set('others', NULL);
						}

						if(isset($payload->submitted_report) && $payload->submitted_report != null)
						{
							$this->db->set('submitted_report', $payload->submitted_report);
						}else{
							$this->db->set('submitted_report', NULL);
						}

						if(isset($payload->field_office) && $payload->field_office != null)
						{
							$this->db->set('field_office', $payload->field_office);
						}
						if(isset($payload->id) && $payload->id != null)
						{
							$this->db->where('id', $payload->id);
							$required_param--;
						}
						if($required_param == 0)
						{
							$update = $this->db->update("".$payload->table."");
							if($this->db->affected_rows() > 0)
							{
								$response = array(
									'status' => 'SUCCESS',
									'message' => 'SUCCESSFULLY UPDATED DATA!'
								);
								$p2 = (object)array( "created_by" => $payload->created_by,
										"action" => "Updated Form 21 Table 9.<br/> Payload: ". json_encode($payload),
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
								'message' => 'PLEASE FILL UP THE REQUIRED DATA!'
							);
						}
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'TABLE CANNOT BE EMPTY!'
						);
					}
					break;
				case 'count':
					if(isset($payload->table) && $payload->table != null )
					{
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
						$get = $this->db->get("".$payload->table."");
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
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'TABLE CANNOT BE EMPTY!'
						);
					}
					break;
				case 'fetchAll':
					if(isset($payload->table) && $payload->table != null )
					{
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
						$get = $this->db->get("".$payload->table."");
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
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'TABLE CANNOT BE EMPTY!'
						);
					}
					break;
				case 'fetchByID':
					if(isset($payload->table) && $payload->table != null )
					{
						if(isset($payload->id) && $payload->id != null )
						{
							$this->db->where('id', $payload->id);
						}
						$get = $this->db->get("".$payload->table."");
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
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'TABLE CANNOT BE EMPTY!'
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
	

}



?>