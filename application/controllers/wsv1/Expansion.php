<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Expansion extends CI_Controller {


	public function __construct() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	// set_time_limit(2700);

    	parent::__construct();
	}

	public function communitySSP()
	{
		include APPPATH . 'third_party/ssp.php';
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Expansion_model->communitySSP($payload);
	}

	public function community_json()
	{
		include APPPATH . 'third_party/ssp.php';
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Expansion_model->community_json($payload);
	}
}
