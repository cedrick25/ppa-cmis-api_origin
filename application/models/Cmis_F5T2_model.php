<?php 
	
class Cmis_F5T2_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}


	public function upsertReferralsReceived($payload)
	{
		
		$data = array();
		if(isset($payload->docket_no) && !empty($payload->docket_no))
		{	
			$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
			$data = array_merge($data, array('docket_no' => strtoupper($payload->docket_no)));
		}
		if(isset($payload->case_no) && !empty($payload->case_no))
		{	
			$data = array_merge($data, array('case_no' => strtoupper($payload->case_no)));
		}
		if(isset($payload->plea_bargain) && !empty($payload->plea_bargain))
		{	
			$data = array_merge($data, array('plea_bargain' => strtoupper($payload->plea_bargain)));
		}
		if(isset($payload->court_origin) && !empty($payload->court_origin))
		{	
			$data = array_merge($data, array('court_origin' => strtoupper($payload->court_origin)));
		}
		if(isset($payload->offense) && !empty($payload->offense))
		{	
			$data = array_merge($data, array('offense' => strtoupper($payload->offense)));
		}
		if(isset($payload->sentence) && !empty($payload->sentence))
		{	
			$data = array_merge($data, array('sentence' => strtoupper($payload->sentence)));
		}
		if(isset($payload->date_of_court_order) && !empty($payload->date_of_court_order))
		{	
			$data = array_merge($data, array('date_of_court_order' => strtoupper($payload->date_of_court_order)));
		}
		if(isset($payload->received_date) && !empty($payload->received_date))
		{	
			$data = array_merge($data, array('received_date' => strtoupper($payload->received_date)));
		}
		if(isset($payload->petitioner_name) && !empty($payload->petitioner_name))
		{	
			$data = array_merge($data, array('petitioner_name' => strtoupper($payload->petitioner_name)));
		}
		if(isset($payload->investigating_officer_name) && !empty($payload->investigating_officer_name))
		{	
			$data = array_merge($data, array('investigating_officer_name' => strtoupper($payload->investigating_officer_name)));
		}
		if(isset($payload->created_date) && !empty($payload->created_date))
		{	
			$data = array_merge($data, array('created_date' => $payload->created_date));
		}else{
			$data = array_merge($data, array('created_date' => date("Y-m-d H:i:s")));
		}
		if(isset($payload->created_by) && !empty($payload->created_by))
		{	
			$data = array_merge($data, array('created_by' => strtoupper($payload->created_by)));
		}
		if(isset($payload->field_office) && !empty($payload->field_office))
		{	
			$data = array_merge($data, array('field_office' => ($payload->field_office)));
		}

		if(isset($payload->field_office_id) && !empty($payload->field_office_id))
		{	
			$data = array_merge($data, array('field_office_id' => ($payload->field_office_id)));
		}
		if(isset($payload->source) && !empty($payload->source))
		{	
			$data = array_merge($data, array('source' => $payload->source));
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{	
			$data = array_merge($data, array('Y_M' => $payload->Y_M));
		}
		$sql = $this->db->insert('F5T2_RCV', $data);
		if($sql)
		{	
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS INSERTING DATA ON TABLE F5T2_RCV'
			);

			//DELETE NEXT MONTH
			$curr_date = strtotime(date($payload->Y_M."-01"));
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));

			$query = $this->db->query("DELETE FROM F5T1 WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");

			$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Added Data Form 5 Table 2 Received.<br/> Payload: ". json_encode($payload),
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
		return json_encode($response);
	}
	public function upsertReferralsActedUpon($payload)
	{
		$data = array();
		if(isset($payload->docket_no) && !empty($payload->docket_no))
		{	
			$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
			$data = array_merge($data, array('docket_no' => strtoupper($payload->docket_no)));
		}
		if(isset($payload->petitioner_name) && !empty($payload->petitioner_name))
		{	
			$data = array_merge($data, array('petitioner_name' => strtoupper($payload->petitioner_name)));
		}
		if(isset($payload->psir_date) && !empty($payload->psir_date))
		{	
			$data = array_merge($data, array('psir_date' => $payload->psir_date));
		}
		if(isset($payload->manifest_date) && !empty($payload->manifest_date))
		{	
			$data = array_merge($data, array('manifest_date' => $payload->manifest_date));
		}

		if(isset($payload->ppo_recommendation) && !empty($payload->ppo_recommendation))
		{	
			$data = array_merge($data, array('ppo_recommendation' => $payload->ppo_recommendation));
		}

		if(isset($payload->transfer_from) && !empty($payload->transfer_from))
		{	
			$data = array_merge($data, array('transfer_from' => strtoupper($payload->transfer_from)));
		}

		if(isset($payload->transfer_to) && !empty($payload->transfer_to))
		{	
			$data = array_merge($data, array('transfer_to' => strtoupper($payload->transfer_to)));
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
			$data = array_merge($data, array('field_office' => ($payload->field_office)));
		}
		if(isset($payload->field_office_id) && !empty($payload->field_office_id))
		{	
			$data = array_merge($data, array('field_office_id' => ($payload->field_office_id)));
		}
		if(isset($payload->source) && !empty($payload->source))
		{	
			$data = array_merge($data, array('source' => $payload->source));
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{	
			$data = array_merge($data, array('Y_M' => $payload->Y_M));
		}
		$sql = $this->db->insert('F5T2_ACTED', $data);
		if($sql)
		{	
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS INSERTING DATA ON TABLE F5T2_ACTED'
			);

			$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Added Data Form 5 Table 2 Acted.<br/> Payload: ". json_encode($payload),
					"module" => "CASELOAD" );
			$this->Cmis_Feedback_model->AuditInsert($p2);



			//DELETE NEXT MONTH
			$curr_date = strtotime(date($payload->Y_M."-01"));
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));

			$query = $this->db->query("DELETE FROM F5T1 WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");

			$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Added Data Form 5 Table 2 Received.<br/> Payload: ". json_encode($payload),
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
		return json_encode($response);
	}
	public function upsertReferralsNotActedUpon($payload)
	{
		$data = array();
		if(isset($payload->docket_no) && !empty($payload->docket_no))
		{	
			$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
			$data = array_merge($data, array('docket_no' => strtoupper($payload->docket_no)));
		}
		if(isset($payload->petitioner_name) && !empty($payload->petitioner_name))
		{	
			$data = array_merge($data, array('petitioner_name' => strtoupper($payload->petitioner_name)));
		}
		if(isset($payload->received_date) && !empty($payload->received_date))
		{	
			$data = array_merge($data, array('received_date' => $payload->received_date));
		}

		if(isset($payload->received_date) && !empty($payload->received_date))
		{	
			$data = array_merge($data, array('received_date' => $payload->received_date));
		}


		if(isset($payload->disposed_decision) && !empty($payload->disposed_decision))
		{	
			$data = array_merge($data, array('disposed_decision' => $payload->disposed_decision));
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
			$data = array_merge($data, array('field_office' => ($payload->field_office)));
		}
		
		if(isset($payload->field_office_id) && !empty($payload->field_office_id))
		{	
			$data = array_merge($data, array('field_office_id' => ($payload->field_office_id)));
		}
		if(isset($payload->source) && !empty($payload->source))
		{	
			$data = array_merge($data, array('source' => $payload->source));
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{	
			$data = array_merge($data, array('Y_M' => $payload->Y_M));
		}
		$sql = $this->db->insert('F5T2_NOTACTED', $data);
		if($sql)
		{	
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS INSERTING DATA ON TABLE F5T2_NOTACTED'
			);

			//DELETE NEXT MONTH
			$curr_date = strtotime(date($payload->Y_M."-01"));
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));

			$query = $this->db->query("DELETE FROM F5T1 WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");


			$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Added Data Form 5 Table 2 Not Acted.<br/> Payload: ". json_encode($payload),
					"module" => "CASELOAD" );
			$this->Cmis_Feedback_model->AuditInsert($p2);

			//DELETE NEXT MONTH
			$curr_date = strtotime(date($payload->Y_M."-01"));
			$date_transfer = date("Y-m",strtotime("+1 month",$curr_date));

			$query = $this->db->query("DELETE FROM F5T1 WHERE field_office = '".$payload->field_office."' and  Y_M  >= '".$date_transfer."' and docket_no ='".$payload->docket_no."'");

			$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Added Data Form 5 Table 2 Received.<br/> Payload: ". json_encode($payload),
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
		return json_encode($response);
	}


	public function fetchF5T2_RCV_ByYM($payload)
	{
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->like('Y_M', $payload->Y_M);
		}
		if(isset($payload->END_Y_M) && !empty($payload->END_Y_M))
		{
			$this->db->where('Y_M >=', $payload->Y_M);
			$this->db->where('Y_M <=', $payload->END_Y_M);
		}

		$this->db->where('status', 1);
		if(isset($payload->field_office) && !empty($payload->field_office) && ($payload->field_office != "ALL") )
		{
			$this->db->where('field_office', $payload->field_office);
		}

		
		$sql = $this->db->get('F5T2_RCV');
		if($sql->num_rows() > 0 )
		{

			$output = array();
			foreach($sql->result() as &$row) {
			    // $row->docket_no_display = "<a title='Click to view Docket Record'><span data-id='".$row->docket_no."' class='docket_link'>".$row->docket_no."</span></a>";
			    // $row->petitioner_name = "<a ><span title='Click to open Petitioner FACT SHEET' data-id='".$row->docket_no."'  class='petitioner_link'>".$row->petitioner_name."</span></a>";
			    $row->docket_no_display = $row->docket_no;
			    $row->petitioner_name = $row->petitioner_name;
			    $output[] = $row;
			}


			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA ON F5T2_RCV TABLE',
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

	public function fetchF5T2_RCV_ByID($payload)
	{
		if(isset($payload->ID) && !empty($payload->ID))
		{
			$this->db->like('ID', $payload->ID);
		}
		$this->db->where('status', 1);
		if(isset($payload->field_office) && !empty($payload->field_office) && ($payload->field_office != "ALL") )
		{
			$this->db->where('field_office', $payload->field_office);
		}

		
		$sql = $this->db->get('F5T2_RCV');
		if($sql->num_rows() > 0 )
		{
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA ON F5T2_RCV TABLE',
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


	public function fetchF5T2_ACTED_ByID($payload)
	{
		if(isset($payload->ID) && !empty($payload->ID))
		{
			$this->db->like('ID', $payload->ID);
		}
		$this->db->where('status', 1);
		if(isset($payload->field_office) && !empty($payload->field_office) && ($payload->field_office != "ALL") )
		{
			$this->db->where('field_office', $payload->field_office);
		}

		
		$sql = $this->db->get('F5T2_ACTED');
		if($sql->num_rows() > 0 )
		{

			
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA ON F5T2_ACTED TABLE',
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

	public function checkExistF5T2RCV($payload){
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
		$get = $this->db->get('F5T2_RCV');
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

	public function checkExistF5T2ACT($payload){
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
		$get = $this->db->get('F5T2_ACTED');
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

	public function fetchF5T2_ACTED_ByYM($payload)
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
		$sql = $this->db->get('F5T2_ACTED');
		if($sql->num_rows() > 0 )
		{
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA ON F5T2_ACTED TABLE',
				'payload' => $sql->result()
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

	public function fetchF5T2_NOTACTED_ByYM($payload)
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

		if(isset($payload->disposed_decision) && !empty($payload->disposed_decision) && ($payload->disposed_decision != "ALL") )
		{
			$this->db->where('disposed_decision', $payload->disposed_decision);
		}
		$this->db->order_by("id","asc");
		
		$sql = $this->db->get('F5T2_NOTACTED');
		if($sql->num_rows() > 0 )
		{
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA ON F5T2_NOTACTED TABLE',
				'payload' => $sql->result()
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

	public function fetchF5T2_NOTACTED_ByID($payload)
	{
		if(isset($payload->ID) && !empty($payload->ID))
		{
			$this->db->like('ID', $payload->ID);
		}
		$this->db->where('status', 1);
		if(isset($payload->field_office) && !empty($payload->field_office) && ($payload->field_office != "ALL") )
		{
			$this->db->where('field_office', $payload->field_office);
		}

		
		$sql = $this->db->get('F5T2_NOTACTED');
		if($sql->num_rows() > 0 )
		{
			$response = array(
				'status' => 'SUCCESS',
				'message' => 'SUCCESS FETCHING DATA ON F5T2_ACTED TABLE',
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


	//AUTOMATION
	public function upsertSF5T2_RCV($payload)
	{
		if($payload != null)
		{
			$check = $this->checkExistF5T2RCV($payload);
			$response = array();
			
			if($check['status'] == 'SUCCESS'){
				$response = json_decode($this->updateReferralsReceived($payload));
			}else{
				#echo "INSERT";
				$response = json_decode($this->upsertReferralsReceived($payload));
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

	public function upsertSF5T2_ACT($payload)
	{
		if($payload != null)
		{
			$check = $this->checkExistF5T2ACT($payload);
			$response = array();
			
			if($check['status'] == 'SUCCESS'){
				$response = json_decode($this->updateReferralsActedUpon($payload));
			}else{
				#echo "INSERT";
				$response = json_decode($this->upsertReferralsActedUpon($payload));
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


	public function updateReferralsReceived($payload)
	{
		if($payload != null)
		{
			
			$required_params = 1;
			if(isset($payload->docket_no) && !empty($payload->docket_no))
			{	
				$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
				$this->db->set('docket_no', $payload->docket_no);
			}
			if(isset($payload->case_no) && !empty($payload->case_no))
			{	
				$this->db->set('case_no', $payload->case_no);
			}
			if(isset($payload->court_origin) && !empty($payload->court_origin))
			{	
				$this->db->set('court_origin', $payload->court_origin);
			}
			if(isset($payload->plea_bargain) && !empty($payload->plea_bargain))
			{	
				$this->db->set('plea_bargain', $payload->plea_bargain);
			}else{
				$this->db->set('plea_bargain', NULL);
			}
			if(isset($payload->offense) && !empty($payload->offense))
			{	
				$this->db->set('offense', $payload->offense);
			}else{
				$this->db->set('offense', NULL);
			}
			if(isset($payload->sentence) && !empty($payload->sentence))
			{	
				$this->db->set('sentence', $payload->sentence);
			}else{
				$this->db->set('sentence', NULL);
			}
			if(isset($payload->date_of_court_order) && !empty($payload->date_of_court_order))
			{	
				$this->db->set('date_of_court_order', $payload->date_of_court_order);
			}
			if(isset($payload->received_date) && !empty($payload->received_date))
			{	
				$this->db->set('received_date', $payload->received_date);
			}
			if(isset($payload->petitioner_name) && !empty($payload->petitioner_name))
			{	
				$this->db->set('petitioner_name', $payload->petitioner_name);
			}
			if(isset($payload->investigating_officer_name) && !empty($payload->investigating_officer_name))
			{	
				$this->db->set('investigating_officer_name', $payload->investigating_officer_name);
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

			if(isset($payload->field_office_id) && !empty($payload->field_office_id))
			{	
				$this->db->set('field_office_id', $payload->field_office_id);
			}

			if(isset($payload->status))
			{	
				$this->db->set('status', $payload->status);
			}


			if(isset($payload->source) && !empty($payload->source))
			{	
				$this->db->set('source', $payload->source);
			}
			if(isset($payload->Y_M) && !empty($payload->Y_M))
			{	
				$this->db->set('Y_M', $payload->Y_M);
			}
			if(isset($payload->id) && !empty($payload->id))
			{	
				$this->db->where('id', $payload->id);
				$required_params--;
			}else{
				$this->db->where('Y_M', $payload->Y_M);
				$this->db->where('docket_no', $payload->docket_no);
				$required_params--;
			}



			if($required_params == 0 )
			{
				$update = $this->db->update('F5T2_RCV');
				if($this->db->affected_rows() > 0 )
				{
					$response = array(
						'status' => 'SUCCESS',
						'message' => 'SUCCESS UPDATING DATA'
					);

					$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Updated Data Form 5 Table 2.<br/> Payload: ". json_encode($payload),
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
					'message' => 'PLEASE FILL UP ALL THE REQUIRED PARAMETER',
					'paramter' => $required_params
				);
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

	public function updateReferralsActedUpon($payload)
	{
		if($payload != null)
		{
			$required_params = 1;
			if(isset($payload->docket_no) && !empty($payload->docket_no))
			{	
				$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
				$this->db->set('docket_no', $payload->docket_no);
			}
			if(isset($payload->petitioner_name) && !empty($payload->petitioner_name))
			{	
				$this->db->set('petitioner_name', $payload->petitioner_name);
			}
			if(isset($payload->psir_date) && $payload->psir_date!= "")
			{	
				$this->db->set('psir_date', $payload->psir_date);
			}else{
				$this->db->set('psir_date', NULL);

			}
			if(isset($payload->field_office) && !empty($payload->field_office))
			{	
				$this->db->set('field_office', $payload->field_office);
			}

			if(isset($payload->field_office_id) && !empty($payload->field_office_id))
			{	
				$this->db->set('field_office_id', $payload->field_office_id);
			}
			if(isset($payload->created_date) && !empty($payload->created_date))
			{	
				$this->db->set('created_date', $payload->created_date);
			}

			if(isset($payload->manifest_date) && !empty($payload->manifest_date))
			{	
				$this->db->set('manifest_date', $payload->manifest_date);
			}else{
				$this->db->set('manifest_date', NULL);
			}

			if(isset($payload->ppo_recommendation) && !empty($payload->ppo_recommendation))
			{	
				$this->db->set('ppo_recommendation', $payload->ppo_recommendation);
			}else{
				$this->db->set('ppo_recommendation', NULL);
			}

			if(isset($payload->transfer_date) && !empty($payload->transfer_date))
			{	
				$this->db->set('transfer_date', $payload->transfer_date);
			}else{
				$this->db->set('transfer_date', NULL);
			}

			if(isset($payload->transfer_to) && !empty($payload->transfer_to))
			{	
				$this->db->set('transfer_to', $payload->transfer_to);
			}else{
				$this->db->set('transfer_to', NULL);
			}
			if(isset($payload->created_by) && !empty($payload->created_by))
			{	
				$this->db->set('created_by', $payload->created_by);
			}
			if(isset($payload->source) && !empty($payload->source))
			{	
				$this->db->set('source', $payload->source);
			}
			if(isset($payload->Y_M) && !empty($payload->Y_M))
			{	
				$this->db->set('Y_M', $payload->Y_M);
			}
			if(isset($payload->status) )
			{	
				$this->db->set('status', $payload->status);
			}

			if(isset($payload->id) && !empty($payload->id))
			{	
				$this->db->where('id', $payload->id);
				$required_params--;
			}else{
				$this->db->where('Y_M', $payload->Y_M);
				$this->db->where('docket_no', $payload->docket_no);
				$required_params--;
			}

			
			if($required_params == 0 )
			{
				$update = $this->db->update('F5T2_ACTED');
				if($this->db->affected_rows() > 0 )
				{
					$response = array(
						'status' => 'SUCCESS',
						'message' => 'SUCCESS UPDATING DATA'
					);

					$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Updated Data Form 5 Table 2.<br/> Payload: ". json_encode($payload),
					"module" => "CASELOAD" );
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
					'message' => 'PLEASE FILL UP ALL THE REQUIRED PARAMETER',
					'paramter' => $required_params
				);
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

	public function updateReferralsNotActedUpon($payload)
	{
		if($payload != null)
		{
			
			$required_params = 1;
			if(isset($payload->docket_no) && !empty($payload->docket_no))
			{	
				$payload->docket_no = preg_replace('/\s+/', '', $payload->docket_no);
				$this->db->set('docket_no', $payload->docket_no);
			}
			if(isset($payload->petitioner_name) && !empty($payload->petitioner_name))
			{	
				$this->db->set('petitioner_name', $payload->petitioner_name);
			}
			if(isset($payload->received_date) && !empty($payload->received_date))
			{	
				$this->db->set('received_date', $payload->received_date);
			}
			if(isset($payload->field_office) && !empty($payload->field_office))
			{	
				$this->db->set('field_office', $payload->field_office);
			}
			if(isset($payload->disposed_decision) && !empty($payload->disposed_decision))
			{	
				$this->db->set('disposed_decision', $payload->disposed_decision);
			}
			if(isset($payload->created_date) && !empty($payload->created_date))
			{	
				$this->db->set('created_date', $payload->created_date);
			}
			if(isset($payload->created_by) && !empty($payload->created_by))
			{	
				$this->db->set('created_by', $payload->created_by);
			}
			if(isset($payload->source) && !empty($payload->source))
			{	
				$this->db->set('source', $payload->source);
			}
			if(isset($payload->status))
			{	
				$this->db->set('status', $payload->status);
			}
			if(isset($payload->Y_M) && !empty($payload->Y_M))
			{	
				$this->db->set('Y_M', $payload->Y_M);
			}
			if(isset($payload->id) && !empty($payload->id))
			{	
				$this->db->where('id', $payload->id);
				$required_params--;
			}
			if($required_params == 0 )
			{
				$update = $this->db->update('F5T2_NOTACTED');
				if($this->db->affected_rows() > 0 )
				{
					$response = array(
						'status' => 'SUCCESS',
						'message' => 'SUCCESS UPDATING DATA'
					);

					$p2 = (object)array( "created_by" => $payload->created_by,
					"action" => "Updated Data Form 5 Table 2.<br/> Payload: ". json_encode($payload),
					"module" => "CASELOAD" );
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
					'message' => 'PLEASE FILL UP ALL THE REQUIRED PARAMETER',
					'paramter' => $required_params
				);
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