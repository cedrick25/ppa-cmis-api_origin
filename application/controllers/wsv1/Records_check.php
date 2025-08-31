<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Records_check extends CI_Controller {


	public function __construct() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	// set_time_limit(2700);

    	parent::__construct();

	}

	public function f5t1()
	{
		include APPPATH . 'third_party/ssp.php';
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Records_check_model->f5t1($payload);
	}
	public function search_data() {
    // Capture GET parameters
	    $docket_no = $this->input->get('docket_no');
	    $name = $this->input->get('name');
	    $table_name = $this->input->get('table_name');
	    $db_name = $this->input->get('db_name'); // Assume 'default' or 'expansion'

	    // Validate db_name
	    $valid_db_names = ['default', 'expansion'];
	    if (!in_array($db_name, $valid_db_names)) {
	        echo json_encode(['error' => 'Invalid database name']);
	        return;
	    }

	    // Fetch data from the model
	    $result = $this->Records_check_model->get_data($db_name, $table_name, $docket_no, $name);

	    // Return the result
	    echo json_encode($result);
	}
	public function search_expansion_data() {
	    // Capture GET parameters
	    $docket_no = $this->input->get('docket_no');
	    $name = $this->input->get('name');
	    $table_name = $this->input->get('table_name');
	    $db_name = $this->input->get('db_name'); // Assume 'default' or 'expansion'

	    // Validate db_name
	    $valid_db_names = ['default', 'expansion'];
	    if (!in_array($db_name, $valid_db_names)) {
	        echo json_encode(['error' => 'Invalid database name']);
	        return;
	    }

	    // Fetch data from the model
	    $result = $this->Records_check_model->get_data_expansion($db_name, $table_name, $docket_no, $name);

	    // Return the result
	    echo json_encode($result);
	}
}
