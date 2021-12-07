<?php 
	
class Cmis_F5T1_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}


	public function validateDocket($payload)
	{	
		$found = 0;
		if(isset($payload->docket_no) && !empty($payload->docket_no)  ){
			foreach ($payload->checkTable as $key => $value) {
				//var_dump($value);
				if(isset($payload->Y_M) && !empty($payload->Y_M))
				{
					$this->db->like('Y_M', $payload->Y_M);
				}
				$this->db->where('status', 1);
				if(isset($payload->field_office) && !empty($payload->field_office) && ($payload->field_office != "ALL") )
				{
					$this->db->where('field_office', $payload->field_office);
				}
				if(isset($payload->docket_no) && !empty($payload->docket_no)  )
				{
					$this->db->like('docket_no', $payload->docket_no);
				}
				

				
				$sql = $this->db->get($value);
				if($sql->num_rows() > 0 )
				{
					$found += 1;
					break;
				}
				
			}
		}
		if($found > 0 )
		{
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS'
			);
		}
		else
		{
			$response = array(
				'status' => 'ERROR',
				'message' => 'DATA NOT FOUND'
			);
		}
		
		return json_encode($response);
	}

	public function fetchF5T1ByYM($payload)
	{
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->like('Y_M', $payload->Y_M);
		}
		$this->db->where('status', 1);
		if(isset($payload->field_office) && !empty($payload->field_office) && ($payload->field_office != "ALL") )
		{
			$this->db->where('field_office', $payload->field_office);
		}
		$this->db->order_by("id","asc");
		//$this->db->order_by("date_rcv","asc");
		

		
		$sql = $this->db->get('F5T1');
		if($sql->num_rows() > 0 )
		{

			$output = array();
			foreach($sql->result() as &$row) {
			    $row->docket_no_display = "<span data-id='".$row->docket_no."' class='docket_link'>".$row->docket_no."</span>";
			    $row->petitioner = "<a ><span data-id='".$row->docket_no."'  class='petitioner_link'>".$row->petitioner."</span></a>";
			    $output[] = $row;
			}



			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA ON F5T1 TABLE',
				'payload' => $output
			);
		}
		else
		{
			$response = array(
				'status' => 'ERROR',
				'message' => 'DATA NOT FOUND'
			);
		}
		return json_encode($response);
	}


	public function fetchF5T1ByID($payload)
	{
		if(isset($payload->ID) && !empty($payload->ID))
		{
			$this->db->where('ID', $payload->ID);
		}
		$sql = $this->db->get('F5T1');
		if($sql->num_rows() > 0 )
		{
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA ON F5T1 TABLE',
				'payload' => $sql->row()
			);
		}
		else
		{
			$response = array(
				'status' => 'ERROR',
				'message' => 'DATA NOT FOUND'
			);
		}
		return json_encode($response);
	}


	public function countF5T1ByYM($payload)
	{
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->like('Y_M', $payload->Y_M);
		}

		

		$this->db->where('status', 1);
		$sql = $this->db->get('F5T1');
		if($sql->num_rows() > 0 )
		{
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA ON F5T1 TABLE',
				'payload' => count($sql->result())
			);
		}
		else
		{
			$response = array(
				'status' => 'ERROR',
				'message' => 'DATA NOT FOUND'
			);
		}
		return json_encode($response);
	}
	public function updateF5T1($payload)
	{
		if(isset($payload->docket_no) && !empty($payload->docket_no))
		{
			$this->db->set('docket_no', strtoupper($payload->docket_no));
		}
		if(isset($payload->petitioner) && !empty($payload->petitioner))
		{
			$this->db->set('petitioner', strtoupper($payload->petitioner));
		}
		if(isset($payload->date_rcv) && !empty($payload->date_rcv))
		{
			$this->db->set('date_rcv', strtoupper($payload->date_rcv));
		}
		if(isset($payload->investigating_officer) && !empty($payload->investigating_officer))
		{
			$this->db->set('investigating_officer', strtoupper($payload->investigating_officer));
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->set('Y_M', strtoupper($payload->Y_M));
		}
		if(isset($payload->source) && !empty($payload->source))
		{
			$this->db->set('source', strtoupper($payload->source));
		}

		if(isset($payload->field_office) && !empty($payload->field_office))
		{
			$this->db->set('field_office', ($payload->field_office));
		}
		if(isset($payload->status) && ($payload->status != ""))
		{
			$this->db->set('status', strtoupper($payload->status));
		}

		


		$this->db->where('id', $payload->id);
		$update = $this->db->update('F5T1');
		if($this->db->affected_rows() > 0 )
		{
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS UPDATING DATA'
			);


			$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Updated Form 5 Table 1.<br/> Payload: ". json_encode($payload),
					"module" => "CASELOAD" );
			$this->Cmis_Feedback_model->AuditInsert($p2);

		}
		else
		{
			$response = array(
				'status' => 'ERROR',
				'message' => 'ERROR UPDATING DATA'
			);
		}
		return json_encode($response);
	}


	public function upsertF5T1($payload)
	{
		$data = array();
		if(isset($payload->docket_no) && !empty($payload->docket_no))
		{
			$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
			$data = array_merge($data, array('docket_no' => strtoupper($payload->docket_no)));
		}
		if(isset($payload->petitioner) && !empty($payload->petitioner))
		{
			$data = array_merge($data, array('petitioner' => strip_tags(strtoupper($payload->petitioner))));
		}
		if(isset($payload->date_rcv) && !empty($payload->date_rcv))
		{
			$data = array_merge($data, array('date_rcv' => strtoupper($payload->date_rcv)));
		}
		if(isset($payload->investigating_officer) && !empty($payload->investigating_officer))
		{
			$data = array_merge($data, array('investigating_officer' => strtoupper($payload->investigating_officer)));
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$data = array_merge($data, array('Y_M' => strtoupper($payload->Y_M)));
		}
		if(isset($payload->source) && !empty($payload->source))
		{
			$data = array_merge($data, array('source' => strtoupper($payload->source)));
		}

		if(isset($payload->field_office) && !empty($payload->field_office))
		{
			$data = array_merge($data, array('field_office' => $payload->field_office));
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

		$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Added Data to Form 5 Table 1.<br/> Payload: ". json_encode($payload),
					"module" => "CASELOAD" );
		$this->Cmis_Feedback_model->AuditInsert($p2);




		
		
		$sql = $this->db->insert('F5T1', $data);
		if($sql)
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
				'message' => 'ERROR INSERTING DATA'
			);
		}

		return json_encode($response);
	}

}



?>