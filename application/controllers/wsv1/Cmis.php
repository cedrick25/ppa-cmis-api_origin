<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cmis extends CI_Controller {


	public function __construct() {
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	// set_time_limit(2700);

    	parent::__construct();
	}

	/* F5T1 */
	public function fetchF5T1ByYM()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T1_model->fetchF5T1ByYM($payload);
	}

	public function validateDocket()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T1_model->validateDocket($payload);
	}

	public function fetchF5T1ByID()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T1_model->fetchF5T1ByID($payload);
	}
	public function countF5T1ByYM()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T1_model->countF5T1ByYM($payload);
	}
	public function updateF5T1()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T1_model->updateF5T1($payload);
	}
	public function upsertF5T1()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T1_model->upsertF5T1($payload);
	}

	public function AuditInsert()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Feedback_model->AuditInsert($payload);
	}
	/* F5T1 */


	/* F5T2 */
	public function fetchF5T2_RCV_ByYM()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->fetchF5T2_RCV_ByYM($payload);
	}

	public function fetchF5T2_RCV_ByID()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->fetchF5T2_RCV_ByID($payload);
	}

	public function fetchF5T2_ACTED_ByYM()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->fetchF5T2_ACTED_ByYM($payload);
	}

	public function fetchF5T2_ACTED_ByID()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->fetchF5T2_ACTED_ByID($payload);
	}

	public function fetchF5T2_NOTACTED_ByYM()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->fetchF5T2_NOTACTED_ByYM($payload);
	}

	public function fetchF5T2_NOTACTED_ByID()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->fetchF5T2_NOTACTED_ByID($payload);
	}

	public function upsertF5T2_RCV()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->upsertReferralsReceived($payload);
	}

	//AUTOMATION
	public function upsertSF5T2_RCV()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->upsertSF5T2_RCV($payload);
	}

	public function upsertSF5T2_ACT()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->upsertSF5T2_ACT($payload);
	}

	public function upsertSF5T4()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T4_model->upsertSF5T4($payload);
	}

	public function upsertSF5T6_RCV()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T6_model->upsertSF5T6_RCV($payload);
	}

	public function upsertSF5T8()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T8_model->upsertSF5T8($payload);
	}

	public function checkExistF5T8()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo json_encode($this->Cmis_F5T8_model->checkExistF5T8($payload));
	}

	public function upsertSF5T12()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T12_model->upsertSF5T12($payload);
	}

	public function checkExistF5T12()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo json_encode($this->Cmis_F5T12_model->checkExistF5T12($payload));
	}



	//

	public function updateF5T2_RCV()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->updateReferralsReceived($payload);
	}

	public function upsertF5T2_ACTED()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->upsertReferralsActedUpon($payload);
	}

	public function updateF5T2_ACTED()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->updateReferralsActedUpon($payload);
	}

	public function upsertF5T2_NOTACTED()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->upsertReferralsNotActedUpon($payload);
	}

	public function updateF5T2_NOTACTED()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T2_model->updateReferralsNotActedUpon($payload);
	}

	public function F5T3(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T3_model->F5T3($payload);
	}
	public function F5T4(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T4_model->F5T4($payload);
	}

	public function F5T5(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T5_model->F5T5($payload);
	}

	public function F5T6_RCV(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T6_model->F5T6_RCV($payload);
	}

	public function F5T6_CMPLTD(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T6_model->F5T6_CMPLTD($payload);
	}

	public function F5T7(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T7_model->F5T7($payload);
	}

	public function F5T8(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T8_model->F5T8($payload);
	}

	public function F5T9(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T9_model->F5T9($payload);
	}

	public function F5T10(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T10_model->F5T10($payload);
	}

	public function F5T11(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T11_model->F5T11($payload);
	}

	public function F5T12(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T12_model->F5T12($payload);
	}

	public function F5T13_RCV(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T13_model->F5T13_RCV($payload);
	}

	public function F5T13_TERM(){
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5T13_model->F5T13_TERM($payload);
	}
	

	public function F21T1()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T1_model->F21T1($payload);
	}

	public function F21T2_RCV()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T2_model->F21T2_RCV($payload);
	}

	public function F21T2_ACTED()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T2_model->F21T2_ACTED($payload);
	}

	public function F21T3()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T3_model->F21T3($payload);
	}

	public function F21T4()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T4_model->F21T4($payload);
	}

	public function F21T5()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T5_model->F21T5($payload);
	}

	public function F21T6_CMPLTD()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T6_model->F21T6_CMPLTD($payload);
	}

	public function F21T6_RCV()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T6_model->F21T6_RCV($payload);
	}

	public function F21T7()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T7_model->F21T7($payload);
	}

	public function F21T8()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T8_model->F21T8($payload);
	}

	public function F21T9()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T9_model->F21T9($payload);
	}

	public function F21T10()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T10_model->F21T10($payload);
	}

	public function F21T11()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T11_model->F21T11($payload);
	}

	public function F21T12()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T12_model->F21T12($payload);
	}

	public function F21T13()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T13_model->F21T13($payload);
	}

	public function F21T14()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21T14_model->F21T14($payload);
	}

	public function F21T15()
	{
		$payload = json_decode(file_get_contents('php://input'));
		if($payload->table == "F21T15_RCV_PAROL" || $payload->table == "F21T15_RCV_PARDON"){
			echo $this->Cmis_F21T15_model->F21T15_RCV($payload);
		}else{
			echo $this->Cmis_F21T15_model->F21T15_TERM($payload);
		}
		
	}

	public function F5SUMMARY()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F5PCS_model->getSummary($payload);
	}

	public function F21SUMMARY()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_F21PCS_model->getSummary($payload);
	}

	public function upsertMasterlist()
	{
		include APPPATH . 'third_party/ssp.php';
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Probationer_model->upsertMasterlist($payload);
	}

	public function upsertMasterlist_request()
	{
		include APPPATH . 'third_party/ssp.php';
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Probationer_model->upsertMasterlist_request($payload);
	}

	public function masterlistSSP()
	{
		include APPPATH . 'third_party/ssp.php';
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Probationer_model->masterlistSSP($payload);
	}
	public function masterlist_json()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Probationer_model->masterlist_json($payload);
	}

	public function masterlistRequestSSP()
	{
		include APPPATH . 'third_party/ssp.php';
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Probationer_model->masterlistRequestSSP($payload);
	}

	public function upsertFeedback()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Feedback_model->upsertFeedback($payload);
	}
	

	public function widgets()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Widgets_model->widgets($payload);
	}

	public function reports()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Reports_model->reports($payload);
	}

	public function callProcedure()
	{
		$payload = json_decode(file_get_contents('php://input'));
		echo $this->Cmis_Widgets_model->callProcedure($payload->procedure,$payload);
	}

}
