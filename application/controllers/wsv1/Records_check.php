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
}
