<?php 
	
class Cmis_F5T4_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}


	//AUTOMATION
	public function upsertSF5T4($payload)
	{
		if($payload != null)
		{
			$check = $this->checkExistF5T4($payload);
			$response = array();
			
			if($check['status'] == 'SUCCESS'){
				$payload->method = "update";
				$response = json_decode($this->F5T4($payload));
			}else{
				$payload->method = "insert";
				#echo "INSERT";
				$response = json_decode($this->F5T4($payload));
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


	public function checkExistF5T4($payload){
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
		$this->db->order_by("docket_no","asc");
		$get = $this->db->get('F5T4');
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


	public function F5T4($payload)
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
					if(isset($payload->petitioner) && $payload->petitioner != null)
					{
						$data = array_merge($data, array('petitioner' => strtoupper($payload->petitioner)));
					}
					if(isset($payload->fname) && $payload->fname != null)
					{
						$data = array_merge($data, array('fname' => $payload->fname));
					}
					if(isset($payload->mname) && $payload->mname != null)
					{
						$data = array_merge($data, array('mname' => $payload->mname));
					}
					if(isset($payload->lname) && $payload->lname != null)
					{
						$data = array_merge($data, array('lname' => $payload->lname));
					}
					if(isset($payload->suffixname) && $payload->suffixname != null)
					{
						$data = array_merge($data, array('suffixname' => $payload->suffixname));
					}
					if(isset($payload->alias) && $payload->alias != null)
					{
						$data = array_merge($data, array('alias' => $payload->alias));
					}
					if(isset($payload->disposed_decision) && $payload->disposed_decision != null)
					{
						$data = array_merge($data, array('disposed_decision' => $payload->disposed_decision));
					}
					if(isset($payload->disposed_date) && $payload->disposed_date != null)
					{
						$data = array_merge($data, array('disposed_date' => $payload->disposed_date));
					}
					if(isset($payload->Y_M) && $payload->Y_M != null)
					{
						$data = array_merge($data, array('Y_M' => $payload->Y_M));
					}
					if(isset($payload->reason_denial) && $payload->reason_denial != null)
					{
						$data = array_merge($data, array('reason_denial' => $payload->reason_denial));
					}
					if(isset($payload->other_types) && $payload->other_types != null)
					{
						$data = array_merge($data, array('other_types' => $payload->other_types));
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

					if(isset($payload->field_office_id) && $payload->field_office_id != null)
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

					


					$insert = $this->db->insert('F5T4', $data);
					if($insert)
					{


						//DELETE NEXT MONTH
						$curr_date = strtotime(date($payload->Y_M."-01"));
						$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));

						$query = $this->db->query("DELETE FROM F5T3 WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");

						$response = array(
							'status' => 'SUCCESS',
							'message' => 'SUCCESSFULLY INSERTED DATA'
						);

						$p2 = (object)array( "created_by" => $payload->created_by,
								"action" => "Added Data Form 5 Table 4.<br/> Payload: ". json_encode($payload),
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
					if(isset($payload->docket_no) && $payload->docket_no != null)
					{
						$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
						$this->db->set('docket_no', strtoupper($payload->docket_no));
					}
					if(isset($payload->petitioner) && $payload->petitioner != null)
					{
						$this->db->set('petitioner', strtoupper($payload->petitioner));
					}
					if(isset($payload->fname) && $payload->fname != null)
					{
						$this->db->set('fname', $payload->fname);
					}
					if(isset($payload->mname) && $payload->mname != null)
					{
						$this->db->set('mname', $payload->mname);
					}
					if(isset($payload->lname) && $payload->lname != null)
					{
						$this->db->set('lname', $payload->lname);
					}
					if(isset($payload->suffixname) && $payload->suffixname != null)
					{
						$this->db->set('suffixname', $payload->suffixname);
					}
					if(isset($payload->alias) && $payload->alias != null)
					{
						$this->db->set('alias', $payload->alias);
					}
					if(isset($payload->disposed_decision) && $payload->disposed_decision != null)
					{
						$this->db->set('disposed_decision', $payload->disposed_decision);
					}
					if(isset($payload->disposed_date) && $payload->disposed_date != null)
					{
						$this->db->set('disposed_date', $payload->disposed_date);
					}
					if(isset($payload->Y_M) && $payload->Y_M != null)
					{
						$this->db->set('Y_M', $payload->Y_M);
					}
					if(isset($payload->reason_denial) && $payload->reason_denial != null)
					{
						$this->db->set('reason_denial', $payload->reason_denial);
					}else{
						$this->db->set('reason_denial', NULL);
						
					}
					if(isset($payload->source) && $payload->source != null)
					{
						$this->db->set('source', $payload->source);
					}
					if(isset($payload->other_types) && $payload->other_types != null)
					{
						$this->db->set('other_types', $payload->other_types);
					}else{
						$this->db->set('other_types', NULL);
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
						$update = $this->db->update('F5T4');
						if($this->db->affected_rows() > 0)
						{
							$response = array(
								'status' => 'SUCCESS',
								'message' => 'SUCCESSFULLY UPDATED DATA'
							);
							$p2 = (object)array( "created_by" => $payload->created_by,
									"action" => "Updated Form 5 Table 4.<br/> Payload: ". json_encode($payload),
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
					if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL")
					{	
						$this->db->where('field_office', $payload->field_office);
					}
					if(isset($payload->Y_M) && !empty($payload->Y_M))
					{
						$this->db->where('Y_M', $payload->Y_M);
					}
					$this->db->where('status',1);
					$this->db->order_by("id","asc");
					$sql = $this->db->get('F5T4');
					if($sql->num_rows() > 0 )
					{
						foreach($sql->result() as &$row) {
						    // $row->docket_no_display = "<a title='Click to view Docket Record'><span data-id='".$row->docket_no."' class='docket_link'>".$row->docket_no."</span></a>";
						    // $row->petitioner = "<a ><span title='Click to open Petitioner FACT SHEET' data-id='".$row->docket_no."'  class='petitioner_link'>".$row->petitioner."</span></a>";
						    
						    $row->docket_no_display = $row->docket_no;
						    $row->petitioner = $row->petitioner;
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
						/*$error = $this->db->error(); // Has keys 'code' and 'message'
						print_r($error);*/
					}
					break;
				case 'count':
					$sql = $this->db->get('F5T4');
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
					$get = $this->db->get('F5T4');
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
					$get = $this->db->get('F5T4');
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

}



?>