<?php 
	
class Cmis_F5T12_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	//AUTOMATION
	public function upsertSF5T12($payload)
	{
		if($payload != null)
		{
			$check = $this->checkExistF5T8($payload);
			$response = array();
			#var_dump($payload);
			#var_dump($check);
			if($check['status'] == 'SUCCESS'){
				$payload->method = "update";
				$response = json_decode($this->F5T12($payload));
			}else{
				$payload->method = "insert";
				#echo "INSERT";
				$response = json_decode($this->F5T12($payload));
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

	public function checkExistF5T12($payload){
		/*if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL" )
		{	
			$this->db->where('field_office', $payload->field_office);
		}*/
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->where('Y_M', $payload->Y_M);
		}
		$this->db->where('status',1);
		$this->db->where('docket_no',$payload->docket_no);
		$this->db->order_by("docket_no","asc");
		$get = $this->db->get('F5T12');
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

	public function F5T12($payload)
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
					if(isset($payload->referral_office) && $payload->referral_office != null)
					{
						$data = array_merge($data, array('referral_office' => $payload->referral_office));
					}
					if(isset($payload->received_date) && $payload->received_date != null)
					{
						$data = array_merge($data, array('received_date' => $payload->received_date));
					}
					if(isset($payload->case_classification) && $payload->case_classification != null)
					{
						$data = array_merge($data, array('case_classification' => $payload->case_classification));
					}
					if(isset($payload->supervising_officer) && $payload->supervising_officer != null)
					{
						$data = array_merge($data, array('supervising_officer' => strtoupper($payload->supervising_officer)));
					}
					if(isset($payload->reasons) && $payload->reasons != null)
					{
						$data = array_merge($data, array('reasons' => strtoupper($payload->reasons)));
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
					if(isset($payload->field_office_id) && $payload->field_office_id != null)
					{
						$data = array_merge($data, array('field_office_id' => $payload->field_office_id));
					}
					
					$insert = $this->db->insert('F5T12', $data);
					if($insert)
					{
						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY INSERTED DATA'
						);
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
					if(isset($payload->referral_office) && $payload->referral_office != null)
					{
						$this->db->set('referral_office', $payload->referral_office);
					}
					if(isset($payload->received_date) && $payload->received_date != null)
					{
						$this->db->set('received_date', $payload->received_date);
					}
					if(isset($payload->case_classification) && $payload->case_classification != null)
					{
						$this->db->set('case_classification', $payload->case_classification);
					}
					if(isset($payload->supervising_officer) && $payload->supervising_officer != null)
					{
						$this->db->set('supervising_officer', strtoupper($payload->supervising_officer));
					}
					if(isset($payload->reasons) && $payload->reasons != null)
					{
						$this->db->set('reasons', strtoupper($payload->reasons));
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
					if(isset($payload->field_office_id) && $payload->field_office_id != null)
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
					}else{
						$this->db->where('Y_M', $payload->Y_M);
						$this->db->where('docket_no', $payload->docket_no);
						$requried_params--;
					}

					if($requried_params == 0)
					{
						$update = $this->db->update('F5T12');
						if($this->db->affected_rows() > 0)
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'SUCCESSFULLY UPDATED DATA'
							);
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
					$sql = $this->db->get('F5T12');
					if($sql->num_rows() > 0 )
					{
						foreach($sql->result() as &$row) {
						    $row->docket_no_display = "<a title='Click to view Docket Record'><span data-id='".$row->docket_no."' class='docket_link'>".$row->docket_no."</span></a>";
						    $row->probationer = "<a ><span title='Click to open Petitioner FACT SHEET' data-id='".$row->docket_no."'  class='petitioner_link'>".$row->probationer."</span></a>";
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
					$get = $this->db->get('F5T12');
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
					$sql = $this->db->get('F5T12');
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
					$get = $this->db->get('F5T12');
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