<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {


	public function __construct() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	// set_time_limit(2700);

    	parent::__construct();
	}

	
	
	public function getAuditTrail()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getAuditTrail($payload);

	}
	public function checkUserEmailExist()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->checkUserEmailExist($payload);

	}

	public function backup()
	{	
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->backup();
	}

	public function backupDate()
	{	
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->backupDate();
	}
	public function full_restore()
	{	
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->full_restore();
	}

	
	public function getDatetime()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getDatetime($payload);
	}

	

	
	public function getAllUserType()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getAllUserType($payload);
	}

	public function getAllUserTypes()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getAllUserTypes($payload);
	}

	public function AddUserType()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->AddUserType($payload);
	}	

	public function getAllUserTypeModules()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getAllUserTypeModules($payload);
	}

	
	public function authenticate()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->authenticate($payload);
	}

	public function AddUser()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->AddUser($payload);
	}	
	public function UpdateUser()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->UpdateUser($payload);
	}	
	public function UpdateUserType()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->UpdateUserType($payload);
	}		

	
	public function getAllUserList()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getAllUserList($payload);
	}

	public function getUserByID()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getUserByID($payload);
	}
	public function getUserTypeByID()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getUserTypeByID($payload);
	}
	public function getUserTypeByModulesByID()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getUserTypeByModulesByID($payload);
	}

	public function getDeletedList()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getDeletedList($payload);
	}
	public function restoreDeleted()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->restoreDeleted($payload);
	}

	

	public function caseload_reports()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->caseload_reports($payload);
	}

	public function getAllforms()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getAllforms($payload);
	}
	public function getFormByPage()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->getFormByPage($payload);
	}

	public function UpdateForm()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->UpdateForm($payload);
	}
	public function migrate_offline()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_offline($payload);
	}
	public function migrate_f5()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f5($payload);
	}
	public function migrate_f21()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21($payload);
	}

	public function migrate_cron_per_region()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_cron_per_region($payload);
	}

	public function migrate_offline_cron()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_offline_cron($payload);
	}

	public function migrate_offline_checker()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_offline_checker($payload);
	}

	public function cleanUp()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->cleanUp($payload);
	}


	public function cleanUpByFO()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->cleanUpByFO($payload);
	}

	public function cleanUpDuplicates()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->cleanUpDuplicates($payload);
	}
	public function authenticateSSO()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->authenticateSSO($payload);
	}
	public function UpdateUserByEmail()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->UpdateUserByEmail($payload);
	}
	public function doLogout()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->doLogout($payload);
	}


	public function isActive()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->isActive($payload);
	}

	public function migrate_f5t1()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f5t1($payload);
	}

	public function migrate_trigger()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_trigger($payload);
	}
	public function migrate_f5t3()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f5t3($payload);
	}
	public function migrate_f5t5()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f5t5($payload);
	}
	public function migrate_f5t7()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f5t7($payload);
	}
	public function migrate_f5t10()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f5t10($payload);
	}
	public function migrate_f5t12()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f5t12($payload);
	}


	public function migrate_f21t1()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t1($payload);
	}
	public function migrate_f21t3()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t3($payload);
	}
	public function migrate_f21t5()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t5($payload);
	}
	public function migrate_f21t7_pardon()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t7_pardon($payload);
	}
	public function migrate_f21t7_parol()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t7_parol($payload);
	}
	public function migrate_f21t10_pardon()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t10_pardon($payload);
	}
	public function migrate_f21t10_parol()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t10_parol($payload);
	}
	public function migrate_f21t12_pardon()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t12_pardon($payload);
	}
	public function migrate_f21t12_parol()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t12_parol($payload);
	}
	public function migrate_f21t14_pardon()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t14_pardon($payload);
	}
	public function migrate_f21t14_parol()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->API_model->migrate_f21t14_parol($payload);
	}
}
