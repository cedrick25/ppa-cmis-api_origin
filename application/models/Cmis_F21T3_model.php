<?php 
	
class Cmis_F21T3_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function F21T3($payload)
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
					if(isset($payload->psir_rec) && $payload->psir_rec != null)
					{
						$data = array_merge($data, array('psir_rec' => $payload->psir_rec));
					}
					if(isset($payload->psir_date) && $payload->psir_date != null)
					{
						$data = array_merge($data, array('psir_date' => $payload->psir_date));
					}
					if(isset($payload->manifest) && $payload->manifest != null)
					{
						$data = array_merge($data, array('manifest' => $payload->manifest));
					}
					if(isset($payload->investigating_officer) && $payload->investigating_officer != null)
					{
						$data = array_merge($data, array('investigating_officer' => strtoupper($payload->investigating_officer)));
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
					$insert = $this->db->insert('F21T3', $data);
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
						$this->db->set('docket_no', strtoupper($payload->docket_no));
					}
					if(isset($payload->petitioner) && $payload->petitioner != null)
					{
						$this->db->set('petitioner', strtoupper($payload->petitioner));
					}
					if(isset($payload->psir_rec) && $payload->psir_rec != null)
					{
						$this->db->set('psir_rec', $payload->psir_rec);
					}
					if(isset($payload->psir_date) && $payload->psir_date != null)
					{
						$this->db->set('psir_date', $payload->psir_date);
					}
					if(isset($payload->manifest) && $payload->manifest != null)
					{
						$this->db->set('manifest', $payload->manifest);
					}
					if(isset($payload->investigating_officer) && $payload->investigating_officer != null)
					{
						$this->db->set('investigating_officer', $payload->investigating_officer);
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
						$update = $this->db->update('F21T3');
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
					$get = $this->db->get('F21T3');
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
					$get = $this->db->get('F21T3');
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
					$get = $this->db->get('F21T3');
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