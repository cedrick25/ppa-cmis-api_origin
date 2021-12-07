<?php 
	
class Cmis_F21T2_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function F21T2_ACTED($payload)
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
					if(isset($payload->petitioner_name) && $payload->petitioner_name != null)
					{
						$data = array_merge($data, array('petitioner_name' => strtoupper($payload->petitioner_name)));
					}
					if(isset($payload->psir_date) && $payload->psir_date != null)
					{
						$data = array_merge($data, array('psir_date' => $payload->psir_date));
					}
					if(isset($payload->ppo_recommendation) && $payload->ppo_recommendation != null)
					{
						$data = array_merge($data, array('ppo_recommendation' => $payload->ppo_recommendation));
					}
					if(isset($payload->transfer_date) && $payload->transfer_date != null)
					{
						$data = array_merge($data, array('transfer_date' => $payload->transfer_date));
					}
					if(isset($payload->transfer_to) && $payload->transfer_to != null)
					{
						$data = array_merge($data, array('transfer_to' => $payload->transfer_to));
					}
					if(isset($payload->Y_M) && $payload->Y_M != null)
					{
						$data = array_merge($data, array('Y_M' => $payload->Y_M));
					}
					if(isset($payload->source) && $payload->source != null)
					{
						$data = array_merge($data, array('source' => $payload->source));
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
					$insert = $this->db->insert('F21T2_ACTED', $data);
					if($insert)
					{	
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESS INSERTING DATA'
						);
						//DELETE NEXT MONTH
						$curr_date = strtotime(date($payload->Y_M."-01"));
						$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));

						$query = $this->db->query("DELETE FROM F21T1 WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");

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
					if(isset($payload->docket_no) && $payload->docket_no != null)
					{
						$this->db->set('docket_no', strtoupper($payload->docket_no));
					}
					if(isset($payload->petitioner_name) && $payload->petitioner_name != null)
					{
						$this->db->set('petitioner_name', strtoupper($payload->petitioner_name));
					}
					if(isset($payload->psir_date) && $payload->psir_date != null)
					{
						$this->db->set('psir_date', $payload->psir_date);
					}else{
						$this->db->set('psir_date', null);
					}
					if(isset($payload->ppo_recommendation) && $payload->ppo_recommendation != null)
					{
						$this->db->set('ppo_recommendation', $payload->ppo_recommendation);
					}else{
						$this->db->set('ppo_recommendation', null);
					}
					if(isset($payload->transfer_date) && $payload->transfer_date != null)
					{
						$this->db->set('transfer_date', $payload->transfer_date);
					}else{
						$this->db->set('transfer_date', null);
					}
					if(isset($payload->transfer_to) && $payload->transfer_to != null)
					{
						$this->db->set('transfer_to', $payload->transfer_to);
					}else{
						$this->db->set('transfer_to', null);
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
						$update = $this->db->update('F21T2_ACTED');
						if($this->db->affected_rows() > 0)
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'SUCCESSFULLY UPDATED DATA!'
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
							'message' => 'PLEASE FILL UP THE REQUIRED DATA!'
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
					$get = $this->db->get('F21T2_ACTED');
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
					$get = $this->db->get('F21T2_ACTED');
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
					$get = $this->db->get('F21T2_ACTED');
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

	public function F21T2_RCV($payload)
	{
		if($payload != null)
		{
			switch ($payload->method) {
				case 'insert':
					$data = array();
					if(isset($payload->docket_no) && $payload->docket_no != null)
					{
						$data = array_merge($data, array('docket_no' => $payload->docket_no));
					}
					if(isset($payload->case_no) && $payload->case_no != null)
					{
						$data = array_merge($data, array('case_no' => $payload->case_no));
					}
					if(isset($payload->prison_name) && $payload->prison_name != null)
					{
						$data = array_merge($data, array('prison_name' => $payload->prison_name));
					}

					if(isset($payload->prison_type) && $payload->prison_type != null)
					{
						$data = array_merge($data, array('prison_type' => $payload->prison_type));
					}
					if(isset($payload->offense) && $payload->offense != null)
					{
						$data = array_merge($data, array('offense' => $payload->offense));
					}
					if(isset($payload->received_date) && $payload->received_date != null)
					{
						$data = array_merge($data, array('received_date' => $payload->received_date));
					}
					if(isset($payload->petitioner_name) && $payload->petitioner_name != null)
					{
						$data = array_merge($data, array('petitioner_name' => $payload->petitioner_name));
					}
					if(isset($payload->investigating_officer_name) && $payload->investigating_officer_name != null)
					{
						$data = array_merge($data, array('investigating_officer_name' => $payload->investigating_officer_name));
					}
					if(isset($payload->Y_M) && $payload->Y_M != null)
					{
						$data = array_merge($data, array('Y_M' => $payload->Y_M));
					}
					if(isset($payload->source) && $payload->source != null)
					{
						$data = array_merge($data, array('source' => $payload->source));
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
					$insert = $this->db->insert('F21T2_RCV', $data);
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
					if(isset($payload->docket_no) && $payload->docket_no != null)
					{
						$this->db->set('docket_no', $payload->docket_no);
					}
					if(isset($payload->case_no) && $payload->case_no != null)
					{
						$this->db->set('case_no', $payload->case_no);
					}
					if(isset($payload->prison_name) && $payload->prison_name != null)
					{
						$this->db->set('prison_name', $payload->prison_name);
					}else{

						$this->db->set('prison_name', NULL);
					}
					
					if(isset($payload->prison_type) && $payload->prison_type != null)
					{
						$this->db->set('prison_type', $payload->prison_type);
					}else{

						$this->db->set('prison_type', NULL);
					}


					if(isset($payload->offense) && $payload->offense != null)
					{
						$this->db->set('offense', $payload->offense);
					}
					if(isset($payload->received_date) && $payload->received_date != null)
					{
						$this->db->set('received_date', $payload->received_date);
					}
					if(isset($payload->petitioner_name) && $payload->petitioner_name != null)
					{
						$this->db->set('petitioner_name', $payload->petitioner_name);
					}
					if(isset($payload->investigating_officer_name) && $payload->investigating_officer_name != null)
					{
						$this->db->set('investigating_officer_name', $payload->investigating_officer_name);
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
						$update = $this->db->update('F21T2_RCV');
						if($this->db->affected_rows() > 0)
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'SUCCESSFULLY UPDATED DATA!'
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
							'message' => 'PLEASE FILL UP THE REQUIRED DATA!'
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
					$get = $this->db->get('F21T2_RCV');
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
					$get = $this->db->get('F21T2_RCV');
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
					$get = $this->db->get('F21T2_RCV');
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

}



?>