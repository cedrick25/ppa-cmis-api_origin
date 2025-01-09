<?php 
	
class Cmis_F5T11_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function F5T11($payload)
	{
		if($payload != null)
		{
			switch ($payload->method) {
				case 'insert':
					$data = array();
					if(isset($payload->docket_no) && $payload->docket_no != null)
					{
						$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
						$data = array_merge($data, array('docket_no' => strtoupper($payload->docket_no)));
					}
					if(isset($payload->probationer) && $payload->probationer != null)
					{
						$data = array_merge($data, array('probationer' => strtoupper($payload->probationer)));
					}
					if(isset($payload->disposed_decision) && $payload->disposed_decision != null)
					{
						$data = array_merge($data, array('disposed_decision' => $payload->disposed_decision));
					}
					if(isset($payload->disposed_date) && $payload->disposed_date != null)
					{
						$data = array_merge($data, array('disposed_date' => $payload->disposed_date));
					}
					if(isset($payload->extension_probation) && $payload->extension_probation != null)
					{
						$data = array_merge($data, array('extension_probation' => $payload->extension_probation));
					}
					if(isset($payload->transfer) && $payload->transfer != null)
					{
						$data = array_merge($data, array('transfer' => $payload->transfer));
					}
					if(isset($payload->reason_other) && $payload->reason_other != null)
					{
						$data = array_merge($data, array('reason_other' => $payload->reason_other));
					}
					if(isset($payload->Y_M) && $payload->Y_M != null)
					{
						$data = array_merge($data, array('Y_M' => $payload->Y_M));
					}
					if(isset($payload->source) && $payload->source != null)
					{
						$data = array_merge($data, array('source' => $payload->source));
					}
					if(isset($payload->status) && $payload->status != null)
					{
						$data = array_merge($data, array('status' => $payload->status));
					}
					if(isset($payload->field_office) && $payload->field_office != null)
					{
						$data = array_merge($data, array('field_office' => $payload->field_office));
					}

					if(isset($payload->field_office_id) && !empty($payload->field_office_id))
					{
						$data = array_merge($data, array('field_office_id' => $payload->field_office_id));
					}
					if(isset($payload->created_date) && $payload->created_date != null)
					{
						$data = array_merge($data, array('created_date' => $payload->created_date));
					}else{
						$data = array_merge($data, array('created_date' => date("Y-m-d H:i:s")));
					}
					if(isset($payload->created_by) && $payload->created_by != null)
					{
						$data = array_merge($data, array('created_by' => $payload->created_by));
					}
					$insert = $this->db->insert('F5T11', $data);
					if($insert)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY INSERTED DATA'
						);
						$p2 = (object)array( "created_by" => $payload->created_by,
								"action" => "Added Data Form 5 Table 11.<br/> Payload: ". json_encode($payload),
								"module" => "CASELOAD" );
						$this->Cmis_Feedback_model->AuditInsert($p2);

						//DELETE NEXT MONTH
						$curr_date = strtotime(date($payload->Y_M."-01"));
						$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));

						$query = $this->db->query("DELETE FROM F5T10 WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");
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
					if(isset($payload->docket_no) && $payload->docket_no != null)
					{
						$this->db->set('docket_no', $payload->docket_no);
					}
					if(isset($payload->probationer) && $payload->probationer != null)
					{
						$this->db->set('probationer', strtoupper($payload->probationer));
					}
					if(isset($payload->disposed_decision) && $payload->disposed_decision != null)
					{
						$this->db->set('disposed_decision', $payload->disposed_decision);
					}
					if(isset($payload->disposed_date) && $payload->disposed_date != null)
					{
						$this->db->set('disposed_date', $payload->disposed_date);
					}
					if(isset($payload->extension_probation) && $payload->extension_probation != null)
					{
						$this->db->set('extension_probation', $payload->extension_probation);
					}
					if(isset($payload->transfer) && $payload->transfer != null)
					{
						$this->db->set('transfer', $payload->transfer);
					}else{
						$this->db->set('transfer', NULL);
					}
					if(isset($payload->reason_other) && $payload->reason_other != null)
					{
						$this->db->set('reason_other', $payload->reason_other);
					}else{
						$this->db->set('reason_other', NULL);
					}
					if(isset($payload->Y_M) && $payload->Y_M != null)
					{
						$this->db->set('Y_M', $payload->Y_M);
					}
					if(isset($payload->source) && $payload->source != null)
					{
						$this->db->set('source', $payload->source);
					}
					if(isset($payload->status) && $payload->status != null)
					{
						$this->db->set('status', $payload->status);
					}
					if(isset($payload->field_office) && $payload->field_office != null)
					{
						$this->db->set('field_office', $payload->field_office);
					}

					if(isset($payload->field_office_id) && !empty($payload->field_office_id))
					{
						$this->db->set('field_office_id', $payload->field_office_id);
					}
					if(isset($payload->created_date) && $payload->created_date != null)
					{
						$this->db->set('created_date', $payload->created_date);
					}
					if(isset($payload->created_by) && $payload->created_by != null)
					{
						$this->db->set('created_by', $payload->created_by);
					}
					if(isset($payload->id) && $payload->id != null)
					{
						$this->db->where('id', $payload->id);
						$requried_params--;
					}
					if($requried_params == 0)
					{
						$update = $this->db->update('F5T11');
						if($this->db->affected_rows() > 0)
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'SUCCESSFULLY UPDATED DATA'
							);
							$p2 = (object)array( "created_by" => $payload->created_by,
									"action" => "Updated Form 5 Table 11.<br/> Payload: ". json_encode($payload),
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
							'message' => 'INCOMPLETE PARAMTER'
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
					$sql = $this->db->get('F5T11');
					if($sql->num_rows() > 0 )
					{
						foreach($sql->result() as &$row) {
						    // $row->docket_no_display = "<a title='Click to view Docket Record'><span data-id='".$row->docket_no."' class='docket_link'>".$row->docket_no."</span></a>";
						    // $row->probationer = "<a ><span title='Click to open Petitioner FACT SHEET' data-id='".$row->docket_no."'  class='petitioner_link'>".$row->probationer."</span></a>";
						    $row->docket_no_display = $row->docket_no;
						    $row->probationer = $row->probationer;
						}
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'payload' => $sql->result()
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'ERROR FETCHING DATA'
						);
					}
					break;
				case 'fetchByID':
					
					if(isset($payload->id) && !empty($payload->id))
					{
						$this->db->where('id', $payload->id);
					}
					$get = $this->db->get('F5T11');
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
				case 'count':
					$sql = $this->db->get('F5T11');
					if($sql->num_rows() > 0 )
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY FETCHED DATA',
							'count' => count($sql->result())
						);
					}
					else
					{
						$response = array(
							'status' => 'ERROR',
							'message' => 'ERROR FETCHING DATA'
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
					$get = $this->db->get('F5T11');
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
	
}


?>