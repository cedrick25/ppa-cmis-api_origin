<?php 
	
class Cmis_Reports_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	

	public function reports($payload)
	{
		if($payload != null)
		{

			switch ($payload->method) {

				case 'f5_regional_pi_r1_p1':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							$totalCarryOver = 0;
							$totalRcv = 0;
							$totalInvestigation = 0;
							$totalRcv2 = 0;
							if($fieldOffice->status == 'SUCCESS'){

								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->filter = ""; $payload->table = "F5T1"; $payload->field_office = $value1->NAME;
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;

									$payload->filter = ""; $payload->table = "F5T2_RCV"; $payload->field_office = $value1->NAME;
									$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRcv += $rcv->count;

									$totalRcv2 = $totalRcv;
									$totalInvestigation = $totalCarryOver + $totalRcv;
								};
							}
							

							

							$data[] = array('REGION' => $value->VALUE_,
											'ID' => $value->ID,
											'carryOver' => $totalCarryOver,
											'totalRcv' => $totalRcv,
											'totalRcv2' => $totalRcv2,
											'totalInvestigation' => $totalInvestigation,

										);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;


				case 'f5_regional_pi_r1_p2':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							$totalGrant = 0;
							$totalDenial = 0;
							$totalManifest = 0;
							$totalTransfer = 0;
							$totalActed = 0;
							$totalRecall = 0;
							$totalWarrant = 0;
							$totalNotActed = 0;

							$totalCarryOver = 0;
							$totalRcv = 0;
							$totalInvestigation = 0;
							$totalRcv2 = 0;

							$totalActive = 0;
							if($fieldOffice->status == 'SUCCESS'){

								foreach ($fieldOffice->payload as $key1 => $value1){


									$payload->filter = ""; $payload->table = "F5T1"; $payload->field_office = $value1->NAME; $payload->filter_val="";$payload->filter_field="";
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;


									$payload->filter = ""; $payload->table = "F5T2_RCV"; $payload->field_office = $value1->NAME;$payload->filter_val="";$payload->filter_field="";
									$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRcv += $rcv->count;

									$totalRcv2 = $totalRcv;
									$totalInvestigation = $totalCarryOver + $totalRcv;


									#echo $this->Cmis_F5T2_model->fetchF5T2_RCV_ByYM($payload);
									$payload->table = "F5T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Grant"; $payload->field_office = $value1->NAME;
									$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalGrant += $grant_upon->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Denial"; $payload->field_office = $value1->NAME;
									$denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDenial += $denial_upon->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "manifest_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$manifest_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalManifest += $manifest_upon->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$transfer_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTransfer += $transfer_upon->count;

									$payload->table = "F5T2_NOTACTED"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Recall"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRecall += $upon->count;

									$payload->table = "F5T2_NOTACTED"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Warrant"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalWarrant += $upon->count;

									
									$totalActed = $totalGrant + $totalDenial + $totalManifest + $totalTransfer;
									$totalNotActed = $totalRecall + $totalWarrant;
									$totalActive = $totalInvestigation - ($totalActed + $totalNotActed);
									#($act_upon);

								};
							}
							

							

							$data[] = array('REGION' => $value->VALUE_,
											'ID' => $value->ID,
											'totalGrant' => $totalGrant,
											'totalDenial' => $totalDenial,
											'totalManifest' => $totalManifest,
											'totalTransfer' => $totalTransfer,
											'totalActed' => $totalActed,
											'totalRecall' => $totalRecall,
											'totalWarrant' => $totalWarrant,
											'totalNotActed' => $totalNotActed,
											'totalActive' => $totalActive,
											'totalInvestigation' => $totalInvestigation

										);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f5_regional_pi_r2':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							$totalCarryOver = 0;
							$totalPSIR = 0;
							$totalManifest = 0;
							$totalSubmitted = 0;
							$totalbeActed = 0;

							$totalGrant = 0;
							$totalDenial = 0;
							$totalDismiss = 0;
							$totalWithdraw = 0;
							$totalReinv = 0;
							$totalOther = 0;
							$totalWarrant = 0;
							$totalRecall = 0;
							$totalDisposed = 0;
							$totalNotActed = 0;
							$totalPending = 0;
							if($fieldOffice->status == 'SUCCESS'){

								foreach ($fieldOffice->payload as $key1 => $value1){


									$payload->filter = ""; $payload->table = "F5T3"; $payload->field_office = $value1->NAME; $payload->filter_val="";$payload->filter_field="";
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;


									$payload->table = "F5T2_ACTED"; $payload->filter_field = "psir_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalPSIR += $upon->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "manifest_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalManifest += $upon->count;

									$totalSubmitted = $totalPSIR + $totalManifest;
									$totalbeActed = $totalSubmitted + $totalCarryOver;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Grant"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalGrant += $upon->count;

									
									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Den"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDenial += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismi"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDismiss += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalWithdraw += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Reinv"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalReinv += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Other"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalOther += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Warrant"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalWarrant += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Recall"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRecall += $upon->count;

									$totalDisposed = $totalGrant + $totalDenial + $totalDismiss + $totalWithdraw + $totalReinv + $totalOther;
									$totalNotActed = $totalWarrant + $totalRecall;

									$totalPending = $totalbeActed - $totalDisposed;
								};
							}
							

							

							$data[] = array('REGION' => $value->VALUE_,
											'ID' => $value->ID,
											'totalCarryOver' => $totalCarryOver,
											'totalPSIR' => $totalPSIR,
											'totalManifest' => $totalManifest,
											'totalSubmitted' => $totalSubmitted,
											'totalbeActed' => $totalbeActed,
											'totalGrant' => $totalGrant,
											'totalDenial' => $totalDenial,
											'totalDismiss' => $totalDismiss,
											'totalWithdraw' => $totalWithdraw,
											'totalReinv' => $totalReinv,
											'totalOther' => $totalOther,
											'totalDisposed' => $totalDisposed,
											'totalWarrant' => $totalWarrant,
											'totalRecall' => $totalRecall,
											'totalNotActed' => $totalNotActed,
											'totalPending' => $totalPending,

										);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;


				case 'f5_regional_pi_r3':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							$totalCarryOver = 0;
							$totalRcv = 0;
							$totalInvestigation = 0;
							$totalCmpltd = 0;
							$totalActive = 0;
							if($fieldOffice->status == 'SUCCESS'){

								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->filter = ""; $payload->table = "F5T5"; $payload->field_office = $value1->NAME;
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;


									$payload->filter = ""; $payload->table = "F5T6_RCV"; $payload->field_office = $value1->NAME;
									$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRcv += $rcv->count;

									$totalInvestigation = $totalCarryOver + $totalRcv;

									$payload->filter = ""; $payload->table = "F5T6_CMPLTD"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCmpltd += $upon->count;
									$totalActive = $totalInvestigation - $totalCmpltd;
								};
							}
							

							

							$data[] = array('REGION' => $value->VALUE_,
											'ID' => $value->ID,
											'carryOver' => $totalCarryOver,
											'totalRcv' => $totalRcv,
											'totalInvestigation' => $totalInvestigation,
											'totalCmpltd' => $totalCmpltd,
											'totalActive' => $totalActive,

										);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f5_regional_pi_r4':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
								
							$getTotalCarryAdultInvestigation = 0;
							$getTotalCarryAdultInvestigationRpi = 0;
							$getTotalCarryAdultInvestigationTpi = 0;
							$getTotalCarryJICLInvestigation = 0;
							$getTotalCarryJICLInvestigationJRPI = 0;
							$getTotalCarryJICLInvestigationJTPI = 0;

							$carryOverInvestigationAdult = 0;
							$carryOverInvestigationJICL = 0;
							$carryOverInvestigationTotal = 0;
							
							$rcvInvestigationJICLMilNewJPI = 0;
							$rcvInvestigationJICLMilNewJTPI = 0;
							$rcvInvestigationAdultMilNewJPI = 0;
							$rcvInvestigationAdultMilNewJTPI = 0;
							$rcvInvestigationJICLMilNew = 0;
							$rcvInvestigationAdultMilNe = 0;
							$rcvInvestigationTotalMilNew = 0;
							
							$rcvInvestigationJICLCivilNewJPI = 0;
							$rcvInvestigationJICLCivilNewJTPI = 0;
							$rcvInvestigationAdultCivilNewJPI = 0;
							$rcvInvestigationAdultCivilNewJTPI = 0;
							$rcvInvestigationJICLCivilNew = 0;
							$rcvInvestigationAdultCivilNew = 0;
							$rcvInvestigationTotalCivilNew = 0;

							$rcvInvestigationJICLNew = 0;
							$rcvInvestigationAdultNew = 0;
							$rcvInvestigationTotalNew = 0;
							$rcvInvestigationJICLRPI = 0;
							$rcvInvestigationAdultRPI = 0;
							$rcvInvestigationTotalRPI = 0;
							$rcvInvestigationJICL = 0;
							$rcvInvestigationAdult = 0;
							$rcvInvestigationTotal = 0;

							$totalHandled = 0;

							$getTotalActedAdultInvestigationTransfer = 0;
							$getTotalActedJICLInvestigationTransfer = 0;
							$actedInvestigationAdultTransfer = 0;
							$actedInvestigationJICLTransfer = 0;
							$actedInvestigationTotalTransfer = 0;

							$getTotalNotActedAdultInvestigationRecalled = 0;
							$getTotalNotActedJICLInvestigationRecalled = 0;
							$getTotalNotActedTotalInvestigationRecalled = 0;
							$notactedAdultInvestigationRecalled = 0;
							$notactedJICLInvestigationRecalled = 0;
							$notactedTotalInvestigationRecalled = 0;

							$notactedAdultInvestigationWarrant = 0;
							$notactedJICLInvestigationWarrant = 0;
							$notactedTotalInvestigationWarrant = 0;
							
							$getTotalNotActedAdultInvestigationWarrant = 0;
							$getTotalNotActedJICLInvestigationWarrant = 0;
							$getTotalNotActedTotalInvestigationWarrant = 0;

							$rcvRef = 0;
							$rcvRefGranted = 0;
							$rcvRefDenial = 0;
							$totalRef = 0;
							$totalGrant = 0;
							$totalDenial = 0;

							$getTotalActedAdultInvestigationGrant = 0;
							$getTotalActedAdultInvestigationDenial = 0;
							$actedInvestigationAdultGrant = 0;
							$actedInvestigationAdultDenial = 0;
							$getTotalActedJICLInvestigationGrant = 0;
							$getTotalActedJICLInvestigationDenial = 0;
							$actedInvestigationJICLGrant = 0;
							$actedInvestigationJICLDenial = 0;
							$actedInvestigationTotalGrant = 0;
							$actedInvestigationTotalDenial = 0;

							$getTotalActedAdultInvestigationManifest = 0;
							$getTotalActedJICLInvestigationManifest = 0;
							$actedInvestigationAdultManifest = 0;
							$actedInvestigationJICLManifest = 0;
							$actedInvestigationTotalManifest = 0;

							$totalInvRefref = 0;

							if($fieldOffice->status == 'SUCCESS'){
								foreach ($fieldOffice->payload as $key1 => $value1){	

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"PI"));
									$getTotalCarryAdultInvestigation += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"RPI"));
									$getTotalCarryAdultInvestigationRpi += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"TPI"));
									$getTotalCarryAdultInvestigationTpi += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"JPI"));
									$getTotalCarryJICLInvestigation += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"JRPI"));
									$getTotalCarryJICLInvestigationJRPI += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"JTPI"));
									$getTotalCarryJICLInvestigationJTPI += $upon;

									$carryOverInvestigationAdult = $getTotalCarryAdultInvestigation + $getTotalCarryAdultInvestigationRpi + $getTotalCarryAdultInvestigationTpi;

									$carryOverInvestigationJICL = $getTotalCarryJICLInvestigation + $getTotalCarryJICLInvestigationJRPI + $getTotalCarryJICLInvestigationJTPI;

									$carryOverInvestigationTotal = $carryOverInvestigationAdult + $carryOverInvestigationJICL;
									/////////

									$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1";$payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLCivilNewJPI += $upon->count;
									
									$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1";$payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLCivilNewJTPI += $upon->count;

									$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultCivilNewJPI += $upon->count;
									
									$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultCivilNewJTPI += $upon->count;

									$rcvInvestigationJICLCivilNew = $rcvInvestigationJICLCivilNewJPI + $rcvInvestigationJICLCivilNewJTPI;
									$rcvInvestigationAdultCivilNew = $rcvInvestigationAdultCivilNewJPI + $rcvInvestigationAdultCivilNewJTPI;
									$rcvInvestigationTotalCivilNew = $rcvInvestigationJICLCivilNew + $rcvInvestigationAdultCivilNew;
									/////////////

									$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLMilNewJPI += $upon->count;
									
									$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLMilNewJTPI += $upon->count;

									$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultMilNewJPI += $upon->count;
									
									$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultMilNewJTPI += $upon->count;

									$rcvInvestigationJICLMilNew  = $rcvInvestigationJICLMilNewJPI + $rcvInvestigationJICLMilNewJTPI;
									$rcvInvestigationAdultMilNew  = $rcvInvestigationAdultMilNewJPI + $rcvInvestigationAdultMilNewJTPI;
									$rcvInvestigationTotalMilNew = $rcvInvestigationJICLMilNew + $rcvInvestigationAdultMilNew;

									///////////
									$rcvInvestigationJICLNew = $rcvInvestigationJICLCivilNew + $rcvInvestigationJICLMilNew;
									$rcvInvestigationAdultNew = $rcvInvestigationAdultCivilNew + $rcvInvestigationAdultMilNew;
									$rcvInvestigationTotalNew = $rcvInvestigationJICLNew + $rcvInvestigationAdultNew;


									$payload->filter = "JRPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLRPI += $upon->count;

									$payload->filter = "RPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultRPI += $upon->count;

									$rcvInvestigationTotalRPI = $rcvInvestigationAdultRPI + $rcvInvestigationJICLRPI;

									$rcvInvestigationJICL = $rcvInvestigationJICLNew + $rcvInvestigationJICLRPI;
									$rcvInvestigationAdult = $rcvInvestigationAdultNew + $rcvInvestigationAdultRPI;
									$rcvInvestigationTotal = $rcvInvestigationJICL + $rcvInvestigationAdult;
									$totalHandled = $carryOverInvestigationTotal + $rcvInvestigationTotal;
									///////////////////

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","",0,1));
									$getTotalActedAdultInvestigationTransfer += $upon;

									$payload->field_office = $value1->NAME;
									$upon =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","",0,1));
									$getTotalActedJICLInvestigationTransfer += $upon;

									$actedInvestigationAdultTransfer = $getTotalActedAdultInvestigationTransfer;
									$actedInvestigationJICLTransfer  = $getTotalActedJICLInvestigationTransfer;
									$actedInvestigationTotalTransfer = $getTotalActedAdultInvestigationTransfer + $getTotalActedJICLInvestigationTransfer;

									////////////////////////
									$payload->field_office = $value1->NAME;
									$upon =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","Recall"));
									$getTotalNotActedAdultInvestigationRecalled += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","Recall"));
									$getTotalNotActedJICLInvestigationRecalled += $upon;

									$getTotalNotActedTotalInvestigationRecalled = $getTotalNotActedAdultInvestigationRecalled + $getTotalNotActedJICLInvestigationRecalled;

									$notactedAdultInvestigationRecalled = $getTotalNotActedAdultInvestigationRecalled;
									$notactedJICLInvestigationRecalled = $getTotalNotActedJICLInvestigationRecalled;
									$notactedTotalInvestigationRecalled = $getTotalNotActedTotalInvestigationRecalled;


									$payload->field_office = $value1->NAME;
									$upon =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","Warrant"))  + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","Warrant")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","Warrant"));
									$getTotalNotActedAdultInvestigationWarrant += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","Warrant "))  + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","Warrant ")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","Warrant"));
									$getTotalNotActedJICLInvestigationWarrant += $upon;

									$getTotalNotActedTotalInvestigationWarrant = $getTotalNotActedAdultInvestigationWarrant + $getTotalNotActedJICLInvestigationWarrant;

									$notactedAdultInvestigationWarrant = $getTotalNotActedAdultInvestigationWarrant;
									$notactedJICLInvestigationWarrant  = $getTotalNotActedJICLInvestigationWarrant;
									$notactedTotalInvestigationWarrant = $getTotalNotActedTotalInvestigationWarrant;
									$totalRef = $notactedTotalInvestigationRecalled + $notactedTotalInvestigationWarrant;

									// /////////////
									
									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","FOR GRANT"));
									$getTotalActedAdultInvestigationGrant += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","FOR DENIAL"));
									$getTotalActedAdultInvestigationDenial += $upon;

									$actedInvestigationAdultGrant = $getTotalActedAdultInvestigationGrant;
									$actedInvestigationAdultDenial = $getTotalActedAdultInvestigationDenial;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","FOR GRANT"));
									$getTotalActedJICLInvestigationGrant += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","FOR DENIAL"));
									$getTotalActedJICLInvestigationDenial += $upon;

									$actedInvestigationJICLGrant = $getTotalActedJICLInvestigationGrant;
									$actedInvestigationJICLDenial = $getTotalActedJICLInvestigationDenial;

									$actedInvestigationTotalGrant = $getTotalActedAdultInvestigationGrant  + $getTotalActedJICLInvestigationGrant;
									$actedInvestigationTotalDenial = $getTotalActedAdultInvestigationDenial  + $getTotalActedJICLInvestigationDenial;
									

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","",1));
									$getTotalActedAdultInvestigationManifest += $upon;

									$payload->field_office = $value1->NAME;
									$upon =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","",1));
									$getTotalActedJICLInvestigationManifest += $upon;

									$actedInvestigationAdultManifest = $getTotalActedAdultInvestigationManifest;
									$actedInvestigationJICLManifest = $getTotalActedJICLInvestigationManifest;
									$actedInvestigationTotalManifest = $getTotalActedAdultInvestigationManifest + $getTotalActedJICLInvestigationManifest;
									$totalInvRefref = $actedInvestigationTotalGrant +
												$actedInvestigationTotalDenial +
												$actedInvestigationTotalTransfer +
												$actedInvestigationTotalManifest 
												;

								};
							}
									$data[] = array('REGION' => $value->VALUE_,
													'carryOverInvestigationTotal' => $carryOverInvestigationTotal,
													'rcvInvestigationTotal' => $rcvInvestigationTotal,
													'totalHandled' => $totalHandled,
													'actedInvestigationTotalGrant' => $actedInvestigationTotalGrant,
													'actedInvestigationTotalDenial' => $actedInvestigationTotalDenial,
													'actedInvestigationTotalManifest' => $actedInvestigationTotalManifest,
													'actedInvestigationTotalTransfer' => $actedInvestigationTotalTransfer,
													'totalInvRefref' => $totalInvRefref,
													'notactedTotalInvestigationRecalled' => $notactedTotalInvestigationRecalled,
													'notactedTotalInvestigationWarrant' => $notactedTotalInvestigationWarrant,
													'totalRef' => $totalRef,

												);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f5_regional_ps_r1_p1':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							$totalCarryOver = 0;
							$totalRcv = 0;
							$totalInvestigation = 0;
							$totalRcv2 = 0;
							if($fieldOffice->status == 'SUCCESS'){

								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->filter = ""; $payload->table = "F5T7"; $payload->field_office = $value1->NAME;
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;


									$payload->filter = ""; $payload->table = "F5T8"; $payload->field_office = $value1->NAME;
									$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRcv += $rcv->count;

									$totalRcv2 = $totalRcv;
									$totalInvestigation = $totalCarryOver + $totalRcv;
								};
							}
							

							

							$data[] = array('REGION' => $value->VALUE_,
											'ID' => $value->ID,
											'carryOver' => $totalCarryOver,
											'totalRcv' => $totalRcv,
											'totalRcv2' => $totalRcv2,
											'totalInvestigation' => $totalInvestigation,

										);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f5_regional_ps_r1_p2':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							
							if($fieldOffice->status == 'SUCCESS'){
								$totalCarryOver = 0;
								$totalFullTerm = 0;
								$totalEarlyTerm = 0;
								$totalDiedTerm = 0;

								$totalAbs = 0;
								$totalComm = 0;
								$totalVio = 0;
								$totalOther = 0;
								$totalRevoc = 0;


								$totalInvestigation = 0;
								$totalRcv = 0;
								$totalRcv2 = 0;
								$totalTerm = 0;

								$totalTransfer = 0;
								$totalOthers = 0;
								$totalDropped = 0;
								$totalActive = 0;

								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->filter_val = ""; $payload->filter_field = ""; $payload->filter = ""; $payload->table = "F5T7"; $payload->field_office = $value1->NAME;
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;


									$payload->filter_val = ""; $payload->filter_field = ""; $payload->filter = ""; $payload->table = "F5T8"; $payload->field_office = $value1->NAME;
									$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRcv += $rcv->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Full Term"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalFullTerm += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Early"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalEarlyTerm += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Died"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDiedTerm += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abs"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalAbs += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Comm"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalComm += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Vio"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalVio += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalOther += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTransfer += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalOthers += $upon->count;

									$totalTerm = $totalFullTerm + $totalEarlyTerm + $totalDiedTerm;

									$totalRcv2 = $totalRcv;
									$totalInvestigation = $totalCarryOver + $totalRcv;
									$totalRevoc = $totalAbs + $totalComm + $totalVio + $totalOther;

									$totalDropped = $totalTerm + $totalRevoc + $totalTransfer + $totalOthers;
									$totalActive = $totalInvestigation - $totalDropped;

								}
									$data[] = array('REGION' => $value->VALUE_,
											'totalInvestigation' => $totalInvestigation,
											'totalFullTerm' => $totalFullTerm,
											'totalEarlyTerm' => $totalEarlyTerm,
											'totalDiedTerm' => $totalDiedTerm,
											'totalTerm' => $totalTerm,
											'totalAbs' => $totalAbs,
											'totalComm' => $totalComm,
											'totalVio' => $totalVio,
											'totalOther' => $totalOther,
											'totalRevoc' => $totalRevoc,
											'totalTransfer' => $totalTransfer,
											'totalOthers' => $totalOthers,
											'totalDropped' => $totalDropped,
											'totalActive' => $totalActive,

										);
							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f5_regional_ps_r1_p3':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							
							if($fieldOffice->status == 'SUCCESS'){
								
								$totalFullTerm = 0;
								$totalEarlyTerm = 0;
								$totalDiedTerm = 0;

								$totalAbs = 0;
								$totalComm = 0;
								$totalVio = 0;
								$totalOther = 0;
								$totalRevoc = 0;


								
								$totalTerm = 0;

								$totalTransfer = 0;
								$totalOthers = 0;
								$totalDropped = 0;
								$totalExt = 0;
								foreach ($fieldOffice->payload as $key1 => $value1){

									
							
									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Full Term"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalFullTerm += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Early"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalEarlyTerm += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Died"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDiedTerm += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abs"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalAbs += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Comm"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalComm += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Vio"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalVio += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalOther += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTransfer += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalExt += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalOthers += $upon->count;

									$totalTerm = $totalFullTerm + $totalEarlyTerm + $totalDiedTerm;

									
									$totalRevoc = $totalAbs + $totalComm + $totalVio + $totalOther;

									$totalDropped = $totalTerm + $totalRevoc + $totalTransfer + $totalOthers;
									

								}
									$data[] = array('REGION' => $value->VALUE_,
											'totalFullTerm' => $totalFullTerm,
											'totalEarlyTerm' => $totalEarlyTerm,
											'totalDiedTerm' => $totalDiedTerm,
											'totalTerm' => $totalTerm,
											'totalAbs' => $totalAbs,
											'totalComm' => $totalComm,
											'totalVio' => $totalVio,
											'totalOther' => $totalOther,
											'totalRevoc' => $totalRevoc,
											'totalExt' => $totalExt,
											'totalTransfer' => $totalTransfer,
											'totalOthers' => $totalOthers,
											'totalDropped' => $totalDropped,
										);
							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

					
				case 'f5_regional_ps_r2_p1':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							// (A)
							$totalCarryTerm = 0;
							$totalCarryRevoc = 0;
							$totalCarryExt = 0;
							$totalCarryOth = 0;
							$totalCarryTrans = 0;
							$totalCarryOver = 0;
							// (B)
							$totalTerm = 0;
							$totalRevoc = 0;
							$totalExt = 0;
							$totalOther = 0;
							$totalTrans = 0;
							$totalSubmitted = 0;
							// (C)
							$totalCasesBeActed = 0;


							if($fieldOffice->status == 'SUCCESS'){
								
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									// (A) CARRY OVER
									$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Termina"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryTerm += $upon->count;

									$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Revoc"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryRevoc += $upon->count;

									$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Extension"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryExt += $upon->count;

									$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryTrans += $upon->count;

									$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOth += $upon->count;

									$totalCarryOver = 	$totalCarryTerm + $totalCarryRevoc + $totalCarryExt + $totalCarryExt + $totalCarryOth;
									
									// (B) SUBMITTED TO COURT
									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTerm += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRevoc += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalExt += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalOther += $upon->count;

									$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTrans += $upon->count;

									$totalSubmitted = $totalTerm + $totalRevoc + $totalExt + $totalOther + $totalTrans;


									$totalCasesBeActed = $totalCarryOver + $totalSubmitted;

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryTerm' => $totalCarryTerm,
													'totalCarryRevoc' => $totalCarryRevoc, 
													'totalCarryExt' => $totalCarryExt,
													'totalCarryTrans' => $totalCarryTrans,
													'totalCarryOth' => $totalCarryOth,
													'totalCarryOver'=> $totalCarryOver,
													'totalTerm'=> $totalTerm,
													'totalRevoc'=> $totalRevoc,
													'totalExt'=> $totalExt,
													'totalOther'=> $totalOther,
													'totalTrans'=> $totalTrans,
													'totalSubmitted'=> $totalSubmitted,
													'totalCasesBeActed'=> $totalCasesBeActed,
										);
							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f5_regional_ps_r3':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							// (A)
							
							$totalCarryOver = 0;
							// (B)
							$totalRcv = 0;
							// (C)
							$totalCasesHandled = 0;
							// (D)
							$totalCompltd = 0;
							// (E)
							$totalCourtesy = 0;

							if($fieldOffice->status == 'SUCCESS'){
								
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									// (A) CARRY OVER
									$payload->table = "F5T12"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOver += $upon->count;

									
									// (B) COURTESY REFERRALS RECEIVED	
									$payload->table = "F5T13_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcv += $upon->count;

									//( C)
									$totalCasesHandled = $totalCarryOver + $totalRcv;

									// (D) DISPOSED
									$payload->table = "F5T13_TERM"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCompltd += $upon->count;
									
									// (E)
									$totalCourtesy = $totalCasesHandled - $totalCompltd;

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOver'=> $totalCarryOver,
													'totalRcv'=> $totalRcv,
													'totalCasesHandled'=> $totalCasesHandled,
													'totalCompltd'=> $totalCompltd,
													'totalCourtesy'=> $totalCourtesy,
										);
							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_regional_ppi_r1_p1':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							
							// (A)
							$totalCarryOver = 0;
							
							// (B)
							$totalRcv = 0;

							// (C)
							$totalCasesHandled = 0;


							if($fieldOffice->status == 'SUCCESS'){
								
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									// (A) CARRY OVER
									$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOver += $upon->count;

									
									// (B) COURTESY REFERRALS RECEIVED	
									$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcv += $upon->count;

									//( C)
									$totalCasesHandled = $totalCarryOver + $totalRcv;


								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOver'=> $totalCarryOver,
													'totalRcv'=> $totalRcv,
													'totalCasesHandled'=> $totalCasesHandled
										);
							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;


				case 'f21_regional_ppi_r1_p2':
						
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							if($fieldOffice->status == 'SUCCESS'){
								
								$totalGrant = 0;
								$totalDenial = 0;
								$commutationTotalGrant = 0;
								$commutationTotalDenial = 0;
								$absoluteTotalGrant = 0;
								$totalppir = 0;

								$totalCarryOver = 0;
								$totalRcv = 0;
								$totalInvestigation = 0;
								$totalRcv2 = 0;

								foreach ($fieldOffice->payload as $key1 => $value1){


									$payload->filter = ""; $payload->table = "F21T1"; $payload->field_office = $value1->NAME; $payload->filter_val="";$payload->filter_field="";
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;

									$payload->filter = ""; $payload->table = "F21T2_RCV"; $payload->field_office = $value1->NAME;$payload->filter_val="";$payload->filter_field="";
									$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRcv += $rcv->count;

									$totalRcv2 = $totalRcv;
									$totalInvestigation = $totalCarryOver + $totalRcv;

									///////
									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; $payload->field_office = $value1->NAME;
									$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalGrant += $grant_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; $payload->field_office = $value1->NAME;
									$denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDenial += $denial_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; $payload->field_office = $value1->NAME;
									$commutation_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$commutationTotalGrant += $commutation_grant_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; $payload->field_office = $value1->NAME;
									$commutation_denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$commutationTotalDenial += $commutation_denial_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; $payload->field_office = $value1->NAME;
									$absolute_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$absoluteTotalGrant += $absolute_grant_upon->count;


									$totalppir = $totalGrant + $totalDenial + $commutationTotalGrant + $commutationTotalDenial + $absoluteTotalGrant;

								}

									$data[] = array('REGION' => $value->VALUE_,
													'totalGrant'=> $totalGrant,
													'totalDenial'=> $totalDenial,
													'commutationTotalGrant' => $commutationTotalGrant,
													'commutationTotalDenial'=> $commutationTotalDenial,
													'absoluteTotalGrant'=> $absoluteTotalGrant,
													'totalppir'=> $totalppir,
										);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_regional_ppi_r1_p3':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							// (A)
							$totalCarryOver = 0;
							
							// (B)
							$totalRcv = 0;

							// (C)
							$totalCasesHandled = 0;


							// (D1)
							$totalInvestigationReport = 0;
							// (D2)
							$totalTransfer = 0;
							
							$totalOther = 0;

							$totalActed = 0;

							$totalActive = 0;

							if($fieldOffice->status == 'SUCCESS'){
								
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									// (A) CARRY OVER
									$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOver += $upon->count;

									
									// (B) COURTESY REFERRALS RECEIVED	
									$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcv += $upon->count;

									//( C)
									$totalCasesHandled = $totalCarryOver + $totalRcv;


									// (D1)
									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "-"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalInvestigationReport += $upon->count;

									// (D2)
									$payload->table = "F21T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTransfer += $upon->count;

									
									$totalActed = $totalInvestigationReport  + $totalTransfer;

									$totalActive = $totalCasesHandled - $totalActed;

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalInvestigationReport'=> $totalInvestigationReport,
													'totalTransfer'=> $totalTransfer,
													'totalOther'=> $totalOther,
													'totalActed'=> $totalActed,
													'totalActive' => $totalActive,
										);
							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f21_regional_ppi_r2_p1':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							if($fieldOffice->status == 'SUCCESS'){
									//(A)
									$totalCasestobeActed = 0;
									$totalCarryOverPendingResolution = 0;
									// (B)

									$totalGrant = 0;
									$totalDenial = 0;
									$totalParole = 0;
									$commutationTotalGrant = 0;
									$commutationTotalDenial = 0;
									$totalCommutation = 0;
									$absoluteTotalGrant = 0;
									$totalRefOthers = 0;

									$totalReportSubmitted = 0;
									$totalCasestobeActedUpon = 0;
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T3"; $payload->filter_field = ""; $payload->filter_val = "Pardon - For Denial"; $payload->field_office = $value1->NAME;
									$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverPendingResolution += $grant_upon->count;

									//(B)
									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; $payload->field_office = $value1->NAME;
									$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalGrant += $grant_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; $payload->field_office = $value1->NAME;
									$denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDenial += $denial_upon->count;
									
									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; $payload->field_office = $value1->NAME;
									$commutation_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$commutationTotalGrant += $commutation_grant_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; $payload->field_office = $value1->NAME;
									$commutation_denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$commutationTotalDenial += $commutation_denial_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; $payload->field_office = $value1->NAME;
									$absolute_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$absoluteTotalGrant += $absolute_grant_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Other"; $payload->additional_operator = "="; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure2($payload));
									$totalRefOthers += $upon->count;

									$totalParole = $totalGrant + $totalDenial;
									$totalCommutation = $commutationTotalGrant + $commutationTotalDenial;
									$totalReportSubmitted = $totalParole + $totalCommutation + $absoluteTotalGrant + $totalRefOthers;
									$totalCasestobeActedUpon = $totalCarryOverPendingResolution + $totalReportSubmitted;
								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOverPendingResolution'=> $totalCarryOverPendingResolution,
													'totalParole'=> $totalParole,
													'totalCommutation'=> $totalCommutation,
													'absoluteTotalGrant'=> $absoluteTotalGrant,
													'totalRefOthers'=> $totalRefOthers,
													'totalReportSubmitted'=> $totalReportSubmitted,
													'totalCasestobeActedUpon'=> $totalCasestobeActedUpon,
										);

							}

						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;


				case 'f21_regional_ppi_r3':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							if($fieldOffice->status == 'SUCCESS'){
									//(A)
									$totalCarryOverCI = 0;
									// (B)
									$totalRcvCI = 0;
									$totalCountCI = 0;
									$totalCmpltdCI = 0;
									$totalCountActiveCI = 0;
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T5"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverCI += $upon->count;

									$payload->table = "F21T6_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcvCI += $upon->count;

									$totalCountCI = $totalCarryOverCI + $totalRcvCI;		

									$payload->table = "F21T6_CMPLTD"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCmpltdCI += $upon->count;

									$totalCountActiveCI = $totalCountCI - $totalCmpltdCI;		

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOverCI'=> $totalCarryOverCI,
													'totalRcvCI'=> $totalRcvCI,
													'totalCountCI'=> $totalCountCI,
													'totalCmpltdCI'=> $totalCmpltdCI,
													'totalCountActiveCI'=> $totalCountActiveCI,
										);


							}

						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_regional_ppi_r4_p1':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							if($fieldOffice->status == 'SUCCESS'){
								
									$totalCarryOverInvestigation = 0;
									$totalRcvTotalInvestigation = 0;
									$totalCasesHandled = 0;
									$totalRefParoleGrantActed = 0;
									$totalRefParoleDeniedActed = 0;
									$totalRefCommGrantActed = 0;
									$totalRefCommDeniedActed = 0;
									$totalRefCondGrantActed = 0;
									$totalRefCondDeniedActed = 0;
								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverInvestigation += $upon->count;

									$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcvTotalInvestigation += $upon->count;
									
									$totalCasesHandled = $totalCarryOverInvestigation + $totalRcvTotalInvestigation;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefParoleGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefParoleDeniedActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCommGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCommDeniedActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCondGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCondDeniedActed += $upon->count;

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOverInvestigation'=> $totalCarryOverInvestigation,
													'totalRcvTotalInvestigation'=> $totalRcvTotalInvestigation,
													'totalCasesHandled'=> $totalCasesHandled,
													'totalRefParoleGrantActed'=> $totalRefParoleGrantActed,
													'totalRefParoleDeniedActed'=> $totalRefParoleDeniedActed,
													'totalRefCommGrantActed'=> $totalRefCommGrantActed,
													'totalRefCommDeniedActed'=> $totalRefCommDeniedActed,
													'totalRefCondGrantActed'=> $totalRefCondGrantActed,
													'totalRefCondDeniedActed'=> $totalRefCondDeniedActed,
										);



							}

						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_regional_ppi_r4_p2':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							if($fieldOffice->status == 'SUCCESS'){
								
									$totalCarryOverInvestigation = 0;
									$totalRcvTotalInvestigation = 0;
									$totalCasesHandled = 0;
									$totalRefParoleGrantActed = 0;
									$totalRefParoleDeniedActed = 0;
									$totalRefCommGrantActed = 0;
									$totalRefCommDeniedActed = 0;
									$totalRefCondGrantActed = 0;
									$totalRefCondDeniedActed = 0;
									$totalRefAbsGrantActed = 0;
									$totalRefAbsDeniedActed = 0;
									$totalPPIR = 0;
									$totalTransferredSubmitted = 0;
									$totalRefOthers = 0;
									$totalInvRef = 0;
									$disRate = 0;
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverInvestigation += $upon->count;

									$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcvTotalInvestigation += $upon->count;
									
									$totalCasesHandled = $totalCarryOverInvestigation + $totalRcvTotalInvestigation;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefParoleGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefParoleDeniedActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCommGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCommDeniedActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCondGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCondDeniedActed += $upon->count;
									
									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefAbsGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefAbsDeniedActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTransferredSubmitted += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Other"; $payload->additional_operator = "="; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefOthers += $upon->count;

									$totalPPIR = $totalRefParoleGrantActed +
										$totalRefParoleDeniedActed +
										$totalRefCommGrantActed +
										$totalRefCommDeniedActed +
										$totalRefCondGrantActed +
										$totalRefCondDeniedActed +
										$totalRefAbsGrantActed +
										$totalRefAbsDeniedActed ;
									
									$totalInvRef = $totalPPIR + $totalTransferredSubmitted + $totalRefOthers;

									// $disRate = $totalInvRef / $totalPPIR;
								}

									$data[] = array('REGION' => $value->VALUE_,
													'totalRefAbsGrantActed'=> $totalRefAbsGrantActed,
													'totalRefAbsDeniedActed'=> $totalRefAbsDeniedActed,
													'totalPPIR'=> $totalPPIR,
													'totalTransferredSubmitted'=> $totalTransferredSubmitted,
													'totalRefOthers'=> $totalRefOthers,
													'totalInvRef'=> $totalInvRef,
													// 'disRate'=> $disRate,
										);
							}

						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f21_regional_pr_pd_r1_p1':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							
							
							if($fieldOffice->status == 'SUCCESS'){
								$totalCarryOverSupvParolIV = 0;
								$totalCarryOverSupvPardonIV = 0;
								$totalCarryOverSupvIV = 0;
								$totalRefRcvSupvParol = 0;
								$totalRefRcvSupvPardon = 0;
								$totalRefRcvSupv = 0;

								$totalSupervCasesHandledPR = 0;
								$totalSupervCasesHandledPD = 0;
								$totalSupervCasesHandled = 0;
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T7_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvParolIV += $upon->count;

									$payload->table = "F21T7_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvPardonIV += $upon->count;

									$totalCarryOverSupvIV = $totalCarryOverSupvParolIV + $totalCarryOverSupvPardonIV;		
									
									$payload->table = "F21T8_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefRcvSupvParol += $upon->count;

									$payload->table = "F21T8_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefRcvSupvPardon += $upon->count;

									$totalRefRcvSupv           = $totalRefRcvSupvParol       + $totalRefRcvSupvPardon;	
									$totalSupervCasesHandledPR = $totalCarryOverSupvParolIV  + $totalRefRcvSupvParol;
									$totalSupervCasesHandledPD = $totalCarryOverSupvPardonIV + $totalRefRcvSupvPardon;
									$totalSupervCasesHandled   = $totalSupervCasesHandledPR  + $totalSupervCasesHandledPD;
								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOverSupvParolIV'=> $totalCarryOverSupvParolIV,
													'totalCarryOverSupvPardonIV'=> $totalCarryOverSupvPardonIV,
													'totalCarryOverSupvIV'=> $totalCarryOverSupvIV,
													'totalRefRcvSupvParol'=> $totalRefRcvSupvParol,
													'totalRefRcvSupvPardon'=> $totalRefRcvSupvPardon,
													'totalRefRcvSupv'=> $totalRefRcvSupv,
													'totalSupervCasesHandledPR'=> $totalSupervCasesHandledPR,
													'totalSupervCasesHandledPD'=> $totalSupervCasesHandledPD,
													'totalSupervCasesHandled'=> $totalSupervCasesHandled,
													
										);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f21_regional_pr_pd_r1_p2':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							
							if($fieldOffice->status == 'SUCCESS'){
								$totalCarryOverSupvParolIV = 0;
								$totalCarryOverSupvPardonIV = 0;
								$totalCarryOverSupvIV = 0;
								$totalRefRcvSupvParol = 0;
								$totalRefRcvSupvPardon = 0;
								$totalCasesResolvedFinal = 0;
								$totalCasesResolvedArrest = 0;
								$totalCasesResolvedDeath = 0;
								$totalCasesResolvedPSOther = 0;
								$totalCasesDropPR = 0;
								$totalCasesDropPD = 0;
								$totalCasesDrop = 0;

								$totalSupervCasesHandledPR = 0;
								$totalSupervCasesHandledPD = 0;
								$totalSupervCasesHandled = 0;

								$totalActiveSuperVisionPR = 0;
								$totalActiveSuperVisionPD = 0;
								$totalActiveSuperVision = 0;

								$totalCarryOverSupvParolIV = 0;
								$totalCasesResolvedFinalParol = 0;
								$totalCasesResolvedFinalPardon = 0;
								$totalCasesResolvedArrestParol = 0;

								$totalCasesResolvedArrestPardon = 0;
								$totalCasesResolvedDeathParol = 0;
								$totalCasesResolvedDeathPardon = 0;
								$totalCasesResolvedRegionalParol = 0;
								$totalCasesResolvedRegionalPardon = 0;
								$totalCasesResolvedOtherParol = 0;
								$totalCasesResolvedOtherPardon = 0;
								$totalCasesResolvedPSOther = 0;


								foreach ($fieldOffice->payload as $key1 => $value1){


									$payload->table = "F21T7_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvParolIV += $upon->count;

									$payload->table = "F21T7_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvPardonIV += $upon->count;

									$totalCarryOverSupvIV = $totalCarryOverSupvParolIV + $totalCarryOverSupvPardonIV;		
									
									$payload->table = "F21T8_PAROL"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefRcvSupvParol += $upon->count;

									$payload->table = "F21T8_PARDON"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefRcvSupvPardon += $upon->count;

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedFinalParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedFinalPardon += $upon->count;

									$totalCasesResolvedFinal = $totalCasesResolvedFinalParol + $totalCasesResolvedFinalPardon;	

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedArrestParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedArrestPardon += $upon->count;
									$totalCasesResolvedArrest = $totalCasesResolvedArrestParol + $totalCasesResolvedArrestPardon;		
									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedDeathParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedDeathPardon += $upon->count;
									$totalCasesResolvedDeath = $totalCasesResolvedDeathParol + $totalCasesResolvedDeathPardon;

									$payload->table = "F21T13_PAROL"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedRegionalParol += $upon->count;

									$payload->table = "F21T13_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedRegionalPardon += $upon->count;
									$totalCasesResolvedRegional = $totalCasesResolvedRegionalParol + $totalCasesResolvedRegionalPardon;		

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedOtherParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedOtherPardon += $upon->count;
									$totalCasesResolvedPSOther = $totalCasesResolvedOtherParol + $totalCasesResolvedOtherPardon;	

									$totalSupervCasesHandledPR = $totalCarryOverSupvParolIV + $totalRefRcvSupvParol;
									$totalSupervCasesHandledPD = $totalCarryOverSupvPardonIV + $totalRefRcvSupvPardon;
									$totalSupervCasesHandled   = $totalSupervCasesHandledPR  + $totalSupervCasesHandledPD;

									$totalCasesDropPR = $totalCasesResolvedFinalParol +
														$totalCasesResolvedArrestParol +
														$totalCasesResolvedDeathParol +
														$totalCasesResolvedRegionalParol +
														$totalCasesResolvedOtherParol;

									$totalCasesDropPD = $totalCasesResolvedFinalPardon +
														$totalCasesResolvedArrestPardon +
														$totalCasesResolvedDeathPardon +
														$totalCasesResolvedRegionalPardon +
														$totalCasesResolvedOtherPardon;
									$totalCasesDrop   = $totalCasesDropPR + $totalCasesDropPD;
									
									$totalActiveSuperVisionPR = $totalSupervCasesHandledPR - $totalCasesDropPR;
									$totalActiveSuperVisionPD = $totalSupervCasesHandledPD - $totalCasesDropPD;
									$totalActiveSuperVision = $totalActiveSuperVisionPR + $totalActiveSuperVisionPD;

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalSupervCasesHandledPR'=> $totalSupervCasesHandledPR,
													'totalSupervCasesHandledPD'=> $totalSupervCasesHandledPD,
													'totalSupervCasesHandled'=> $totalSupervCasesHandled,
													'totalCasesResolvedFinalParol'=> $totalCasesResolvedFinalParol,
													'totalCasesResolvedFinalPardon'=> $totalCasesResolvedFinalPardon,
													'totalCasesResolvedFinal'=> $totalCasesResolvedFinal,
													'totalCasesResolvedArrestParol'=> $totalCasesResolvedArrestParol,
													'totalCasesResolvedArrestPardon'=> $totalCasesResolvedArrestPardon,
													'totalCasesResolvedArrest'=> $totalCasesResolvedArrest,
													'totalCasesResolvedDeathParol'=> $totalCasesResolvedDeathParol,
													'totalCasesResolvedDeathPardon'=> $totalCasesResolvedDeathPardon,
													'totalCasesResolvedDeath'=> $totalCasesResolvedDeath,
													'totalCasesResolvedRegionalParol'=> $totalCasesResolvedRegionalParol,
													'totalCasesResolvedRegionalPardon'=> $totalCasesResolvedRegionalPardon,
													'totalCasesResolvedRegional'=> $totalCasesResolvedRegional,
													'totalCasesResolvedOtherParol'=> $totalCasesResolvedOtherParol,
													'totalCasesResolvedOtherPardon'=> $totalCasesResolvedOtherPardon,
													'totalCasesResolvedPSOther'=> $totalCasesResolvedPSOther,
													'totalCasesDropPR'=> $totalCasesDropPR,
													'totalCasesDropPD'=> $totalCasesDropPD,
													'totalCasesDrop' => $totalCasesDrop,
													'totalActiveSuperVisionPR'=> $totalActiveSuperVisionPR,
													'totalActiveSuperVisionPD'=> $totalActiveSuperVisionPD,
													'totalActiveSuperVision' => $totalActiveSuperVision,
													 
										);


							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	

				case 'f21_regional_pr_pd_r1_p3':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
								
							if($fieldOffice->status == 'SUCCESS'){
								$SupvActedSummaryTotal = 0;
								$SupvActedInfraTotal = 0;
								$SupvActedDeathTotal = 0;
								$SupvActedOtherTotal = 0;
								$totalPRSB = 0;
								$totalPD = 0;
								$totalPR = 0;
								$totalPDandPR = 0;
								$totalCasesActedUponByPPO = 0;
								$SupvActedSummaryParol = 0;

								$SupvActedSummaryPardon = 0;
								$SupvActedInfraParol = 0;
								$SupvActedInfraPardon = 0;
								$SupvActedDeathParol = 0;
								$SupvActedDeathPardon = 0;
								$SupvActedOtherParol = 0;
								$SupvActedOtherPardon = 0;
								$SupvActedSummaryParol = 0;
								$SupvActedSummaryParol = 0;
								
								foreach ($fieldOffice->payload as $key1 => $value1){


									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; $payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedSummaryParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedSummaryPardon += $upon->count;
									$SupvActedSummaryTotal = $SupvActedSummaryParol + $SupvActedSummaryPardon;		

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedInfraParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedInfraPardon += $upon->count;
									$SupvActedInfraTotal = $SupvActedInfraParol + $SupvActedInfraPardon;	

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedDeathParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedDeathPardon += $upon->count;
									$SupvActedDeathTotal = $SupvActedDeathParol + $SupvActedDeathPardon;		

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedOtherParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedOtherPardon += $upon->count;
									$SupvActedOtherTotal = $SupvActedOtherParol + $SupvActedOtherPardon;	

									$totalPRSB = $SupvActedSummaryTotal +
												$SupvActedInfraTotal +
												$SupvActedDeathTotal +
												$SupvActedOtherTotal;

									$totalPR = $SupvActedSummaryParol +
												$SupvActedInfraParol +
												$SupvActedDeathParol +
												$SupvActedOtherParol;

									$totalPD = $SupvActedSummaryPardon +
												$SupvActedInfraPardon +
												$SupvActedDeathPardon +
												$SupvActedOtherPardon;

									$totalPDandPR = $totalPR + $totalPD;
									$totalCasesActedUponByPPO = $totalPRSB + $totalPDandPR;
								}
									$data[] = array('REGION' => $value->VALUE_,
													'SupvActedSummaryParol'=> $SupvActedSummaryParol,
													'SupvActedSummaryPardon'=> $SupvActedSummaryPardon,
													'SupvActedSummaryTotal'=> $SupvActedSummaryTotal,
													'SupvActedInfraParol'=> $SupvActedInfraParol,
													'SupvActedInfraPardon'=> $SupvActedInfraPardon,
													'SupvActedInfraTotal'=> $SupvActedInfraTotal,
													'SupvActedDeathParol'=> $SupvActedDeathParol,
													'SupvActedDeathPardon'=> $SupvActedDeathPardon,
													'SupvActedDeathTotal'=> $SupvActedDeathTotal,
													'SupvActedOtherParol'=> $SupvActedOtherParol,
													'SupvActedOtherPardon'=> $SupvActedOtherPardon,
													'SupvActedOtherTotal'=> $SupvActedOtherTotal,
													'totalPRSB'=> $totalPRSB,
													'totalPR'=> $totalPR,
													'totalPD'=> $totalPD,
													'totalPDandPR'=> $totalPDandPR,
													'totalCasesActedUponByPPO'=> $totalCasesActedUponByPPO,
										);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	

				case 'f21_regional_pr_pd_r2_p1':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
								
							if($fieldOffice->status == 'SUCCESS'){
								
								$totalCarryOverPending = 0;
								$SupvActedSummaryTotal = 0;
								$SupvActedInfraTotal = 0;
								$SupvActedDeathTotal = 0;
								$SupvActedOtherTotal = 0;
								$totalReportSubmitted = 0;

								$totalPD = 0;
								$totalPR = 0;
								$totalPDandPR = 0;

								$totalCarryOverPendingParol = 0;
								$totalCarryOverPendingPardon = 0;
								$SupvActedSummaryParol = 0;
								$SupvActedSummaryPardon = 0;
								$SupvActedInfraParol = 0;
								$SupvActedInfraPardon = 0;
								$SupvActedDeathParol = 0;
								$SupvActedDeathPardon = 0;
								$SupvActedOtherParol = 0;
								$SupvActedOtherPardon = 0;
								$SupvActedDeathParol = 0;

								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T10_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverPendingParol += $upon->count;

									$payload->table = "F21T10_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverPendingPardon += $upon->count;
									$totalCarryOverPending = $totalCarryOverPendingParol + $totalCarryOverPendingPardon;	

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; $payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedSummaryParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedSummaryPardon += $upon->count;
									$SupvActedSummaryTotal = $SupvActedSummaryParol + $SupvActedSummaryPardon;		

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedInfraParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedInfraPardon += $upon->count;
									$SupvActedInfraTotal = $SupvActedInfraParol + $SupvActedInfraPardon;		

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedDeathParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedDeathPardon += $upon->count;
									$SupvActedDeathTotal = $SupvActedDeathParol + $SupvActedDeathPardon;		

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedOtherParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedOtherPardon += $upon->count;
									$SupvActedOtherTotal = $SupvActedOtherParol + $SupvActedOtherPardon;

									$totalReportSubmitted = $SupvActedSummaryTotal +
												$SupvActedInfraTotal +
												$SupvActedDeathTotal +
												$SupvActedOtherTotal
											;

									$totalPR = $totalCarryOverPendingParol +
												$SupvActedSummaryParol +
												$SupvActedInfraParol +
												$SupvActedDeathParol +
												$SupvActedOtherParol
											;

									$totalPD = $totalCarryOverPendingPardon +
												$SupvActedSummaryPardon +
												$SupvActedInfraPardon +
												$SupvActedDeathPardon +
												$SupvActedOtherPardon 
											;

									$totalPRandPD = $totalPR + $totalPD;

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOverPendingParol'=> $totalCarryOverPendingParol,
													'totalCarryOverPendingPardon'=> $totalCarryOverPendingPardon,
													'totalCarryOverPending'=> $totalCarryOverPending,
													'SupvActedSummaryParol'=> $SupvActedSummaryParol,
													'SupvActedSummaryPardon'=> $SupvActedSummaryPardon,
													'SupvActedSummaryTotal'=> $SupvActedSummaryTotal,
													'SupvActedInfraParol'=> $SupvActedInfraParol,
													'SupvActedInfraPardon'=> $SupvActedInfraPardon,
													'SupvActedInfraTotal'=> $SupvActedInfraTotal,
													'SupvActedDeathParol'=> $SupvActedDeathParol,
													'SupvActedDeathPardon'=> $SupvActedDeathPardon,
													'SupvActedDeathTotal'=> $SupvActedDeathTotal,
													'SupvActedOtherParol'=> $SupvActedOtherParol,
													'SupvActedOtherPardon'=> $SupvActedOtherPardon,
													'SupvActedOtherTotal'=> $SupvActedOtherTotal,
													'totalReportSubmitted'=> $totalReportSubmitted,
													'totalPR'=> $totalPR,
													'totalPD'=> $totalPD,
													'totalPRandPD'=> $totalPRandPD,
										);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	
				case 'f21_regional_pr_pd_r2_p2':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
								
							if($fieldOffice->status == 'SUCCESS'){
								
								$totalCarryOverPending = 0;
								$SupvActedSummaryTotal = 0;
								$SupvActedInfraTotal = 0;
								$SupvActedDeathTotal = 0;
								$SupvActedOtherTotal = 0;
								$totalReportSubmitted = 0;

								$totalPD = 0;
								$totalPR = 0;
								$totalPDandPR = 0;

								$totalCarryOverPendingParol = 0;
								$totalCarryOverPendingPardon = 0;
								$SupvActedSummaryParol = 0;
								$SupvActedSummaryPardon = 0;
								$SupvActedInfraParol = 0;
								$SupvActedInfraPardon = 0;
								$SupvActedDeathParol = 0;
								$SupvActedDeathPardon = 0;
								$SupvActedOtherParol = 0;
								$SupvActedOtherPardon = 0;
								$SupvActedDeathParol = 0;

								$totalCasesResolvedFinalParol = 0;
								$totalCasesResolvedFinalPardon = 0;
								$totalCasesResolvedArrestParol = 0;
								$totalCasesResolvedArrestPardon = 0;
								$totalCasesResolvedDeathParol = 0;
								$totalCasesResolvedDeathPardon = 0;
								$totalCasesResolvedOtherParol = 0;
								$totalCasesResolvedOtherPardon = 0;

								$totalPRCasesResolved = 0;
								$totalPDCasesResolved = 0;
								$totalCasesResolved = 0;
								$totalPRandPD = 0;
								$totalCasesPendingPR = 0;
								$totalCasesPendingPD = 0;
								$totalCasesPending = 0;

								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T10_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverPendingParol += $upon->count;

									$payload->table = "F21T10_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverPendingPardon += $upon->count;
									$totalCarryOverPending = $totalCarryOverPendingParol + $totalCarryOverPendingPardon;	

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; $payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedSummaryParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedSummaryPardon += $upon->count;
									$SupvActedSummaryTotal = $SupvActedSummaryParol + $SupvActedSummaryPardon;		

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedInfraParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedInfraPardon += $upon->count;
									$SupvActedInfraTotal = $SupvActedInfraParol + $SupvActedInfraPardon;		

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedDeathParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedDeathPardon += $upon->count;
									$SupvActedDeathTotal = $SupvActedDeathParol + $SupvActedDeathPardon;		

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedOtherParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$SupvActedOtherPardon += $upon->count;
									$SupvActedOtherTotal = $SupvActedOtherParol + $SupvActedOtherPardon;

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL"; $payload->field_office = $value1->NAME;  
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedFinalParol += $upon->count;
									///////////////////
									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL"; $payload->field_office = $value1->NAME;  
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedFinalPardon += $upon->count;
									$totalCasesResolvedFinal = $totalCasesResolvedFinalParol + $totalCasesResolvedFinalPardon;	

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedArrestParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedArrestPardon += $upon->count;
									$totalCasesResolvedArrest = $totalCasesResolvedArrestParol + $totalCasesResolvedArrestPardon;	

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedDeathParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedDeathPardon += $upon->count;
									$totalCasesResolvedDeath = $totalCasesResolvedDeathParol + $totalCasesResolvedDeathPardon;	

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedOtherParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedOtherPardon += $upon->count;
									$totalCasesResolvedPSOther = $totalCasesResolvedOtherParol + $totalCasesResolvedOtherPardon;

									$totalReportSubmitted = $SupvActedSummaryTotal +
												$SupvActedInfraTotal +
												$SupvActedDeathTotal +
												$SupvActedOtherTotal
											;

									$totalPR = $totalCarryOverPendingParol +
												$SupvActedSummaryParol +
												$SupvActedInfraParol +
												$SupvActedDeathParol +
												$SupvActedOtherParol
											;

									$totalPD = $totalCarryOverPendingPardon +
												$SupvActedSummaryPardon +
												$SupvActedInfraPardon +
												$SupvActedDeathPardon +
												$SupvActedOtherPardon 
											;

									$totalPRandPD = $totalPR + $totalPD;
									/////////////

									$totalPRCasesResolved = $totalCasesResolvedFinalParol +
											$totalCasesResolvedArrestParol +
											$totalCasesResolvedDeathParol +
											$totalCasesResolvedOtherParol
										;
									$totalPDCasesResolved = $totalCasesResolvedFinalPardon +
												$totalCasesResolvedArrestPardon +
												$totalCasesResolvedDeathPardon +
												$totalCasesResolvedOtherPardon
											;
									$totalCasesResolved = $totalPRCasesResolved + $totalPDCasesResolved;
									$totalPRandPD = $totalPR + $totalPD;

									$totalCasesPendingPR = $totalPR - $totalPRCasesResolved;
									$totalCasesPendingPD = $totalPD - $totalPDCasesResolved;
									$totalCasesPending = $totalCasesPendingPR + $totalCasesPendingPD;
								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalPR'=> $totalPR,
													'totalPD'=> $totalPD,
													'totalPRandPD'=> $totalPRandPD,
													'totalCasesResolvedFinalParol'=> $totalCasesResolvedFinalParol,
													'totalCasesResolvedFinalPardon'=> $totalCasesResolvedFinalPardon,
													'totalCasesResolvedFinal'=> $totalCasesResolvedFinal,
													'totalCasesResolvedArrestParol'=> $totalCasesResolvedArrestParol,
													'totalCasesResolvedArrestPardon'=> $totalCasesResolvedArrestPardon,
													'totalCasesResolvedArrest'=> $totalCasesResolvedArrest,
													'totalCasesResolvedDeathParol'=> $totalCasesResolvedDeathParol,
													'totalCasesResolvedDeathPardon'=> $totalCasesResolvedDeathPardon,
													'totalCasesResolvedDeath'=> $totalCasesResolvedDeath,
													'totalCasesResolvedOtherParol'=> $totalCasesResolvedOtherParol,
													'totalCasesResolvedOtherPardon'=> $totalCasesResolvedOtherPardon,
													'totalCasesResolvedPSOther'=> $totalCasesResolvedPSOther,
													'totalPRCasesResolved'=> $totalPRCasesResolved,
													'totalPDCasesResolved'=> $totalPDCasesResolved,
													'totalCasesResolved'=> $totalCasesResolved,
													'totalCasesPendingPR'=> $totalCasesPendingPR,
													'totalCasesPendingPD'=> $totalCasesPendingPD,
													'totalCasesPending'=> $totalCasesPending,
										);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	

				case 'f21_regional_pr_pd_r3':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							
							if($fieldOffice->status == 'SUCCESS'){
								
								$totalCarryOverPendingRegional = 0;
								$totalReportSubmittedRegional = 0;
								$totalCasesResolvedRegional = 0;
								$totalCasesPendingResolutionRegionalParol = 0;
								$totalCasesPendingResolutionRegionalPardon = 0;
								$totalCasesPendingResolutionRegional = 0;

								$totalCarryOverPendingRegionalParol = 0;
								$totalCarryOverPendingRegionalPardon = 0;
								$totalReportSubmittedRegionalParol = 0;
								$totalReportSubmittedRegionalPardon = 0;
								$totalCasesResolvedRegionalParol = 0;
								$totalCasesResolvedRegionalPardon = 0;
								$totalCasesPendingResolutionRegionalParol = 0;
								$totalCasesPendingResolutionRegionalPardon = 0;
								$totalCasesPendingResolutionRegional = 0;



								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T12_PAROL"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverPendingRegionalParol += $upon->count;

									$payload->table = "F21T12_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverPendingRegionalPardon += $upon->count;
									$totalCarryOverPendingRegional = $totalCarryOverPendingRegionalParol + $totalCarryOverPendingRegionalPardon;

									$payload->table = "F21T9_PAROL"; $payload->filter_field = "transfer"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalReportSubmittedRegionalParol += $upon->count;

									$payload->table = "F21T9_PARDON"; $payload->filter_field = "transfer"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalReportSubmittedRegionalPardon += $upon->count;
									$totalReportSubmittedRegional = $totalReportSubmittedRegionalParol + $totalReportSubmittedRegionalPardon;	

									$payload->table = "F21T13_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedRegionalParol += $upon->count;

									$payload->table = "F21T13_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedRegionalPardon += $upon->count;
									$totalCasesResolvedRegional = $totalCasesResolvedRegionalParol + $totalCasesResolvedRegionalPardon;	

									$totalCasesPendingResolutionRegionalParol = $totalCarryOverPendingRegionalParol - $totalCasesResolvedRegionalParol;		
									$totalCasesPendingResolutionRegionalPardon = $totalCarryOverPendingRegionalPardon - $totalCasesResolvedRegionalPardon;		
									$totalCasesPendingResolutionRegional = $totalCasesPendingResolutionRegionalParol + $totalCasesPendingResolutionRegionalPardon;		

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOverPendingRegionalParol'=> $totalCarryOverPendingRegionalParol,
													'totalCarryOverPendingRegionalPardon'=> $totalCarryOverPendingRegionalPardon,
													'totalCarryOverPendingRegional'=> $totalCarryOverPendingRegional,
													'totalReportSubmittedRegionalParol'=> $totalReportSubmittedRegionalParol,
													'totalReportSubmittedRegionalPardon'=> $totalReportSubmittedRegionalPardon,
													'totalReportSubmittedRegional'=> $totalReportSubmittedRegional,
													'totalCasesResolvedRegionalParol'=> $totalCasesResolvedRegionalParol,
													'totalCasesResolvedRegionalPardon'=> $totalCasesResolvedRegionalPardon,
													'totalCasesResolvedRegional'=> $totalCasesResolvedRegional,
													'totalCasesPendingResolutionRegionalParol'=> $totalCasesPendingResolutionRegionalParol,
													'totalCasesPendingResolutionRegionalPardon'=> $totalCasesPendingResolutionRegionalPardon,
													'totalCasesPendingResolutionRegional'=> $totalCasesPendingResolutionRegional,

										);

							}	
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	
				case 'f21_regional_pr_pd_r4':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							
							if($fieldOffice->status == 'SUCCESS'){
								
								$totalCarryOverSupv = 0;
								$totalRcvSupv = 0;
								$totalCourtesySupvParol = 0;
								$totalCourtesySupvPardon = 0;
								$totalCourtesySupvTotal = 0;
								$totalTermSupv = 0;
								$totalActCourtesySupvParol = 0;
								$totalActCourtesySupvPardon = 0;
								$totalActCourtesySupv = 0;

								$totalCarryOverSupvParol = 0;
								$totalCarryOverSupvPardon = 0;
								$totalRcvSupvParol = 0;
								$totalRcvSupvPardon = 0;

								$totalTermSupvParol = 0;
								$totalTermSupvPardon = 0;

								foreach ($fieldOffice->payload as $key1 => $value1){
									
									$payload->table = "F21T14_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvParol += $upon->count;

									$payload->table = "F21T14_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvPardon += $upon->count;
									$totalCarryOverSupv = $totalCarryOverSupvParol + $totalCarryOverSupvPardon;

									$payload->table = "F21T15_RCV_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcvSupvParol += $upon->count;

									$payload->table = "F21T15_RCV_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcvSupvPardon += $upon->count;
									$totalRcvSupv = $totalRcvSupvParol + $totalRcvSupvPardon;	

									$totalCourtesySupvParol  = $totalCarryOverSupvParol  + $totalRcvSupvParol;		
									$totalCourtesySupvPardon = $totalCarryOverSupvPardon + $totalRcvSupvPardon;		
									$totalCourtesySupvTotal  = $totalCourtesySupvParol   + $totalCourtesySupvPardon;		

									$payload->table = "F21T15_TERM_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTermSupvParol += $upon->count;

									$payload->table = "F21T15_TERM_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTermSupvPardon += $upon->count;
									$totalTermSupv = $totalTermSupvParol + $totalTermSupvPardon;

									$totalActCourtesySupvParol = $totalCourtesySupvParol - $totalTermSupvParol;		
									$totalActCourtesySupvPardon = $totalCourtesySupvPardon - $totalTermSupvPardon;		
									$totalActCourtesySupv = $totalActCourtesySupvParol + $totalActCourtesySupvPardon;		
									
								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOverSupvParol'=> $totalCarryOverSupvParol,
													'totalCarryOverSupvPardon'=> $totalCarryOverSupvPardon,
													'totalCarryOverSupv'=> $totalCarryOverSupv,
													'totalRcvSupvParol'=> $totalRcvSupvParol,
													'totalRcvSupvPardon'=> $totalRcvSupvPardon,
													'totalRcvSupv'=> $totalRcvSupv,
													'totalCourtesySupvParol'=> $totalCourtesySupvParol,
													'totalCourtesySupvPardon'=> $totalCourtesySupvPardon,
													'totalCourtesySupvTotal'=> $totalCourtesySupvTotal,
													'totalTermSupvParol'=> $totalTermSupvParol,
													'totalTermSupvPardon'=> $totalTermSupvPardon,
													'totalTermSupv'=> $totalTermSupv,
													'totalActCourtesySupvParol'=> $totalActCourtesySupvParol,
													'totalActCourtesySupvPardon'=> $totalActCourtesySupvPardon,
													'totalActCourtesySupv'=> $totalActCourtesySupv,

										);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	
				//FIELDS
				//@breakpoint
				case 'f5_field_office_pi_f1_p1':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						

						
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

						
						if($fieldOffice->status == 'SUCCESS'){

							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOver = 0;
								$totalRcv = 0;
								$reInvRef = 0;
								$totalRcvJRPI = 0;
								$totalRcvRPI = 0;
								$total = 0;
								$totalInvestigation = 0;

								$JCIL_a = 0;
								$JCIL_b = 0;
								$JCIL_total = 0;
								$adult_a = 0;
								$adult_b = 0;
								$adult_total = 0;
								$civil_total = 0;

								$payload->filter = ""; $payload->table = "F5T1"; $payload->field_office = $value1->NAME;
								$carryOver = json_decode($this->Cmis_F5PCS_model->carryOver($payload));
								$totalCarryOver += $carryOver->count;

								// civil JICL
								$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$rcvInvestigationJICLCivilNewJPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$JCIL_a += $rcvInvestigationJICLCivilNewJPI->count;
								
								$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$rcvInvestigationJICLCivilNewJTPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$JCIL_b += $rcvInvestigationJICLCivilNewJTPI->count;
								
								$JCIL_total = $JCIL_a + $JCIL_b;

								// civil Adult
								$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$rcvInvestigationAdultCivilNewJPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$adult_a += $rcvInvestigationAdultCivilNewJPI->count;
								
								$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1";
								$rcvInvestigationAdultCivilNewJTPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$adult_b += $rcvInvestigationAdultCivilNewJTPI->count;

								$adult_total = $adult_a + $adult_b;
								$civil_total = $JCIL_total + $adult_total;

								$military_JCIL_a = 0;
								$military_JCIL_b = 0;
								$military_JCIL_total = 0;
								$military_adult_a = 0;
								$military_adult_b = 0;
								$military_adult_total = 0;
								$military_total = 0;
								// military JICL
								$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$a = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$military_JCIL_a += $a->count;
								
								$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$b = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$military_JCIL_b += $b->count;
								
								$military_JCIL_total = $military_JCIL_a + $military_JCIL_b;

								// military Adult
								$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; 
								$c = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$military_adult_a += $c->count;
								
								$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0";
								$d = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$military_adult_b += $d->count;

								$military_adult_total = $military_adult_a + $military_adult_b;
								$military_total = $military_JCIL_total + $military_adult_total;

								$payload->filter = "JRPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
								$rcvJRPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalRcvJRPI += $rcvJRPI->count;

								$payload->filter = "RPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
								$rcvRPI= json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalRcvRPI += $rcvRPI->count;

								$reInvRef = $totalRcvJRPI + $totalRcvRPI;
								$total = $civil_total + $military_total + $reInvRef;
								$totalInvestigation = $totalCarryOver + $total;

								$data[] = array('FIELD' => $value1->NAME,
										'carryOver' => $totalCarryOver,
										'civil_total' => $civil_total,
										'military_total' => $military_total,
										'reInvRef' => $reInvRef,
										'total' => $total,
										'totalInvestigation' => $totalInvestigation,

									);
							};
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f5_field_office_pi_f1_p2':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						// var_dump($array);
						// echo json_encode($array);
						$data = array();
						
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						
						if($fieldOffice->status == 'SUCCESS'){

							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalGrant = 0;
								$totalDenial = 0;
								$totalManifest = 0;
								$totalTransfer = 0;
								$totalActed = 0;
								$totalRecall = 0;
								$totalWarrant = 0;
								$totalNotActed = 0;

								$totalActive = 0;

								$totalCarryOver = 0;
								$totalRcv = 0;
								$reInvRef = 0;
								$totalRcvJRPI = 0;
								$totalRcvRPI = 0;
								$total = 0;
								$totalInvestigation = 0;

								$JCIL_a = 0;
								$JCIL_b = 0;
								$JCIL_total = 0;
								$adult_a = 0;
								$adult_b = 0;
								$adult_total = 0;
								$civil_total = 0;

								$payload->filter = ""; $payload->table = "F5T1"; $payload->field_office = $value1->NAME; $payload->filter_val="";$payload->filter_field="";
								$carryOver = json_decode($this->Cmis_F5PCS_model->carryOver($payload));
								$totalCarryOver += $carryOver->count;

								// civil JICL
								$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$rcvInvestigationJICLCivilNewJPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$JCIL_a += $rcvInvestigationJICLCivilNewJPI->count;
								
								$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$rcvInvestigationJICLCivilNewJTPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$JCIL_b += $rcvInvestigationJICLCivilNewJTPI->count;
								
								$JCIL_total = $JCIL_a + $JCIL_b;

								// civil Adult
								$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$rcvInvestigationAdultCivilNewJPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$adult_a += $rcvInvestigationAdultCivilNewJPI->count;
								
								$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1";
								$rcvInvestigationAdultCivilNewJTPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$adult_b += $rcvInvestigationAdultCivilNewJTPI->count;

								$adult_total = $adult_a + $adult_b;
								$civil_total = $JCIL_total + $adult_total;

								$military_JCIL_a = 0;
								$military_JCIL_b = 0;
								$military_JCIL_total = 0;
								$military_adult_a = 0;
								$military_adult_b = 0;
								$military_adult_total = 0;
								$military_total = 0;
								// military JICL
								$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$a = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$military_JCIL_a += $a->count;
								
								$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
								$b = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$military_JCIL_b += $b->count;
								
								$military_JCIL_total = $military_JCIL_a + $military_JCIL_b;

								// military Adult
								$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; 
								$c = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$military_adult_a += $c->count;
								
								$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0";
								$d = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$military_adult_b += $d->count;

								$military_adult_total = $military_adult_a + $military_adult_b;
								$military_total = $military_JCIL_total + $military_adult_total;

								$payload->filter = "JRPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
								$rcvJRPI = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalRcvJRPI += $rcvJRPI->count;

								$payload->filter = "RPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
								$rcvRPI= json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalRcvRPI += $rcvRPI->count;

								$reInvRef = $totalRcvJRPI + $totalRcvRPI;
								$total = $civil_total + $military_total + $reInvRef;
								$totalInvestigation = $totalCarryOver + $total;

								#echo $this->Cmis_F5T2_model->fetchF5T2_RCV_ByYM($payload);
								$payload->filter = ""; $payload->table = "F5T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "FOR GRANT"; $payload->field_office = $value1->NAME;
								$grant_upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalGrant += $grant_upon->count;

								$payload->filter = ""; $payload->table = "F5T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Denial"; $payload->field_office = $value1->NAME;
								$denial_upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalDenial += $denial_upon->count;

								$payload->filter = ""; $payload->table = "F5T2_ACTED"; $payload->filter_field = "manifest_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
								$manifest_upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalManifest += $manifest_upon->count;

								$payload->filter = ""; $payload->table = "F5T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
								$transfer_upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalTransfer += $transfer_upon->count;

								$payload->filter = ""; $payload->table = "F5T2_NOTACTED"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Recall"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalRecall += $upon->count;

								$payload->filter = ""; $payload->table = "F5T2_NOTACTED"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Warrant"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalWarrant += $upon->count;


								$totalActed = $totalGrant + $totalDenial + $totalManifest + $totalTransfer;
								$totalNotActed = $totalRecall + $totalWarrant;
								$totalActive = $totalInvestigation - ($totalActed + $totalNotActed);
								
								/// based on formula (D+E)
								$totalActiveCase = $totalActed + $totalNotActed;
								#($act_upon);
								$data[] = array('FIELD' => $value1->NAME,
										'totalGrant' => $totalGrant,
										'totalDenial' => $totalDenial,
										'totalManifest' => $totalManifest,
										'totalTransfer' => $totalTransfer,
										'totalActed' => $totalActed,
										'totalRecall' => $totalRecall,
										'totalWarrant' => $totalWarrant,
										'totalNotActed' => $totalNotActed,
										'totalActive' => $totalActive,
										'totalActiveCase' => $totalActiveCase,
										'totalInvestigation' => $totalInvestigation);
							};
						}

						#var_dump();
						#break;
						
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f5_field_office_pi_f2':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
							
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							if($fieldOffice->status == 'SUCCESS'){

								foreach ($fieldOffice->payload as $key1 => $value1){
									$totalCarryOver = 0;
									$totalPSIR = 0;
									$totalManifest = 0;
									$totalSubmitted = 0;
									$totalbeActed = 0;

									$totalGrant = 0;
									$totalDenial = 0;
									$totalDismiss = 0;
									$totalWithdraw = 0;
									$totalReinv = 0;
									$totalOther = 0;
									$totalWarrant = 0;
									$totalRecall = 0;
									$totalDisposed = 0;
									$totalNotActed = 0;
									$totalPending = 0;


									$payload->filter = ""; $payload->table = "F5T1"; $payload->field_office = $value1->NAME; $payload->filter_val="";$payload->filter_field="";
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Grant"; $payload->field_office = $value1->NAME;
									$grant_upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalGrant += $grant_upon->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Denial"; $payload->field_office = $value1->NAME;
									$denial_upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalDenial += $denial_upon->count;
									$totalPSIR += $totalGrant + $totalDenial;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "manifest_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalManifest += $upon->count;

									$totalSubmitted = $totalPSIR + $totalManifest;
									$totalbeActed = $totalSubmitted + $totalCarryOver;

									// $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Grant"; $payload->field_office = $value1->NAME;
									// $uponGrant = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									// $totalGrant += $uponGrant->count;

									$casedDispositionGrantJICLJPI = 0;
									$casedDispositionGrantJICLJRPI = 0;
									$casedDispositionGrantJICLJTPI = 0;
									$casedDispositionGrantJICL = 0;

									$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionGrantJICLJPI += $upon->count;

									$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionGrantJICLJRPI += $upon->count;

									$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionGrantJICLJTPI += $upon->count;

									$casedDispositionGrantJICL = $casedDispositionGrantJICLJPI + $casedDispositionGrantJICLJRPI + $casedDispositionGrantJICLJTPI;

									$casedDispositionGrantAdultPI = 0;
									$casedDispositionGrantAdultRPI = 0;
									$casedDispositionGrantAdultTPI = 0;
									$casedDispositionGrantAdult = 0;
									
									$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionGrantAdultPI += $upon->count;

									$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionGrantAdultRPI += $upon->count;

									$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionGrantAdultTPI += $upon->count;

									$casedDispositionGrantAdult = $casedDispositionGrantAdultPI + $casedDispositionGrantAdultRPI + $casedDispositionGrantAdultTPI;

									$totalGrant = $casedDispositionGrantJICL + $casedDispositionGrantAdult;


									//start den
									$casedDispositionDeniedJICLJPI = 0;
									$casedDispositionDeniedJICLJRPI = 0;
									$casedDispositionDeniedJICJTPI = 0;
									$casedDispositionDeniedJICL = 0;

									$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDeniedJICLJPI += $upon->count;

									$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDeniedJICLJRPI += $upon->count;

									$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDeniedJICJTPI += $upon->count;

									$casedDispositionDeniedJICL = $casedDispositionDeniedJICLJPI + $casedDispositionDeniedJICLJRPI + $casedDispositionDeniedJICJTPI;

									$casedDispositionDeniedAdultPI = 0;
									$casedDispositionDeniedAdultRPI = 0;
									$casedDispositionDeniedAdultTPI = 0;
									$casedDispositionDeniedAdult = 0;
									
									$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDeniedAdultPI += $upon->count;

									$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDeniedAdultRPI += $upon->count;

									$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDeniedAdultTPI += $upon->count;

									$casedDispositionDeniedAdult = $casedDispositionDeniedAdultPI + $casedDispositionDeniedAdultRPI + $casedDispositionDeniedAdultTPI;

									$totalDenial = $casedDispositionDeniedJICL + $casedDispositionDeniedAdult;
									// $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Den"; $payload->field_office = $value1->NAME;
									// $upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									// $totalDenial += $upon->count;
									//end den

									// start Dismissed
									$casedDispositionDiedJICLJPI = 0;
									$casedDispositionDiedJICLJRPI = 0;
									$casedDispositionDiedJICLJTPI = 0;
									$casedDispositionDiedJICL = 0;

									$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDiedJICLJPI += $upon->count;

									$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDiedJICLJRPI += $upon->count;

									$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDiedJICLJTPI += $upon->count;

									$casedDispositionDiedJICL = $casedDispositionDiedJICLJPI + $casedDispositionDiedJICLJRPI + $casedDispositionDiedJICLJTPI;

									$casedDispositionDiedAdultPI = 0;
									$casedDispositionDiedAdultRPI = 0;
									$casedDispositionDiedAdultTPI = 0;
									$casedDispositionDiedAdult = 0;
									
									$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDiedAdultPI += $upon->count;

									$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDiedAdultRPI += $upon->count;

									$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionDiedAdultTPI += $upon->count;

									$casedDispositionDiedAdult = $casedDispositionDiedAdultPI + $casedDispositionDiedAdultRPI + $casedDispositionDiedAdultTPI;

									$totalDismiss = $casedDispositionDiedJICL + $casedDispositionDiedAdult;
									// $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismi"; $payload->field_office = $value1->NAME;
									// $upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									// $totalDismiss += $upon->count;
									// end Dismissed

									//start With
									$casedDispositionWithJICLJPI = 0;
									$casedDispositionWithJICLJRPI = 0;
									$casedDispositionWithJICLJTPI = 0;
									$casedDispositionWithJICL = 0;

									$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionWithJICLJPI += $upon->count;

									$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionWithJICLJRPI += $upon->count;

									$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionWithJICLJTPI += $upon->count;

									$casedDispositionWithJICL = $casedDispositionWithJICLJPI + $casedDispositionWithJICLJRPI + $casedDispositionWithJICLJTPI;

									$casedDispositionWithAdultPI = 0;
									$casedDispositionWithAdultRPI = 0;
									$casedDispositionWithAdultTPI = 0;
									$casedDispositionWithAdult = 0;
									
									$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionWithAdultPI += $upon->count;

									$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionWithAdultRPI += $upon->count;

									$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$casedDispositionWithAdultTPI += $upon->count;

									$casedDispositionWithAdult = $casedDispositionWithAdultPI + $casedDispositionWithAdultRPI + $casedDispositionWithAdultTPI;

									// $totalWithdraw = $casedDispositionWithJICL + $casedDispositionWithAdult;
									$payload->filter = ""; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalWithdraw += $upon->count;
									//end With

									$payload->filter = ""; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Reinv"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalReinv += $upon->count;

									$payload->filter = ""; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Other"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalOther += $upon->count;

									$payload->filter = ""; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Warrant"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalWarrant += $upon->count;

									$payload->filter = ""; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Recall"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRecall += $upon->count;

									$totalDisposed = $totalGrant + $totalDenial + $totalDismiss + $totalWithdraw + $totalReinv + $totalOther;
									$totalNotActed = $totalWarrant + $totalRecall;

									$totalPending = $totalbeActed - $totalDisposed;

									$data[] = array('FIELD' => $value1->NAME,
											'totalCarryOver' => $totalCarryOver,
											'totalPSIR' => $totalPSIR,
											'totalManifest' => $totalManifest,
											'totalSubmitted' => $totalSubmitted,
											'totalbeActed' => $totalbeActed,
											'totalGrant' => $totalGrant,
											'totalDenial' => $totalDenial,
											'totalDismiss' => $totalDismiss,
											'totalWithdraw' => $totalWithdraw,
											'totalReinv' => $totalReinv,
											'totalOther' => $totalOther,
											'totalDisposed' => $totalDisposed,
											'totalWarrant' => $totalWarrant,
											'totalRecall' => $totalRecall,
											'totalNotActed' => $totalNotActed,
											'totalPending' => $totalPending,

										);
								};
							}

							#var_dump();
							#break;
						
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f5_field_office_pi_f3':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
							
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						if($fieldOffice->status == 'SUCCESS'){
							foreach ($fieldOffice->payload as $key1 => $value1){

							$totalCarryOver = 0;
							$totalRcv = 0;
							$totalInvestigation = 0;
							$totalRcvPartial = 0;
							$totalCmpltdPartial = 0;
							$totalPartial = 0;

							$totalCmpltd = 0;
							$totalActive = 0;

							$payload->filter = ""; $payload->table = "F5T5"; $payload->field_office = $value1->NAME;
							$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
							$totalCarryOver += $carryOver->count;

							$payload->filter = ""; $payload->table = "F5T6_RCV"; $payload->field_office = $value1->NAME;
							$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
							$totalRcv += $rcv->count;

							$totalInvestigation = $totalCarryOver + $totalRcv;

							$totalJPCIRcv = 0;
							$payload->filter = "JCPI"; $payload->table = "F5T6_RCV"; $payload->field_office = $value1->NAME;
							$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
							$totalJPCIRcv += $rcv->count;

							$totalCPIRcv = 0;
							$payload->filter = "CPI"; $payload->table = "F5T6_RCV"; $payload->field_office = $value1->NAME;
							$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
							$totalCPIRcv += $rcv->count;

							$totalJPCICmpltd = 0;
							$payload->filter = "JCPI"; $payload->table = "F5T6_CMPLTD"; $payload->field_office = $value1->NAME;
							$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
							$totalJPCICmpltd += $rcv->count;

							$totalCPICmpltd = 0;
							$payload->filter = "CPI"; $payload->table = "F5T6_CMPLTD"; $payload->field_office = $value1->NAME;
							$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
							$totalCPICmpltd += $rcv->count;
							
							$totalRcvPartial = $totalJPCIRcv + $totalCPIRcv;
							$totalCmpltdPartial = $totalJPCICmpltd + $totalCPICmpltd;
							$totalPartial = $totalRcvPartial - $totalCmpltdPartial;

							$totalFBCIRcv = 0;
							$payload->filter = "FBCI"; $payload->table = "F5T6_RCV"; $payload->field_office = $value1->NAME;
							$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
							$totalFBCIRcv += $rcv->count;

							$totalFBCICmpltd = 0;
							$payload->filter = "FBCI"; $payload->table = "F5T6_CMPLTD"; $payload->field_office = $value1->NAME;
							$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
							$totalFBCICmpltd += $rcv->count;

							$totalFullBlown = 0;
							$totalD = 0;

							$totalFullBlown = $totalFBCIRcv - $totalFBCICmpltd;
							$totalD = $totalPartial + $totalFullBlown;

							$totalActive = $totalInvestigation - $totalD;

							$data[] = array('FIELD' => $value1->NAME,
											'carryOver' => $totalCarryOver,
											'totalRcv' => $totalRcv,
											'totalInvestigation' => $totalInvestigation,
											'totalPartial' => $totalPartial,
											'totalFullBlown' => $totalFullBlown,
											'totalD' => $totalD,
											'totalActive' => $totalActive,

										);
							};
						}
						
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f5_field_office_pi_f4':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
							
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						if($fieldOffice->status == 'SUCCESS'){
							foreach ($fieldOffice->payload as $key1 => $value1){									
								$getTotalCarryAdultInvestigation = 0;
								$getTotalCarryAdultInvestigationRpi = 0;
								$getTotalCarryAdultInvestigationTpi = 0;
								$getTotalCarryJICLInvestigation = 0;
								$getTotalCarryJICLInvestigationJRPI = 0;
								$getTotalCarryJICLInvestigationJTPI = 0;

								$carryOverInvestigationAdult = 0;
								$carryOverInvestigationJICL = 0;
								$carryOverInvestigationTotal = 0;
								
								$rcvInvestigationJICLMilNewJPI = 0;
								$rcvInvestigationJICLMilNewJTPI = 0;
								$rcvInvestigationAdultMilNewJPI = 0;
								$rcvInvestigationAdultMilNewJTPI = 0;
								$rcvInvestigationJICLMilNew = 0;
								$rcvInvestigationAdultMilNe = 0;
								$rcvInvestigationTotalMilNew = 0;
								
								$rcvInvestigationJICLCivilNewJPI = 0;
								$rcvInvestigationJICLCivilNewJTPI = 0;
								$rcvInvestigationAdultCivilNewJPI = 0;
								$rcvInvestigationAdultCivilNewJTPI = 0;
								$rcvInvestigationJICLCivilNew = 0;
								$rcvInvestigationAdultCivilNew = 0;
								$rcvInvestigationTotalCivilNew = 0;

								$rcvInvestigationJICLNew = 0;
								$rcvInvestigationAdultNew = 0;
								$rcvInvestigationTotalNew = 0;
								$rcvInvestigationJICLRPI = 0;
								$rcvInvestigationAdultRPI = 0;
								$rcvInvestigationTotalRPI = 0;
								$rcvInvestigationJICL = 0;
								$rcvInvestigationAdult = 0;
								$rcvInvestigationTotal = 0;

								$totalHandled = 0;

								$getTotalActedAdultInvestigationTransfer = 0;
								$getTotalActedJICLInvestigationTransfer = 0;
								$actedInvestigationAdultTransfer = 0;
								$actedInvestigationJICLTransfer = 0;
								$actedInvestigationTotalTransfer = 0;

								$getTotalNotActedAdultInvestigationRecalled = 0;
								$getTotalNotActedJICLInvestigationRecalled = 0;
								$getTotalNotActedTotalInvestigationRecalled = 0;
								$notactedAdultInvestigationRecalled = 0;
								$notactedJICLInvestigationRecalled = 0;
								$notactedTotalInvestigationRecalled = 0;

								$notactedAdultInvestigationWarrant = 0;
								$notactedJICLInvestigationWarrant = 0;
								$notactedTotalInvestigationWarrant = 0;

								$rcvRef = 0;
								$rcvRefGranted = 0;
								$rcvRefDenial = 0;
								$totalRef = 0;
								$totalGrant = 0;
								$totalDenial = 0;

								$getTotalActedAdultInvestigationGrant = 0;
								$getTotalActedAdultInvestigationDenial = 0;
								$actedInvestigationAdultGrant = 0;
								$actedInvestigationAdultDenial = 0;
								$getTotalActedJICLInvestigationGrant = 0;
								$getTotalActedJICLInvestigationDenial = 0;
								$actedInvestigationJICLGrant = 0;
								$actedInvestigationJICLDenial = 0;
								$actedInvestigationTotalGrant = 0;
								$actedInvestigationTotalDenial = 0;

								$getTotalActedAdultInvestigationManifest = 0;
								$getTotalActedJICLInvestigationManifest = 0;
								$actedInvestigationAdultManifest = 0;
								$actedInvestigationJICLManifest = 0;
								$actedInvestigationTotalManifest = 0;

								$totalInvRefref = 0;

								$payload->field_office = $value1->NAME;
								$getTotalCarryAdultInvestigation    = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"PI"));

								$payload->field_office = $value1->NAME;
								$getTotalCarryAdultInvestigationRpi = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"RPI"));

								$payload->field_office = $value1->NAME;
								$getTotalCarryAdultInvestigationTpi = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"TPI"));

								$payload->field_office = $value1->NAME;
								$getTotalCarryJICLInvestigation     = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"JPI"));

								$payload->field_office = $value1->NAME;
								$getTotalCarryJICLInvestigationJRPI = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"JRPI"));

								$payload->field_office = $value1->NAME;
								$getTotalCarryJICLInvestigationJTPI = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"JTPI"));

								$carryOverInvestigationAdult = $getTotalCarryAdultInvestigation + $getTotalCarryAdultInvestigationRpi + $getTotalCarryAdultInvestigationTpi;

								$carryOverInvestigationJICL = $getTotalCarryJICLInvestigation + $getTotalCarryJICLInvestigationJRPI + $getTotalCarryJICLInvestigationJTPI;

								$carryOverInvestigationTotal = $carryOverInvestigationAdult + $carryOverInvestigationJICL;
								/////////

								$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1";$payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationJICLCivilNewJPI += $upon->count;
								
								$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1";$payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationJICLCivilNewJTPI += $upon->count;

								$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationAdultCivilNewJPI += $upon->count;
								
								$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationAdultCivilNewJTPI += $upon->count;

								$rcvInvestigationJICLCivilNew = $rcvInvestigationJICLCivilNewJPI + $rcvInvestigationJICLCivilNewJTPI;
								$rcvInvestigationAdultCivilNew = $rcvInvestigationAdultCivilNewJPI + $rcvInvestigationAdultCivilNewJTPI;
								$rcvInvestigationTotalCivilNew = $rcvInvestigationJICLCivilNew + $rcvInvestigationAdultCivilNew;
								/////////////

								$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationJICLMilNewJPI += $upon->count;
								
								$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationJICLMilNewJTPI += $upon->count;

								$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationAdultMilNewJPI += $upon->count;
								
								$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationAdultMilNewJTPI += $upon->count;

								$rcvInvestigationJICLMilNew  = $rcvInvestigationJICLMilNewJPI + $rcvInvestigationJICLMilNewJTPI;
								$rcvInvestigationAdultMilNew  = $rcvInvestigationAdultMilNewJPI + $rcvInvestigationAdultMilNewJTPI;
								$rcvInvestigationTotalMilNew = $rcvInvestigationJICLMilNew + $rcvInvestigationAdultMilNew;

								///////////
								$rcvInvestigationJICLNew = $rcvInvestigationJICLCivilNew + $rcvInvestigationJICLMilNew;
								$rcvInvestigationAdultNew = $rcvInvestigationAdultCivilNew + $rcvInvestigationAdultMilNew;
								$rcvInvestigationTotalNew = $rcvInvestigationJICLNew + $rcvInvestigationAdultNew;


								$payload->filter = "JRPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationJICLRPI += $upon->count;

								$payload->filter = "RPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$rcvInvestigationAdultRPI += $upon->count;

								$rcvInvestigationTotalRPI = $rcvInvestigationAdultRPI + $rcvInvestigationJICLRPI;

								$rcvInvestigationJICL = $rcvInvestigationJICLNew + $rcvInvestigationJICLRPI;
								$rcvInvestigationAdult = $rcvInvestigationAdultNew + $rcvInvestigationAdultRPI;
								$rcvInvestigationTotal = $rcvInvestigationJICL + $rcvInvestigationAdult;
								$totalHandled = $carryOverInvestigationTotal + $rcvInvestigationTotal;
								///////////////////

								$payload->field_office = $value1->NAME;
								$getTotalActedAdultInvestigationTransfer = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","",0,1));

								$payload->field_office = $value1->NAME;
								$getTotalActedJICLInvestigationTransfer =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","",0,1));
								$actedInvestigationAdultTransfer = $getTotalActedAdultInvestigationTransfer;
								$actedInvestigationJICLTransfer  = $getTotalActedJICLInvestigationTransfer;
								$actedInvestigationTotalTransfer = $getTotalActedAdultInvestigationTransfer + $getTotalActedJICLInvestigationTransfer;

								////////////////////////
								$payload->field_office = $value1->NAME;
								$getTotalNotActedAdultInvestigationRecalled =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","Recall"));
								$payload->field_office = $value1->NAME;
								$getTotalNotActedJICLInvestigationRecalled = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","Recall"));
								$getTotalNotActedTotalInvestigationRecalled = $getTotalNotActedAdultInvestigationRecalled + $getTotalNotActedJICLInvestigationRecalled;

								$notactedAdultInvestigationRecalled = $getTotalNotActedAdultInvestigationRecalled;
								$notactedJICLInvestigationRecalled = $getTotalNotActedJICLInvestigationRecalled;
								$notactedTotalInvestigationRecalled = $getTotalNotActedTotalInvestigationRecalled;


								$payload->field_office = $value1->NAME;
								$getTotalNotActedAdultInvestigationWarrant =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","Warrant"))  + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","Warrant")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","Warrant"));

								$payload->field_office = $value1->NAME;
								$getTotalNotActedJICLInvestigationWarrant = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","Warrant "))  + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","Warrant ")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","Warrant"));
								$getTotalNotActedTotalInvestigationWarrant = $getTotalNotActedAdultInvestigationWarrant + $getTotalNotActedJICLInvestigationWarrant;

								$notactedAdultInvestigationWarrant = $getTotalNotActedAdultInvestigationWarrant;
								$notactedJICLInvestigationWarrant  = $getTotalNotActedJICLInvestigationWarrant;
								$notactedTotalInvestigationWarrant = $getTotalNotActedTotalInvestigationWarrant;
								$totalRef = $notactedTotalInvestigationRecalled + $notactedTotalInvestigationWarrant;

								/////////////
								
								$getTotalActedAdultInvestigationGrant = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","FOR GRANT"));
								$getTotalActedAdultInvestigationDenial = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","FOR DENIAL"));

								$actedInvestigationAdultGrant = $getTotalActedAdultInvestigationGrant;
								$actedInvestigationAdultDenial = $getTotalActedAdultInvestigationDenial;

								$getTotalActedJICLInvestigationGrant = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","FOR GRANT"));
								$getTotalActedJICLInvestigationDenial = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","FOR DENIAL"));

								$actedInvestigationJICLGrant = $getTotalActedJICLInvestigationGrant;
								$actedInvestigationJICLDenial = $getTotalActedJICLInvestigationDenial;

								$actedInvestigationTotalGrant = $getTotalActedAdultInvestigationGrant  + $getTotalActedJICLInvestigationGrant;
								$actedInvestigationTotalDenial = $getTotalActedAdultInvestigationDenial  + $getTotalActedJICLInvestigationDenial;
								

								$getTotalActedAdultInvestigationManifest = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","",1));
								$getTotalActedJICLInvestigationManifest =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","",1));

								$actedInvestigationAdultManifest = $getTotalActedAdultInvestigationManifest;
								$actedInvestigationJICLManifest = $getTotalActedJICLInvestigationManifest;
								$actedInvestigationTotalManifest = $getTotalActedAdultInvestigationManifest + $getTotalActedJICLInvestigationManifest;
								$totalInvRefref = $actedInvestigationTotalGrant +
											$actedInvestigationTotalDenial +
											$actedInvestigationTotalTransfer +
											$actedInvestigationTotalManifest 
											;

								$data[] = array('FIELD' => $value1->NAME,
												'carryOverInvestigationTotal' => $carryOverInvestigationTotal,
												'rcvInvestigationTotal' => $rcvInvestigationTotal,
												'totalHandled' => $totalHandled,
												'actedInvestigationTotalGrant' => $actedInvestigationTotalGrant,
												'actedInvestigationTotalDenial' => $actedInvestigationTotalDenial,
												'actedInvestigationTotalManifest' => $actedInvestigationTotalManifest,
												'actedInvestigationTotalTransfer' => $actedInvestigationTotalTransfer,
												'totalInvRefref' => $totalInvRefref,
												'notactedTotalInvestigationRecalled' => $notactedTotalInvestigationRecalled,
												'notactedTotalInvestigationWarrant' => $notactedTotalInvestigationWarrant,
												'totalRef' => $totalRef,

											);
							};
						}
						
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f5_field_office_ps_f1_p1':
						//var_dump($payload);
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

						
						if($fieldOffice->status == 'SUCCESS'){

							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOver = 0;
								$totalRcv = 0;
								$totalInvestigation = 0;
								$totalRcv2 = 0;

								$payload->filter = ""; $payload->table = "F5T7"; $payload->field_office = $value1->NAME;
								$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalCarryOver += $carryOver->count;


								$payload->filter = ""; $payload->table = "F5T8"; $payload->field_office = $value1->NAME;
								$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalRcv += $rcv->count;

								$totalRcv2 = $totalRcv;
								$totalInvestigation = $totalCarryOver + $totalRcv;
							
								$data[] = array('FIELD' => $value1->NAME,
										'carryOver' => $totalCarryOver,
										'totalRcv' => $totalRcv,
										'totalRcv2' => $totalRcv2,
										'totalInvestigation' => $totalInvestigation,

									);
							};
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
			case 'f5_field_office_ps_f1_p2':
						//var_dump($payload);
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

						
						if($fieldOffice->status == 'SUCCESS'){

							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOver = 0;
								$totalFullTerm = 0;
								$totalEarlyTerm = 0;
								$totalDiedTerm = 0;

								$totalAbs = 0;
								$totalComm = 0;
								$totalVio = 0;
								$totalOther = 0;
								$totalRevoc = 0;


								$totalInvestigation = 0;
								$totalRcv = 0;
								$totalRcv2 = 0;
								$totalTerm = 0;

								$totalTransfer = 0;
								$totalOthers = 0;
								$totalDropped = 0;
								$totalActive = 0;

								$payload->filter_val = ""; $payload->filter_field = ""; $payload->filter = ""; $payload->table = "F5T7"; $payload->field_office = $value1->NAME;
								$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalCarryOver += $carryOver->count;


								$payload->filter_val = ""; $payload->filter_field = ""; $payload->filter = ""; $payload->table = "F5T8"; $payload->field_office = $value1->NAME;
								$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalRcv += $rcv->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Full Term"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalFullTerm += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Early"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalEarlyTerm += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Died"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalDiedTerm += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abs"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalAbs += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Comm"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalComm += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Vio"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalVio += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalOther += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTransfer += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalOthers += $upon->count;

								$totalTerm = $totalFullTerm + $totalEarlyTerm + $totalDiedTerm;

								$totalRcv2 = $totalRcv;
								$totalInvestigation = $totalCarryOver + $totalRcv;
								$totalRevoc = $totalAbs + $totalComm + $totalVio + $totalOther;

								$totalDropped = $totalTerm + $totalRevoc + $totalTransfer + $totalOthers;
								$totalActive = $totalInvestigation - $totalDropped;

								$data[] = array('FIELD' => $value1->NAME,
										'totalInvestigation' => $totalInvestigation,
										'totalFullTerm' => $totalFullTerm,
										'totalEarlyTerm' => $totalEarlyTerm,
										'totalDiedTerm' => $totalDiedTerm,
										'totalTerm' => $totalTerm,
										'totalAbs' => $totalAbs,
										'totalComm' => $totalComm,
										'totalVio' => $totalVio,
										'totalOther' => $totalOther,
										'totalRevoc' => $totalRevoc,
										'totalTransfer' => $totalTransfer,
										'totalOthers' => $totalOthers,
										'totalDropped' => $totalDropped,
										'totalActive' => $totalActive,

									);
							};
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

			case 'f5_field_office_ps_f1_p3':
						//var_dump($payload);
						
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							
						if($fieldOffice->status == 'SUCCESS'){
								
								
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalFullTerm = 0;
								$totalEarlyTerm = 0;
								$totalDiedTerm = 0;

								$totalAbs = 0;
								$totalComm = 0;
								$totalVio = 0;
								$totalOther = 0;
								$totalRevoc = 0;


								
								$totalTerm = 0;

								$totalTransfer = 0;
								$totalOthers = 0;
								$totalDropped = 0;
								$totalExt = 0;
								
						
								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Full Term"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalFullTerm += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Early"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalEarlyTerm += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Died"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalDiedTerm += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abs"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalAbs += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Comm"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalComm += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Vio"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalVio += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalOther += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTransfer += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalExt += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalOthers += $upon->count;

								$totalTerm = $totalFullTerm + $totalEarlyTerm + $totalDiedTerm;

								
								$totalRevoc = $totalAbs + $totalComm + $totalVio + $totalOther;

								$totalDropped = $totalTerm + $totalRevoc + $totalTransfer + $totalOthers;
								
								$data[] = array('FIELD' => $value1->NAME,
										'totalFullTerm' => $totalFullTerm,
										'totalEarlyTerm' => $totalEarlyTerm,
										'totalDiedTerm' => $totalDiedTerm,
										'totalTerm' => $totalTerm,
										'totalAbs' => $totalAbs,
										'totalComm' => $totalComm,
										'totalVio' => $totalVio,
										'totalOther' => $totalOther,
										'totalRevoc' => $totalRevoc,
										'totalExt' => $totalExt,
										'totalTransfer' => $totalTransfer,
										'totalOthers' => $totalOthers,
										'totalDropped' => $totalDropped,
									);

							}
						}
						
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;


				case 'f5_field_office_ps_f2_p1':
						//var_dump($payload);
						
						
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

						
						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								// (A)
								$totalCarryTerm = 0;
								$totalCarryRevoc = 0;
								$totalCarryExt = 0;
								$totalCarryOth = 0;
								$totalCarryTrans = 0;
								$totalCarryOver = 0;
								// (B)
								$totalTerm = 0;
								$totalRevoc = 0;
								$totalExt = 0;
								$totalOther = 0;
								$totalTrans = 0;
								$totalSubmitted = 0;
								// (C)
								$totalCasesBeActed = 0;


								// (A) CARRY OVER
								$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Termina"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryTerm += $upon->count;

								$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Revoc"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryRevoc += $upon->count;

								$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Extension"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryExt += $upon->count;

								$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryTrans += $upon->count;

								$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOth += $upon->count;

								$totalCarryOver = 	$totalCarryTerm + $totalCarryRevoc + $totalCarryExt + $totalCarryExt + $totalCarryOth;
								
								// (B) SUBMITTED TO COURT
								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTerm += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRevoc += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalExt += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalOther += $upon->count;

								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTrans += $upon->count;

								$totalSubmitted = $totalTerm + $totalRevoc + $totalExt + $totalOther + $totalTrans;


								$totalCasesBeActed = $totalCarryOver + $totalSubmitted;
								

								$data[] = array('FIELD' => $value1->NAME,
													'totalCarryTerm' => $totalCarryTerm,
													'totalCarryRevoc' => $totalCarryRevoc, 
													'totalCarryExt' => $totalCarryExt,
													'totalCarryTrans' => $totalCarryTrans,
													'totalCarryOth' => $totalCarryOth,
													'totalCarryOver'=> $totalCarryOver,
													'totalTerm'=> $totalTerm,
													'totalRevoc'=> $totalRevoc,
													'totalExt'=> $totalExt,
													'totalOther'=> $totalOther,
													'totalTrans'=> $totalTrans,
													'totalSubmitted'=> $totalSubmitted,
													'totalCasesBeActed'=> $totalCasesBeActed,
										);
							}
						}
						
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f5_field_office_ps_f2_p2':

						
						
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						

						if($fieldOffice->status == 'SUCCESS'){
							
							
							foreach ($fieldOffice->payload as $key1 => $value1){

								// (A)
								$totalCarryOver = 0;
								// (B)
								
								$totalSubmitted = 0;
								// (C)
								$totalCasesBeActed = 0;

								$totalFullTerm = 0;
								$totalEarlyTerm = 0;
								$totalDiedTerm = 0;
								$totalTerm = 0;

								$totalAbs = 0;
								$totalComm = 0;
								$totalVio = 0;
								$totalOther = 0;
								$totalRevoc = 0;
								
								$totalExt = 0;
								$totalTransfer = 0;
								$totalOthers = 0;

								$totalDisposed = 0;
								$totalPending = 0;

								// (A) CARRY OVER
								$payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOver += $upon->count;

								
								// (B) SUBMITTED TO COURT
								$payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalSubmitted += $upon->count;

								//( C)
								$totalCasesBeActed = $totalCarryOver + $totalSubmitted;

								// (D) DISPOSED

								// (D - TERMINATION)
								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Full Term"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalFullTerm += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Early"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalEarlyTerm += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Died"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalDiedTerm += $upon->count;

								$totalTerm = $totalFullTerm + $totalEarlyTerm + $totalDiedTerm;

								// (D - REVOC)
								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abs"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalAbs += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Comm"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalComm += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Vio"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalVio += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalOther += $upon->count;

								$totalRevoc = $totalAbs + $totalComm + $totalVio + $totalOther;

								// (D - OTHER)
								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Exten"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalExt += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTransfer += $upon->count;

								$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalOthers += $upon->count;


								$totalDisposed = $totalTerm + $totalRevoc + $totalExt + $totalTransfer + $totalOthers;
								
								// (E)
								$totalPending = $totalCasesBeActed - $totalDisposed;

								$data[] = array('FIELD' => $value1->NAME,
												'totalCasesBeActed'=> $totalCasesBeActed,
												'totalFullTerm'=> $totalFullTerm,
												'totalEarlyTerm'=> $totalEarlyTerm,
												'totalDiedTerm'=> $totalDiedTerm,
												'totalTerm'=> $totalTerm,
												'totalAbs'=> $totalAbs,
												'totalComm'=> $totalComm,
												'totalVio'=> $totalVio,
												'totalOther'=> $totalOther,
												'totalRevoc'=> $totalRevoc,
												'totalExt'=> $totalExt,
												'totalTransfer'=> $totalTransfer,
												'totalOthers'=> $totalOthers,
												'totalDisposed'=> $totalDisposed,
												'totalPending'=> $totalPending,
									);
							}
						}
						
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f5_field_office_ps_f3':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						
						if($fieldOffice->status == 'SUCCESS'){
							
							
							foreach ($fieldOffice->payload as $key1 => $value1){

								// (A)
								$totalCarryOver = 0;
								// (B)
								$totalRcv = 0;
								// (C)
								$totalCasesHandled = 0;
								// (D)
								$totalCompltd = 0;
								// (E)
								$totalCourtesy = 0;


								// (A) CARRY OVER
								$payload->table = "F5T12"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOver += $upon->count;

								
								// (B) COURTESY REFERRALS RECEIVED	
								$payload->table = "F5T13_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRcv += $upon->count;

								//( C)
								$totalCasesHandled = $totalCarryOver + $totalRcv;

								// (D) DISPOSED
								$payload->table = "F5T13_TERM"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCompltd += $upon->count;
								
								// (E)
								$totalCourtesy = $totalCasesHandled - $totalCompltd;

								$data[] = array('FIELD' => $value1->NAME,
												'totalCarryOver'=> $totalCarryOver,
												'totalRcv'=> $totalRcv,
												'totalCasesHandled'=> $totalCasesHandled,
												'totalCompltd'=> $totalCompltd,
												'totalCourtesy'=> $totalCourtesy,
									);
							}
						}
						
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_field_office_ppi_f1_p1':

						
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						
						// (A)


						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){

								$totalCarryOver = 0;
								
								// (B)
								$totalRcv = 0;

								// (C)
								$totalCasesHandled = 0;
								
								// (A) CARRY OVER
								$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOver += $upon->count;

								
								// (B) COURTESY REFERRALS RECEIVED	
								$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRcv += $upon->count;

								//( C)
								$totalCasesHandled = $totalCarryOver + $totalRcv;

								$data[] = array('FIELD' => $value1->NAME,
												'totalCarryOver'=> $totalCarryOver,
												'totalRcv'=> $totalRcv,
												'totalCasesHandled'=> $totalCasesHandled
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_field_office_ppi_f1_p2':
						
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalGrant = 0;
								$totalDenial = 0;
								$commutationTotalGrant = 0;
								$commutationTotalDenial = 0;
								$absoluteTotalGrant = 0;
								$totalppir = 0;

								$totalCarryOver = 0;
								$totalRcv = 0;
								$totalInvestigation = 0;
								$totalRcv2 = 0;


								$payload->filter = ""; $payload->table = "F21T1"; $payload->field_office = $value1->NAME; $payload->filter_val="";$payload->filter_field="";
								$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalCarryOver += $carryOver->count;

								$payload->filter = ""; $payload->table = "F21T2_RCV"; $payload->field_office = $value1->NAME;$payload->filter_val="";$payload->filter_field="";
								$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
								$totalRcv += $rcv->count;

								$totalRcv2 = $totalRcv;
								$totalInvestigation = $totalCarryOver + $totalRcv;

								///////
								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; $payload->field_office = $value1->NAME;
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalGrant += $grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; $payload->field_office = $value1->NAME;
								$denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalDenial += $denial_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; $payload->field_office = $value1->NAME;
								$commutation_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$commutationTotalGrant += $commutation_grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; $payload->field_office = $value1->NAME;
								$commutation_denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$commutationTotalDenial += $commutation_denial_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; $payload->field_office = $value1->NAME;
								$absolute_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$absoluteTotalGrant += $absolute_grant_upon->count;


								$totalppir = $totalGrant + $totalDenial + $commutationTotalGrant + $commutationTotalDenial + $absoluteTotalGrant;

								$data[] = array('FIELD' => $value1->NAME,
												'totalGrant'=> $totalGrant,
												'totalDenial'=> $totalDenial,
												'commutationTotalGrant' => $commutationTotalGrant,
												'commutationTotalDenial'=> $commutationTotalDenial,
												'absoluteTotalGrant'=> $absoluteTotalGrant,
												'totalppir'=> $totalppir,
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				

				case 'f21_field_office_ppi_f1_p3':

					$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
					// (A)

					if($fieldOffice->status == 'SUCCESS'){
						
						
						foreach ($fieldOffice->payload as $key1 => $value1){

							$totalCarryOver = 0;
							
							// (B)
							$totalRcv = 0;

							// (C)
							$totalCasesHandled = 0;


							// (D1)
							$totalInvestigationReport = 0;
							// (D2)
							$totalTransfer = 0;
							
							$totalRefOthers = 0;

							$totalActed = 0;

							$totalActive = 0;

							// (A) CARRY OVER
							$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = "";
							$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
							$totalCarryOver += $upon->count;

							
							// (B) COURTESY REFERRALS RECEIVED	
							$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
							$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
							$totalRcv += $upon->count;

							//( C)
							$totalCasesHandled = $totalCarryOver + $totalRcv;


							// (D1)
							$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "-"; $payload->field_office = $value1->NAME;
							$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
							$totalInvestigationReport += $upon->count;

							// (D2)
							$payload->table = "F21T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
							$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
							$totalTransfer += $upon->count;

							$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Other"; $payload->additional_operator = "="; 
							$upon = json_decode($this->Cmis_F21PCS_model->callProcedure2($payload));
							$totalRefOthers += $upon->count;

							$totalActed = $totalInvestigationReport  + $totalTransfer + $totalRefOthers;

							$totalActive = $totalInvestigationReport - $totalActed;

							$data[] = array('FIELD' => $value1->NAME,
											'totalInvestigationReport'=> $totalInvestigationReport,
											'totalTransfer'=> $totalTransfer,
											'totalRefOthers'=> $totalRefOthers,
											'totalActed'=> $totalActed,
											'totalActive' => $totalActive,
								);
						}
					}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;


				case 'f21_field_office_ppi_f2_p1':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								//(A)
								$totalCarryOverPendingResolution = 0;
								// (B)
								$totalGrant = 0;
								$totalDenial = 0;
								$totalParole = 0;
								$commutationTotalGrant = 0;
								$commutationTotalDenial = 0;
								$totalCommutation = 0;
								$absoluteTotalGrant = 0;
								$totalRefOthers = 0;

								$totalReportSubmitted = 0;
								$payload->table = "F21T3"; $payload->filter_field = ""; $payload->filter_val = "Pardon - For Denial"; $payload->field_office = $value1->NAME;
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverPendingResolution += $grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; $payload->field_office = $value1->NAME;
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalGrant += $grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; $payload->field_office = $value1->NAME;
								$denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalDenial += $denial_upon->count;
								
								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; $payload->field_office = $value1->NAME;
								$commutation_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$commutationTotalGrant += $commutation_grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; $payload->field_office = $value1->NAME;
								$commutation_denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$commutationTotalDenial += $commutation_denial_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; $payload->field_office = $value1->NAME;
								$absolute_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$absoluteTotalGrant += $absolute_grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Other"; $payload->additional_operator = "="; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure2($payload));
								$totalRefOthers += $upon->count;

								$totalParole = $totalGrant + $totalDenial;
								$totalCommutation = $commutationTotalGrant + $commutationTotalDenial;
								$totalReportSubmitted = $totalParole + $totalCommutation + $absoluteTotalGrant + $totalRefOthers;
								$totalCasestobeActedUpon = $totalCarryOverPendingResolution + $totalReportSubmitted;
								$data[] = array('FIELD' => $value1->NAME,
												'totalCarryOverPendingResolution'=> $totalCarryOverPendingResolution,
												'totalParole'=> $totalParole,
												'totalCommutation'=> $totalCommutation,
												'absoluteTotalGrant'=> $absoluteTotalGrant,
												'totalRefOthers'=> $totalRefOthers,
												'totalReportSubmitted'=> $totalReportSubmitted,
												'totalCasestobeActedUpon'=> $totalCasestobeActedUpon,
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_field_office_ppi_f2_p2':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCasesResolvedGrantedParole = 0;
								$totalCasesResolvedGrantedComm = 0;
								$totalCasesResolvedGrantedCond = 0;
								$totalCasesResolvedGrantedAbs = 0;
								$totalCasesResolvedGranted = 0;

								$totalCasesResolvedDeniedParole = 0;
								$totalCasesResolvedDeniedComm = 0;
								$totalCasesResolvedDeniedCond = 0;
								$totalCasesResolvedDeniedAbs = 0;
								$totalCasesResolvedDenied = 0;

								$totalCasesResolvedCancelledParole = 0;
								$totalCasesResolvedCancelledComm = 0;
								$totalCasesResolvedCancelledCond = 0;
								$totalCasesResolvedCancelledAbs = 0;
								$totalCasesResolvedCancelled = 0;

								$totalCasesResolvedDied = 0;
								$totalCasesResolvedOther = 0;
								$totalCasesResolvedCount = 0;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "PAROLE - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedGrantedParole += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "COMMUTATION - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedGrantedComm += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "CONDITIONAL - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedGrantedCond += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ABSOLUTE - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedGrantedAbs += $grant_upon->count;

								$totalCasesResolvedGranted = $totalCasesResolvedGrantedParole + $totalCasesResolvedGrantedComm + $totalCasesResolvedGrantedCond + $totalCasesResolvedGrantedAbs;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "PAROLE - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedDeniedParole += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "COMMUTATION - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedDeniedComm += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "CONDITIONAL - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedDeniedCond += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ABSOLUTE - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedDeniedAbs += $grant_upon->count;

								$totalCasesResolvedDenied = $totalCasesResolvedDeniedParole + $totalCasesResolvedDeniedComm + $totalCasesResolvedDeniedCond + $totalCasesResolvedDeniedAbs;	

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "PAROLE - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedCancelledParole += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "COMMUTATION - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedCancelledComm += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "CONDITIONAL - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedCancelledCond += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ABSOLUTE - Granted"; $payload->field_office = $value1->NAME; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedCancelledAbs += $grant_upon->count;

								$totalCasesResolvedCancelled = $totalCasesResolvedCancelledParole + $totalCasesResolvedCancelledComm + $totalCasesResolvedCancelledCond;	

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Died"; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedDied += $grant_upon->count;

								$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Other"; 
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedOther += $grant_upon->count;

								$totalCasesResolvedCount = $totalCasesResolvedGranted + $totalCasesResolvedDenied + $totalCasesResolvedCancelled + $totalCasesResolvedDied+ $totalCasesResolvedOther;	

								$totalCarryOverPendingResolution = 0;
								$totalGrant = 0;
								$totalDenial = 0;
								$totalParole = 0;
								$commutationTotalGrant = 0;
								$commutationTotalDenial = 0;
								$totalCommutation = 0;
								$absoluteTotalGrant = 0;
								$totalRefOthers = 0;

								$totalReportSubmitted = 0;
								$payload->table = "F21T3"; $payload->filter_field = ""; $payload->filter_val = "Pardon - For Denial"; $payload->field_office = $value1->NAME;
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverPendingResolution += $grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; $payload->field_office = $value1->NAME;
								$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalGrant += $grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; $payload->field_office = $value1->NAME;
								$denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalDenial += $denial_upon->count;
								
								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; $payload->field_office = $value1->NAME;
								$commutation_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$commutationTotalGrant += $commutation_grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; $payload->field_office = $value1->NAME;
								$commutation_denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$commutationTotalDenial += $commutation_denial_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; $payload->field_office = $value1->NAME;
								$absolute_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$absoluteTotalGrant += $absolute_grant_upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Other"; $payload->additional_operator = "="; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure2($payload));
								$totalRefOthers += $upon->count;

								$totalParole = $totalGrant + $totalDenial;
								$totalCommutation = $commutationTotalGrant + $commutationTotalDenial;
								$totalReportSubmitted = $totalParole + $totalCommutation + $absoluteTotalGrant + $totalRefOthers;
								$totalCasestobeActedUpon = $totalCarryOverPendingResolution + $totalReportSubmitted;

								$totalCasesPending = $totalCasesResolvedCount + $totalReportSubmitted;
								$data[] = array('FIELD' => $value1->NAME,
												'totalCasesResolvedGrantedParole'=> $totalCasesResolvedGrantedParole,
												'totalCasesResolvedGrantedComm'=> $totalCasesResolvedGrantedComm,
												'totalCasesResolvedGrantedCond'=> $totalCasesResolvedGrantedCond,
												'totalCasesResolvedGrantedAbs'=> $totalCasesResolvedGrantedAbs,
												'totalCasesResolvedGranted'=> $totalCasesResolvedGranted,
												
												'totalCasesResolvedDeniedParole'=> $totalCasesResolvedDeniedParole,
												'totalCasesResolvedDeniedComm'=> $totalCasesResolvedDeniedComm,
												'totalCasesResolvedDeniedCond'=> $totalCasesResolvedDeniedCond,
												'totalCasesResolvedDeniedAbs'=> $totalCasesResolvedDeniedAbs,
												'totalCasesResolvedDenied'=> $totalCasesResolvedDenied,
												
												'totalCasesResolvedCancelledParole'=> $totalCasesResolvedCancelledParole,
												'totalCasesResolvedCancelledComm'=> $totalCasesResolvedCancelledComm,
												'totalCasesResolvedCancelledCond'=> $totalCasesResolvedCancelledCond,
												'totalCasesResolvedCancelled'=> $totalCasesResolvedCancelled,

												'totalCasesResolvedDied'=> $totalCasesResolvedDied,
												'totalCasesResolvedOther'=> $totalCasesResolvedOther,
												'totalCasesResolvedCount'=> $totalCasesResolvedCount,
												'totalCasesPending'=> $totalCasesPending,
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f21_field_office_ppi_f3':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								//(A)
								$totalCarryOverCI = 0;
								// (B)
								$totalRcvCI = 0;
								$totalCountCI = 0;
								$totalCmpltdCI = 0;
								$totalCountActiveCI = 0;

								$payload->table = "F21T5"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverCI += $upon->count;

								$payload->table = "F21T6_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRcvCI += $upon->count;

								$totalCountCI = $totalCarryOverCI + $totalRcvCI;		

								$payload->table = "F21T6_CMPLTD"; $payload->filter_field = ""; $payload->filter_val = ""; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCmpltdCI += $upon->count;

								$totalCountActiveCI = $totalCountCI - $totalCmpltdCI;		

								$data[] = array('FIELD' => $value1->NAME,
												'totalCarryOverCI'=> $totalCarryOverCI,
												'totalRcvCI'=> $totalRcvCI,
												'totalCountCI'=> $totalCountCI,
												'totalCmpltdCI'=> $totalCmpltdCI,
												'totalCountActiveCI'=> $totalCountActiveCI,
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_field_office_ppi_f4_p1':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOverInvestigation = 0;
								$totalRcvTotalInvestigation = 0;
								$totalCasesHandled = 0;
								$totalRefParoleGrantActed = 0;
								$totalRefParoleDeniedActed = 0;
								$totalRefCommGrantActed = 0;
								$totalRefCommDeniedActed = 0;
								$totalRefCondGrantActed = 0;
								$totalRefCondDeniedActed = 0;

								$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverInvestigation += $upon->count;

								$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRcvTotalInvestigation += $upon->count;
								
								$totalCasesHandled = $totalCarryOverInvestigation + $totalRcvTotalInvestigation;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefParoleGrantActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefParoleDeniedActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefCommGrantActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefCommDeniedActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Granted"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefCondGrantActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Denial"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefCondDeniedActed += $upon->count;

								$data[] = array('FIELD' => $value1->NAME,
												'totalCarryOverInvestigation'=> $totalCarryOverInvestigation,
												'totalRcvTotalInvestigation'=> $totalRcvTotalInvestigation,
												'totalCasesHandled'=> $totalCasesHandled,
												'totalRefParoleGrantActed'=> $totalRefParoleGrantActed,
												'totalRefParoleDeniedActed'=> $totalRefParoleDeniedActed,
												'totalRefCommGrantActed'=> $totalRefCommGrantActed,
												'totalRefCommDeniedActed'=> $totalRefCommDeniedActed,
												'totalRefCondGrantActed'=> $totalRefCondGrantActed,
												'totalRefCondDeniedActed'=> $totalRefCondDeniedActed,
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_field_office_ppi_f4_p2':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOverInvestigation = 0;
								$totalRcvTotalInvestigation = 0;
								$totalCasesHandled = 0;
								$totalRefParoleGrantActed = 0;
								$totalRefParoleDeniedActed = 0;
								$totalRefCommGrantActed = 0;
								$totalRefCommDeniedActed = 0;
								$totalRefCondGrantActed = 0;
								$totalRefCondDeniedActed = 0;
								$totalRefAbsGrantActed = 0;
								$totalRefAbsDeniedActed = 0;
								$totalPPIR = 0;
								$totalTransferredSubmitted = 0;
								$totalRefOthers = 0;
								$totalInvRef = 0;
								$disRate = 0;

								$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverInvestigation += $upon->count;

								$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRcvTotalInvestigation += $upon->count;
								
								$totalCasesHandled = $totalCarryOverInvestigation + $totalRcvTotalInvestigation;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefParoleGrantActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefParoleDeniedActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefCommGrantActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefCommDeniedActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Granted"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefCondGrantActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Denial"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefCondDeniedActed += $upon->count;
								
								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefAbsGrantActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Denial"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefAbsDeniedActed += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTransferredSubmitted += $upon->count;

								$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Other"; $payload->additional_operator = "="; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefOthers += $upon->count;

								$totalPPIR = $totalRefParoleGrantActed +
									$totalRefParoleDeniedActed +
									$totalRefCommGrantActed +
									$totalRefCommDeniedActed +
									$totalRefCondGrantActed +
									$totalRefCondDeniedActed +
									$totalRefAbsGrantActed +
									$totalRefAbsDeniedActed ;
								
								$totalInvRef = $totalPPIR + $totalTransferredSubmitted + $totalRefOthers;

								// $disRate = $totalInvRef / $totalPPIR;

								$data[] = array('FIELD' => $value1->NAME,
												'totalRefAbsGrantActed'=> $totalRefAbsGrantActed,
												'totalRefAbsDeniedActed'=> $totalRefAbsDeniedActed,
												'totalPPIR'=> $totalPPIR,
												'totalTransferredSubmitted'=> $totalTransferredSubmitted,
												'totalRefOthers'=> $totalRefOthers,
												'totalInvRef'=> $totalInvRef,
												// 'disRate'=> $disRate,
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f21_field_office_pr_pd_f1_p1':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOverSupvParolIV = 0;
								$totalCarryOverSupvPardonIV = 0;
								$totalCarryOverSupvIV = 0;
								$totalRefRcvSupvParol = 0;
								$totalRefRcvSupvPardon = 0;
								$totalRefRcvSupv = 0;

								$totalSupervCasesHandledPR = 0;
								$totalSupervCasesHandledPD = 0;
								$totalSupervCasesHandled = 0;

								$payload->table = "F21T7_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverSupvParolIV += $upon->count;

								$payload->table = "F21T7_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverSupvPardonIV += $upon->count;

								$totalCarryOverSupvIV = $totalCarryOverSupvParolIV + $totalCarryOverSupvPardonIV;		
								
								$payload->table = "F21T8_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefRcvSupvParol += $upon->count;

								$payload->table = "F21T8_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
								$count = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefRcvSupvPardon += $upon->count;

								$totalRefRcvSupv = $totalRefRcvSupvParol + $totalRefRcvSupvPardon;	
								$totalSupervCasesHandledPR = $totalCarryOverSupvParolIV + $totalRefRcvSupvParol;
								$totalSupervCasesHandledPD = $totalCarryOverSupvPardonIV + $totalRefRcvSupvPardon;
								$totalSupervCasesHandled   = $totalSupervCasesHandledPR  + $totalSupervCasesHandledPD;
								$data[] = array('FIELD' => $value1->NAME,
												'totalCarryOverSupvParolIV'=> $totalCarryOverSupvParolIV,
												'totalCarryOverSupvPardonIV'=> $totalCarryOverSupvPardonIV,
												'totalCarryOverSupvIV'=> $totalCarryOverSupvIV,
												'totalRefRcvSupvParol'=> $totalRefRcvSupvParol,
												'totalRefRcvSupvPardon'=> $totalRefRcvSupvPardon,
												'totalRefRcvSupv'=> $totalRefRcvSupv,
												'totalSupervCasesHandledPR'=> $totalSupervCasesHandledPR,
												'totalSupervCasesHandledPD'=> $totalSupervCasesHandledPD,
												'totalSupervCasesHandled'=> $totalSupervCasesHandled,
												
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'f21_field_office_pr_pd_f1_p2':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOverSupvParolIV = 0;
								$totalCarryOverSupvPardonIV = 0;
								$totalCarryOverSupvIV = 0;
								$totalRefRcvSupvParol = 0;
								$totalRefRcvSupvPardon = 0;
								$totalCasesResolvedFinal = 0;
								$totalCasesResolvedArrest = 0;
								$totalCasesResolvedDeath = 0;
								$totalCasesResolvedRegional = 0;
								$totalCasesResolvedPSOther = 0;
								$totalCasesDropPR = 0;
								$totalCasesDropPD = 0;
								$totalCasesDrop = 0;

								$totalSupervCasesHandledPR = 0;
								$totalSupervCasesHandledPD = 0;
								$totalSupervCasesHandled = 0;

								$totalActiveSuperVisionPR = 0;
								$totalActiveSuperVisionPD = 0;
								$totalActiveSuperVision = 0;

								$totalCasesResolvedFinalParol = 0;
								$totalCasesResolvedFinalPardon = 0;
								$totalCasesResolvedArrestParol = 0;
								$totalCasesResolvedArrestPardon = 0;

								$totalCasesResolvedDeathParol = 0;
								$totalCasesResolvedDeathPardon = 0;
								$totalCasesResolvedOtherParol = 0;
								$totalCasesResolvedOtherPardon = 0;
								$totalCasesResolvedRegionalParol = 0;
								$totalCasesResolvedRegionalPardon = 0;
								$totalCasesResolvedRegional = 0;

								$payload->table = "F21T7_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverSupvParolIV += $upon->count;

								$payload->table = "F21T7_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverSupvPardonIV += $upon->count;

								$totalCarryOverSupvIV = $totalCarryOverSupvParolIV + $totalCarryOverSupvPardonIV;		
								
								$payload->table = "F21T8_PAROL"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefRcvSupvParol += $upon->count;

								$payload->table = "F21T8_PARDON"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRefRcvSupvPardon += $upon->count;


								$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedFinalParol += $upon->count;

								$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedFinalPardon += $upon->count;
								
								$totalCasesResolvedFinal = $totalCasesResolvedFinalParol + $totalCasesResolvedFinalPardon;	


								$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedArrestParol += $upon->count;

								$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedArrestPardon += $upon->count;

								$totalCasesResolvedArrest = $totalCasesResolvedArrestParol + $totalCasesResolvedArrestPardon;
														
								$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedDeathParol += $upon->count;


								$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedDeathPardon += $upon->count;

								$totalCasesResolvedDeath = $totalCasesResolvedDeathParol + $totalCasesResolvedDeathPardon;

								$payload->table = "F21T13_PAROL"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedRegionalParol += $upon->count;

								$payload->table = "F21T13_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedRegionalPardon += $upon->count;
								$totalCasesResolvedRegional = $totalCasesResolvedRegionalParol + $totalCasesResolvedRegionalPardon;		

								$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedOtherParol += $upon->count;

								$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedOtherPardon += $upon->count;

								$totalCasesResolvedPSOther = $totalCasesResolvedOtherParol + $totalCasesResolvedOtherPardon;	

								$totalSupervCasesHandledPR = $totalCarryOverSupvParolIV + $totalRefRcvSupvParol;
								$totalSupervCasesHandledPD = $totalCarryOverSupvPardonIV + $totalRefRcvSupvPardon;
								$totalSupervCasesHandled   = $totalSupervCasesHandledPR + $totalSupervCasesHandledPD;

								$totalCasesDropPR = $totalCasesResolvedFinalParol +
													$totalCasesResolvedArrestParol +
													$totalCasesResolvedDeathParol +
													$totalCasesResolvedRegionalParol +
													$totalCasesResolvedOtherParol;

								$totalCasesDropPD = $totalCasesResolvedFinalPardon +
													$totalCasesResolvedArrestPardon +
													$totalCasesResolvedDeathPardon +
													$totalCasesResolvedRegionalPardon +
													$totalCasesResolvedOtherPardon;
								$totalCasesDrop   = $totalCasesDropPR + $totalCasesDropPD;
								
								$totalActiveSuperVisionPR = $totalSupervCasesHandledPR - $totalCasesDropPR;
								$totalActiveSuperVisionPD = $totalSupervCasesHandledPD - $totalCasesDropPD;
								$totalActiveSuperVision = $totalActiveSuperVisionPR + $totalActiveSuperVisionPD;

								$data[] = array('FIELD' => $value1->NAME,
												'totalSupervCasesHandledPR'=> $totalSupervCasesHandledPR,
												'totalSupervCasesHandledPD'=> $totalSupervCasesHandledPD,
												'totalSupervCasesHandled'=> $totalSupervCasesHandled,
												'totalCasesResolvedFinalParol'=> $totalCasesResolvedFinalParol,
												'totalCasesResolvedFinalPardon'=> $totalCasesResolvedFinalPardon,
												'totalCasesResolvedFinal'=> $totalCasesResolvedFinal,
												'totalCasesResolvedArrestParol'=> $totalCasesResolvedArrestParol,
												'totalCasesResolvedArrestPardon'=> $totalCasesResolvedArrestPardon,
												'totalCasesResolvedArrest'=> $totalCasesResolvedArrest,
												'totalCasesResolvedDeathParol'=> $totalCasesResolvedDeathParol,
												'totalCasesResolvedDeathPardon'=> $totalCasesResolvedDeathPardon,
												'totalCasesResolvedDeath'=> $totalCasesResolvedDeath,
												'totalCasesResolvedRegionalParol'=> $totalCasesResolvedRegionalParol,
												'totalCasesResolvedRegionalPardon'=> $totalCasesResolvedRegionalPardon,
												'totalCasesResolvedRegional'=> $totalCasesResolvedRegional,
												'totalCasesResolvedOtherParol'=> $totalCasesResolvedOtherParol,
												'totalCasesResolvedOtherPardon'=> $totalCasesResolvedOtherPardon,
												'totalCasesResolvedPSOther'=> $totalCasesResolvedPSOther,
												'totalCasesDropPR'=> $totalCasesDropPR,
												'totalCasesDropPD'=> $totalCasesDropPD,
												'totalCasesDrop' => $totalCasesDrop,
												'totalActiveSuperVisionPR'=> $totalActiveSuperVisionPR,
												'totalActiveSuperVisionPD'=> $totalActiveSuperVisionPD,
												'totalActiveSuperVision' => $totalActiveSuperVision,
												 
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'f21_field_office_pr_pd_f1_p3':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$SupvActedSummaryTotal = 0;
								$SupvActedInfraTotal = 0;
								$SupvActedDeathTotal = 0;
								$SupvActedOtherTotal = 0;
								$totalPRSB = 0;
								$totalPD = 0;
								$totalPR = 0;
								$totalPDandPR = 0;
								$totalCasesActedUponByPPO = 0;

								$SupvActedSummaryParol = 0;
								$SupvActedSummaryPardon = 0;

								$SupvActedInfraParol = 0;
								$SupvActedInfraPardon = 0;

								$SupvActedDeathParol = 0;
								$SupvActedDeathPardon = 0;
								$SupvActedOtherParol = 0;
								$SupvActedOtherPardon = 0;

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedSummaryParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedSummaryPardon += $upon->count;
								$SupvActedSummaryTotal = $SupvActedSummaryParol + $SupvActedSummaryPardon;		

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedInfraParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedInfraPardon += $upon->count;
								$SupvActedInfraTotal = $SupvActedInfraParol + $SupvActedInfraPardon;	

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedDeathParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedDeathPardon += $upon->count;
								$SupvActedDeathTotal = $SupvActedDeathParol + $SupvActedDeathPardon;		

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedOtherParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedOtherPardon += $upon->count;

								$SupvActedOtherTotal = $SupvActedOtherParol + $SupvActedOtherPardon;	

								$totalPRSB = $SupvActedSummaryTotal +
											$SupvActedInfraTotal +
											$SupvActedDeathTotal +
											$SupvActedOtherTotal;

								$totalPR = $SupvActedSummaryParol +
											$SupvActedInfraParol +
											$SupvActedDeathParol +
											$SupvActedOtherParol;

								$totalPD = $SupvActedSummaryPardon +
											$SupvActedInfraPardon +
											$SupvActedDeathPardon +
											$SupvActedOtherPardon;

								$totalPDandPR = $totalPR + $totalPD;
								$totalCasesActedUponByPPO = $totalPRSB + $totalPDandPR;
								$data[] = array('FIELD' => $value1->NAME,
												'SupvActedSummaryParol'=> $SupvActedSummaryParol,
												'SupvActedSummaryPardon'=> $SupvActedSummaryPardon,
												'SupvActedSummaryTotal'=> $SupvActedSummaryTotal,
												'SupvActedInfraParol'=> $SupvActedInfraParol,
												'SupvActedInfraPardon'=> $SupvActedInfraPardon,
												'SupvActedInfraTotal'=> $SupvActedInfraTotal,
												'SupvActedDeathParol'=> $SupvActedDeathParol,
												'SupvActedDeathPardon'=> $SupvActedDeathPardon,
												'SupvActedDeathTotal'=> $SupvActedDeathTotal,
												'SupvActedOtherParol'=> $SupvActedOtherParol,
												'SupvActedOtherPardon'=> $SupvActedOtherPardon,
												'SupvActedOtherTotal'=> $SupvActedOtherTotal,
												'totalPRSB'=> $totalPRSB,
												'totalPR'=> $totalPR,
												'totalPD'=> $totalPD,
												'totalPDandPR'=> $totalPDandPR,
												'totalCasesActedUponByPPO'=> $totalCasesActedUponByPPO,
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	
				case 'f21_field_office_pr_pd_f2_p1':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOverPending = 0;
								$SupvActedSummaryTotal = 0;
								$SupvActedInfraTotal = 0;
								$SupvActedDeathTotal = 0;
								$SupvActedOtherTotal = 0;
								$totalReportSubmitted = 0;

								$totalPD = 0;
								$totalPR = 0;
								$totalPDandPR = 0;0;

								$totalCarryOverPendingParol = 0;
								$totalCarryOverPendingPardon = 0;
								$SupvActedSummaryParol = 0;
								$SupvActedSummaryPardon = 0;

								$SupvActedInfraParol = 0;
								$SupvActedInfraPardon = 0;
								$SupvActedDeathParol = 0;
								$SupvActedDeathPardon = 0;
								$SupvActedOtherParol = 0;
								$SupvActedOtherPardon = 0;

								$payload->table = "F21T10_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverPendingParol += $upon->count;

								$payload->table = "F21T10_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverPendingPardon += $upon->count;
								$totalCarryOverPending = $totalCarryOverPendingParol + $totalCarryOverPendingPardon;	

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedSummaryParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedSummaryPardon += $upon->count;
								$SupvActedSummaryTotal = $SupvActedSummaryParol + $SupvActedSummaryPardon;		

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedInfraParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedInfraPardon += $upon->count;
								$SupvActedInfraTotal = $SupvActedInfraParol + $SupvActedInfraPardon;		

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedDeathParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedDeathPardon += $upon->count;
								$SupvActedDeathTotal = $SupvActedDeathParol + $SupvActedDeathPardon;		

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedOtherParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedOtherPardon += $upon->count;
								$SupvActedOtherTotal = $SupvActedOtherParol + $SupvActedOtherPardon;

								$totalReportSubmitted = $SupvActedSummaryTotal +
											$SupvActedInfraTotal +
											$SupvActedDeathTotal +
											$SupvActedOtherTotal;

								$totalPR = $totalCarryOverPendingParol +
											$SupvActedSummaryParol +
											$SupvActedInfraParol +
											$SupvActedDeathParol +
											$SupvActedOtherParol;

								$totalPD = $totalCarryOverPendingPardon +
											$SupvActedSummaryPardon +
											$SupvActedInfraPardon +
											$SupvActedDeathPardon +
											$SupvActedOtherPardon;

								$totalPRandPD = $totalPR + $totalPD;

								$data[] = array('FIELD' => $value1->NAME,
												'totalCarryOverPendingParol'=> $totalCarryOverPendingParol,
												'totalCarryOverPendingPardon'=> $totalCarryOverPendingPardon,
												'totalCarryOverPending'=> $totalCarryOverPending,
												'SupvActedSummaryParol'=> $SupvActedSummaryParol,
												'SupvActedSummaryPardon'=> $SupvActedSummaryPardon,
												'SupvActedSummaryTotal'=> $SupvActedSummaryTotal,
												'SupvActedInfraParol'=> $SupvActedInfraParol,
												'SupvActedInfraPardon'=> $SupvActedInfraPardon,
												'SupvActedInfraTotal'=> $SupvActedInfraTotal,
												'SupvActedDeathParol'=> $SupvActedDeathParol,
												'SupvActedDeathPardon'=> $SupvActedDeathPardon,
												'SupvActedDeathTotal'=> $SupvActedDeathTotal,
												'SupvActedOtherParol'=> $SupvActedOtherParol,
												'SupvActedOtherPardon'=> $SupvActedOtherPardon,
												'SupvActedOtherTotal'=> $SupvActedOtherTotal,
												'totalReportSubmitted'=> $totalReportSubmitted,
												'totalPR'=> $totalPR,
												'totalPD'=> $totalPD,
												'totalPRandPD'=> $totalPRandPD,
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	

				case 'f21_field_office_pr_pd_f2_p2':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOverPending = 0;
								$SupvActedSummaryTotal = 0;
								$SupvActedInfraTotal = 0;
								$SupvActedDeathTotal = 0;
								$SupvActedOtherTotal = 0;
								$totalReportSubmitted = 0;
								$totalCasesResolvedFinal = 0;
								$totalCasesResolvedArrest = 0;
								$totalCasesResolvedDeath = 0;
								$totalCasesResolvedPSOther= 0;
								$totalPRCasesResolved= 0;
								$totalPDCasesResolved= 0;
								$totalCasesResolved= 0;
								$totalCasesPendingPR = 0;
								$totalCasesPendingPD = 0;
								$totalCasesPending = 0;

								$totalPD = 0;
								$totalPR = 0;
								$totalPDandPR = 0;

								$totalCarryOverPendingParol = 0;
								$totalCarryOverPendingPardon = 0;
								$SupvActedSummaryParol = 0;
								$SupvActedSummaryPardon = 0;
								$SupvActedInfraParol = 0;
								$SupvActedInfraPardon = 0;
								$SupvActedDeathParol = 0;
								$SupvActedDeathPardon = 0;
								$SupvActedOtherParol = 0;
								$SupvActedOtherPardon = 0;
								$totalCasesResolvedFinalParol = 0;
								$totalCasesResolvedFinalPardon = 0;
								$totalCasesResolvedArrestParol = 0;
								$totalCasesResolvedArrestPardon = 0;
								$totalCasesResolvedDeathParol = 0;
								$totalCasesResolvedDeathPardon = 0;
								$totalCasesResolvedOtherParol = 0;
								$totalCasesResolvedOtherPardon = 0;
								$payload->table = "F21T10_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverPendingParol += $upon->count;

								$payload->table = "F21T10_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverPendingPardon += $upon->count;
								$totalCarryOverPending = $totalCarryOverPendingParol + $totalCarryOverPendingPardon;	

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedSummaryParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedSummaryPardon += $upon->count;
								$SupvActedSummaryTotal = $SupvActedSummaryParol + $SupvActedSummaryPardon;		

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedInfraParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedInfraPardon += $upon->count;
								$SupvActedInfraTotal = $SupvActedInfraParol + $SupvActedInfraPardon;		

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedDeathParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedDeathPardon += $upon->count;
								$SupvActedDeathTotal = $SupvActedDeathParol + $SupvActedDeathPardon;		

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedOtherParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$SupvActedOtherPardon += $upon->count;
								$SupvActedOtherTotal = $SupvActedOtherParol + $SupvActedOtherPardon;

								$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL"; $payload->field_office = $value1->NAME;  
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedFinalParol += $upon->count;

								$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL"; $payload->field_office = $value1->NAME;  
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedFinalPardon += $upon->count;
								$totalCasesResolvedFinal = $totalCasesResolvedFinalParol + $totalCasesResolvedFinalPardon;	

								$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedArrestParol += $upon->count;

								$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedArrestPardon += $upon->count;
								$totalCasesResolvedArrest = $totalCasesResolvedArrestParol + $totalCasesResolvedArrestPardon;	

								$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedDeathParol += $upon->count;

								$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedDeathPardon += $upon->count;
								$totalCasesResolvedDeath = $totalCasesResolvedDeathParol + $totalCasesResolvedDeathPardon;	

								$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedOtherParol += $upon->count;

								$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedOtherPardon += $upon->count;
								$totalCasesResolvedPSOther = $totalCasesResolvedOtherParol + $totalCasesResolvedOtherPardon;	

								$totalReportSubmitted = $SupvActedSummaryTotal +
											$SupvActedInfraTotal +
											$SupvActedDeathTotal +
											$SupvActedOtherTotal
										;

								$totalPR = $totalCarryOverPendingParol +
											$SupvActedSummaryParol +
											$SupvActedInfraParol +
											$SupvActedDeathParol +
											$SupvActedOtherParol
										;

								$totalPD = $totalCarryOverPendingPardon +
											$SupvActedSummaryPardon +
											$SupvActedInfraPardon +
											$SupvActedDeathPardon +
											$SupvActedOtherPardon 
										;


								$totalPRCasesResolved = $totalCasesResolvedFinalParol +
											$totalCasesResolvedArrestParol +
											$totalCasesResolvedDeathParol +
											$totalCasesResolvedOtherParol
										;
								$totalPDCasesResolved = $totalCasesResolvedFinalPardon +
											$totalCasesResolvedArrestPardon +
											$totalCasesResolvedDeathPardon +
											$totalCasesResolvedOtherPardon
										;
								$totalCasesResolved = $totalPRCasesResolved + $totalPDCasesResolved;
								$totalPRandPD = $totalPR + $totalPD;

								$totalCasesPendingPR = $totalPR - $totalPRCasesResolved;
								$totalCasesPendingPD = $totalPD - $totalPDCasesResolved;
								$totalCasesPending = $totalCasesPendingPR + $totalCasesPendingPD;

								$data[] = array('FIELD' => $value1->NAME,
												'totalPR'=> $totalPR,
												'totalPD'=> $totalPD,
												'totalPRandPD'=> $totalPRandPD,
												'totalCasesResolvedFinalParol'=> $totalCasesResolvedFinalParol,
												'totalCasesResolvedFinalPardon'=> $totalCasesResolvedFinalPardon,
												'totalCasesResolvedFinal'=> $totalCasesResolvedFinal,
												'totalCasesResolvedArrestParol'=> $totalCasesResolvedArrestParol,
												'totalCasesResolvedArrestPardon'=> $totalCasesResolvedArrestPardon,
												'totalCasesResolvedArrest'=> $totalCasesResolvedArrest,
												'totalCasesResolvedDeathParol'=> $totalCasesResolvedDeathParol,
												'totalCasesResolvedDeathPardon'=> $totalCasesResolvedDeathPardon,
												'totalCasesResolvedDeath'=> $totalCasesResolvedDeath,
												'totalCasesResolvedOtherParol'=> $totalCasesResolvedOtherParol,
												'totalCasesResolvedOtherPardon'=> $totalCasesResolvedOtherPardon,
												'totalCasesResolvedPSOther'=> $totalCasesResolvedPSOther,
												'totalPRCasesResolved'=> $totalPRCasesResolved,
												'totalPDCasesResolved'=> $totalPDCasesResolved,
												'totalCasesResolved'=> $totalCasesResolved,
												'totalCasesPendingPR'=> $totalCasesPendingPR,
												'totalCasesPendingPD'=> $totalCasesPendingPD,
												'totalCasesPending'=> $totalCasesPending,
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	

				case 'f21_field_office_pr_pd_f3':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOverPendingRegional = 0;
								$totalReportSubmittedRegional = 0;
								$totalCasesResolvedRegional = 0;
								$totalCasesPendingResolutionRegionalParol = 0;
								$totalCasesPendingResolutionRegionalPardon = 0;
								$totalCasesPendingResolutionRegional = 0;

								$totalCarryOverPendingRegionalParol = 0;
								$totalCarryOverPendingRegionalPardon = 0;
								$totalReportSubmittedRegionalParol = 0;
								$totalReportSubmittedRegionalPardon = 0;
								
								$totalCasesResolvedRegionalParol = 0;
								$totalCasesResolvedRegionalPardon = 0;
								$payload->table = "F21T12_PAROL"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverPendingRegionalParol += $upon->count;

								$payload->table = "F21T12_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverPendingRegionalPardon += $upon->count;
								$totalCarryOverPendingRegional = $totalCarryOverPendingRegionalParol + $totalCarryOverPendingRegionalPardon;

								$payload->table = "F21T9_PAROL"; $payload->filter_field = "transfer"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalReportSubmittedRegionalParol += $upon->count;

								$payload->table = "F21T9_PARDON"; $payload->filter_field = "transfer"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalReportSubmittedRegionalPardon += $upon->count;
								$totalReportSubmittedRegional = $totalReportSubmittedRegionalParol + $totalReportSubmittedRegionalPardon;	

								$payload->table = "F21T13_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedRegionalParol += $upon->count;

								$payload->table = "F21T13_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCasesResolvedRegionalPardon += $upon->count;
								$totalCasesResolvedRegional = $totalCasesResolvedRegionalParol + $totalCasesResolvedRegionalPardon;	

								$totalCasesPendingResolutionRegionalParol = $totalCarryOverPendingRegionalParol - $totalCasesResolvedRegionalParol;		
								$totalCasesPendingResolutionRegionalPardon = $totalCarryOverPendingRegionalPardon - $totalCasesResolvedRegionalPardon;		
								$totalCasesPendingResolutionRegional = $totalCasesPendingResolutionRegionalParol + $totalCasesPendingResolutionRegionalPardon;		

								$data[] = array('FIELD' => $value1->NAME,
												'totalCarryOverPendingRegionalParol'=> $totalCarryOverPendingRegionalParol,
												'totalCarryOverPendingRegionalPardon'=> $totalCarryOverPendingRegionalPardon,
												'totalCarryOverPendingRegional'=> $totalCarryOverPendingRegional,
												'totalReportSubmittedRegionalParol'=> $totalReportSubmittedRegionalParol,
												'totalReportSubmittedRegionalPardon'=> $totalReportSubmittedRegionalPardon,
												'totalReportSubmittedRegional'=> $totalReportSubmittedRegional,
												'totalCasesResolvedRegionalParol'=> $totalCasesResolvedRegionalParol,
												'totalCasesResolvedRegionalPardon'=> $totalCasesResolvedRegionalPardon,
												'totalCasesResolvedRegional'=> $totalCasesResolvedRegional,
												'totalCasesPendingResolutionRegionalParol'=> $totalCasesPendingResolutionRegionalParol,
												'totalCasesPendingResolutionRegionalPardon'=> $totalCasesPendingResolutionRegionalPardon,
												'totalCasesPendingResolutionRegional'=> $totalCasesPendingResolutionRegional,

									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	

				case 'f21_field_office_pr_pd_f4':

						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						// (A)

						if($fieldOffice->status == 'SUCCESS'){
							
							foreach ($fieldOffice->payload as $key1 => $value1){
								$totalCarryOverSupv = 0;
								$totalRcvSupv = 0;
								$totalCourtesySupvParol = 0;
								$totalCourtesySupvPardon = 0;
								$totalCourtesySupvTotal = 0;
								$totalTermSupv = 0;
								$totalActCourtesySupvParol = 0;
								$totalActCourtesySupvPardon = 0;
								$totalActCourtesySupv = 0;
								$totalCarryOverSupvParol = 0;
								$totalCarryOverSupvPardon = 0;
								$totalRcvSupvParol = 0;
								$totalRcvSupvPardon = 0;
								$totalTermSupvParol = 0;
								$totalTermSupvPardon = 0;
								
								$payload->table = "F21T14_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverSupvParol += $upon->count;

								$payload->table = "F21T14_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverSupvPardon += $upon->count;
								$totalCarryOverSupv = $totalCarryOverSupvParol + $totalCarryOverSupvPardon;

								$payload->table = "F21T15_RCV_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRcvSupvParol += $upon->count;

								$payload->table = "F21T15_RCV_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRcvSupvPardon += $upon->count;
								$totalRcvSupv = $totalRcvSupvParol + $totalRcvSupvPardon;	

								$totalCourtesySupvParol = $totalCarryOverSupvParol + $totalRcvSupvParol;		
								$totalCourtesySupvPardon = $totalCarryOverSupvPardon + $totalRcvSupvPardon;		
								$totalCourtesySupvTotal = $totalCourtesySupvParol + $totalCourtesySupvPardon;		

								$payload->table = "F21T15_TERM_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTermSupvParol += $upon->count;

								$payload->table = "F21T15_TERM_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTermSupvPardon += $upon->count;
								$totalTermSupv = $totalTermSupvParol + $totalTermSupvPardon;

								$totalActCourtesySupvParol = $totalCourtesySupvParol - $totalTermSupvParol;		
								$totalActCourtesySupvPardon = $totalCourtesySupvPardon - $totalTermSupvPardon;		
								$totalActCourtesySupv = $totalActCourtesySupvParol + $totalActCourtesySupvPardon;		
								
								$data[] = array('FIELD' => $value1->NAME,
												'totalCarryOverSupvParol'=> $totalCarryOverSupvParol,
												'totalCarryOverSupvPardon'=> $totalCarryOverSupvPardon,
												'totalCarryOverSupv'=> $totalCarryOverSupv,
												'totalRcvSupvParol'=> $totalRcvSupvParol,
												'totalRcvSupvPardon'=> $totalRcvSupvPardon,
												'totalRcvSupv'=> $totalRcvSupv,
												'totalCourtesySupvParol'=> $totalCourtesySupvParol,
												'totalCourtesySupvPardon'=> $totalCourtesySupvPardon,
												'totalCourtesySupvTotal'=> $totalCourtesySupvTotal,
												'totalTermSupvParol'=> $totalTermSupvParol,
												'totalTermSupvPardon'=> $totalTermSupvPardon,
												'totalTermSupv'=> $totalTermSupv,
												'totalActCourtesySupvParol'=> $totalActCourtesySupvParol,
												'totalActCourtesySupvPardon'=> $totalActCourtesySupvPardon,
												'totalActCourtesySupv'=> $totalActCourtesySupv,

									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	
				case 'quarterly_f1':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							$totalGrant = 0;
							$totalDenial = 0;
							$totalManifest = 0;
							$totalTransfer = 0;
							$totalActed = 0;
							$totalRecall = 0;
							$totalWarrant = 0;
							$totalNotActed = 0;

							$totalCarryOver = 0;
							$totalRcv = 0;
							$totalInvestigation = 0;
							$totalRcv2 = 0;

							$totalActive = 0;
							if($fieldOffice->status == 'SUCCESS'){

								foreach ($fieldOffice->payload as $key1 => $value1){


									$payload->filter = ""; $payload->table = "F5T1"; $payload->field_office = $value1->NAME; $payload->filter_val="";$payload->filter_field="";
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;


									$payload->filter = ""; $payload->table = "F5T2_RCV"; $payload->field_office = $value1->NAME;$payload->filter_val="";$payload->filter_field="";
									$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRcv += $rcv->count;

									$totalRcv2 = $totalRcv;
									$totalInvestigation = $totalCarryOver + $totalRcv;


									#echo $this->Cmis_F5T2_model->fetchF5T2_RCV_ByYM($payload);
									$payload->table = "F5T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Grant"; $payload->field_office = $value1->NAME;
									$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalGrant += $grant_upon->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Denial"; $payload->field_office = $value1->NAME;
									$denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDenial += $denial_upon->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "manifest_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$manifest_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalManifest += $manifest_upon->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$transfer_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTransfer += $transfer_upon->count;

									$payload->table = "F5T2_NOTACTED"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Recall"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRecall += $upon->count;

									$payload->table = "F5T2_NOTACTED"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Warrant"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalWarrant += $upon->count;

									
									$totalActed = $totalGrant + $totalDenial + $totalManifest + $totalTransfer;
									$totalNotActed = $totalRecall + $totalWarrant;
									$totalActive = $totalInvestigation - ($totalActed + $totalNotActed);
									#($act_upon);
									$totalActiveInvestigationCaseload = $totalInvestigation - $totalActed - $totalNotActed;
								};
							}
							

							

							$data[] = array('REGION' => $value->VALUE_,
											'ID' => $value->ID,
											'carryOver' => $totalCarryOver,
											'totalRcv' => $totalRcv,
											'totalRcv2' => $totalRcv2,
											'totalInvestigation' => $totalInvestigation,
											'totalGrant' => $totalGrant,
											'totalDenial' => $totalDenial,
											'totalManifest' => $totalManifest,
											'totalTransfer' => $totalTransfer,
											'totalActed' => $totalActed,
											'totalRecall' => $totalRecall,
											'totalWarrant' => $totalWarrant,
											'totalNotActed' => $totalNotActed,
											'totalActive' => $totalActive,
											'totalActiveInvestigationCaseload' => $totalActiveInvestigationCaseload,

										);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'quarterly_f2':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							$totalCarryOver = 0;
							$totalPSIR = 0;
							$totalManifest = 0;
							$totalSubmitted = 0;
							$totalbeActed = 0;

							$totalGrant = 0;
							$totalDenial = 0;
							$totalDismiss = 0;
							$totalWithdraw = 0;
							$totalReinv = 0;
							$totalOther = 0;
							$totalWarrant = 0;
							$totalRecall = 0;
							$totalDisposed = 0;
							$totalNotActed = 0;
							$totalPending = 0;
							if($fieldOffice->status == 'SUCCESS'){

								foreach ($fieldOffice->payload as $key1 => $value1){


									$payload->filter = ""; $payload->table = "F5T3"; $payload->field_office = $value1->NAME; $payload->filter_val="";$payload->filter_field="";
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;


									$payload->table = "F5T2_ACTED"; $payload->filter_field = "psir_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalPSIR += $upon->count;

									$payload->table = "F5T2_ACTED"; $payload->filter_field = "manifest_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalManifest += $upon->count;

									$totalSubmitted = $totalPSIR + $totalManifest;
									$totalbeActed = $totalSubmitted + $totalCarryOver;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Grant"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalGrant += $upon->count;

									
									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Den"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDenial += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismi"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDismiss += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalWithdraw += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Reinv"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalReinv += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Other"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalOther += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Warrant"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalWarrant += $upon->count;

									$payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Recall"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRecall += $upon->count;

									$totalDisposed = $totalGrant + $totalDenial + $totalDismiss + $totalWithdraw + $totalReinv + $totalOther;
									$totalNotActed = $totalWarrant + $totalRecall;

									$totalPending = $totalbeActed - $totalDisposed;
								};
							}
							

							

							$data[] = array('REGION' => $value->VALUE_,
											'ID' => $value->ID,
											'totalCarryOver' => $totalCarryOver,
											'totalPSIR' => $totalPSIR,
											'totalManifest' => $totalManifest,
											'totalSubmitted' => $totalSubmitted,
											'totalbeActed' => $totalbeActed,
											'totalGrant' => $totalGrant,
											'totalDenial' => $totalDenial,
											'totalDismiss' => $totalDismiss,
											'totalWithdraw' => $totalWithdraw,
											'totalReinv' => $totalReinv,
											'totalOther' => $totalOther,
											'totalDisposed' => $totalDisposed,
											'totalWarrant' => $totalWarrant,
											'totalRecall' => $totalRecall,
											'totalNotActed' => $totalNotActed,
											'totalPending' => $totalPending,

										);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				
				case 'quarterly_f3':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							
							if($fieldOffice->status == 'SUCCESS'){
								$totalCarryOver = 0;
								$totalFullTerm = 0;
								$totalEarlyTerm = 0;
								$totalDiedTerm = 0;

								$totalAbs = 0;
								$totalComm = 0;
								$totalVio = 0;
								$totalOther = 0;
								$totalRevoc = 0;


								$totalInvestigation = 0;
								$totalRcv = 0;
								$totalRcv2 = 0;
								$totalTerm = 0;

								$totalTransfer = 0;
								$totalOthers = 0;
								$totalDropped = 0;
								$totalActive = 0;

								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->filter_val = ""; $payload->filter_field = ""; $payload->filter = ""; $payload->table = "F5T7"; $payload->field_office = $value1->NAME;
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;


									$payload->filter_val = ""; $payload->filter_field = ""; $payload->filter = ""; $payload->table = "F5T8"; $payload->field_office = $value1->NAME;
									$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRcv += $rcv->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Full Term"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalFullTerm += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Early"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalEarlyTerm += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Died"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDiedTerm += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abs"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalAbs += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Comm"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalComm += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Vio"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalVio += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalOther += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTransfer += $upon->count;

									$payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalOthers += $upon->count;

									$totalTerm = $totalFullTerm + $totalEarlyTerm + $totalDiedTerm;

									$totalRcv2 = $totalRcv;
									$totalInvestigation = $totalCarryOver + $totalRcv;
									$totalRevoc = $totalAbs + $totalComm + $totalVio + $totalOther;

									$totalDropped = $totalTerm + $totalRevoc + $totalTransfer + $totalOthers;
									$totalActive = $totalInvestigation - $totalDropped;

									
								}
									$data[] = array('REGION' => $value->VALUE_,
											'totalCarryOver' => $totalCarryOver,
											'totalRcv2' => $totalRcv2,
											'totalInvestigation' => $totalInvestigation,
											'totalFullTerm' => $totalFullTerm,
											'totalEarlyTerm' => $totalEarlyTerm,
											'totalDiedTerm' => $totalDiedTerm,
											'totalTerm' => $totalTerm,
											'totalAbs' => $totalAbs,
											'totalComm' => $totalComm,
											'totalVio' => $totalVio,
											'totalOther' => $totalOther,
											'totalRevoc' => $totalRevoc,
											'totalTransfer' => $totalTransfer,
											'totalOthers' => $totalOthers,
											'totalDropped' => $totalDropped,
											'totalActive' => $totalActive,

										);
							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'quarterly_f4':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
								
							$totalCarryOver = 0;
							$totalRcv = 0;
							$totalCasesHandled = 0;

							$totalGrant = 0;
							$totalDenial = 0;
							$commutationTotalGrant = 0;
							$commutationTotalDenial = 0;
							$absoluteTotalGrant = 0;
							$totalppir = 0;

							$totalParole = 0;
							$totalcommutation = 0;

							$totalTransfer = 0;
							$totalInvRef = 0;
							$totalActiveInv = 0;

							if($fieldOffice->status == 'SUCCESS'){
								
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOver += $upon->count;

									$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcv += $upon->count;

									$totalCasesHandled = $totalCarryOver + $totalRcv;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; $payload->field_office = $value1->NAME;
									$grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalGrant += $grant_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; $payload->field_office = $value1->NAME;
									$denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalDenial += $denial_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; $payload->field_office = $value1->NAME;
									$commutation_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$commutationTotalGrant += $commutation_grant_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; $payload->field_office = $value1->NAME;
									$commutation_denial_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$commutationTotalDenial += $commutation_denial_upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; $payload->field_office = $value1->NAME;
									$absolute_grant_upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$absoluteTotalGrant += $absolute_grant_upon->count;

									$totalParole = $totalGrant + $totalDenial;
									$totalcommutation = $commutationTotalGrant + $commutationTotalDenial;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTransfer += $upon->count;

									$totalInvRef = $totalParole + $totalcommutation + $absoluteTotalGrant + $totalTransfer;
									$totalActiveInv = $totalCasesHandled - $totalInvRef;
								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOver'=> $totalCarryOver,
													'totalRcv'=> $totalRcv,
													'totalCasesHandled'=> $totalCasesHandled,
													'totalParole'=> $totalParole,
													'totalcommutation'=> $totalcommutation,
													'absoluteTotalGrant'=> $absoluteTotalGrant,
													'totalTransfer'=> $totalTransfer,
													'totalInvRef'=> $totalInvRef,
													'totalActiveInv'=> $totalActiveInv,
										);
							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'quarterly_f5': 

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
								
							
							if($fieldOffice->status == 'SUCCESS'){
								$totalCarryOverSupvParolIV = 0;
								$totalCarryOverSupvPardonIV = 0;
								$totalCarryOverSupvIV = 0;
								$totalRefRcvSupvParol = 0;
								$totalRefRcvSupvPardon = 0;
								foreach ($fieldOffice->payload as $key1 => $value1){
									$payload->table = "F21T7_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvParolIV += $upon->count;

									$payload->table = "F21T7_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvPardonIV += $upon->count;

									$totalCarryOverSupvIV = $totalCarryOverSupvParolIV + $totalCarryOverSupvPardonIV;

									$payload->table = "F21T8_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefRcvSupvParol += $upon->count;

									$payload->table = "F21T8_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefRcvSupvPardon += $upon->count;
									$totalRefRcvSupv = $totalRefRcvSupvParol + $totalRefRcvSupvPardon;	
									$totalSupervCasesHandledPR = $totalCarryOverSupvParolIV  + $totalRefRcvSupvParol;
									$totalSupervCasesHandledPD = $totalCarryOverSupvPardonIV + $totalRefRcvSupvPardon;
									$totalSupervCasesHandled   = $totalSupervCasesHandledPR  + $totalSupervCasesHandledPD;
								}
									$data[] = array('REGION' => $value->VALUE_,
										'totalCarryOverSupvParolIV'=> $totalCarryOverSupvParolIV,
										'totalCarryOverSupvPardonIV'=> $totalCarryOverSupvPardonIV,
										'totalCarryOverSupvIV'=> $totalCarryOverSupvIV,
										'totalRefRcvSupvParol'=> $totalRefRcvSupvParol,
										'totalRefRcvSupvPardon'=> $totalRefRcvSupvPardon,
										'totalRefRcvSupv'=> $totalRefRcvSupv,
										'totalSupervCasesHandledPR'=> $totalSupervCasesHandledPR,
										'totalSupervCasesHandledPD'=> $totalSupervCasesHandledPD,
										'totalSupervCasesHandled'=> $totalSupervCasesHandled,
																	
									);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'quarterly_f6':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							
							if($fieldOffice->status == 'SUCCESS'){
								$totalCarryOverSupvParolIV = 0;
								$totalCarryOverSupvPardonIV = 0;
								$totalCarryOverSupvIV = 0;
								$totalRefRcvSupvParol = 0;
								$totalRefRcvSupvPardon = 0;
								$totalCasesResolvedFinal = 0;
								$totalCasesResolvedArrest = 0;
								$totalCasesResolvedDeath = 0;
								$totalCasesResolvedPSOther = 0;
								$totalCasesDropPR = 0;
								$totalCasesDropPD = 0;
								$totalCasesDrop = 0;

								$totalSupervCasesHandledPR = 0;
								$totalSupervCasesHandledPD = 0;
								$totalSupervCasesHandled = 0;

								$totalActiveSuperVisionPR = 0;
								$totalActiveSuperVisionPD = 0;
								$totalActiveSuperVision = 0;

								$totalCarryOverSupvParolIV = 0;
								$totalCasesResolvedFinalParol = 0;
								$totalCasesResolvedFinalPardon = 0;
								$totalCasesResolvedArrestParol = 0;

								$totalCasesResolvedArrestPardon = 0;
								$totalCasesResolvedDeathParol = 0;
								$totalCasesResolvedDeathPardon = 0;
								$totalCasesResolvedRegionalParol = 0;
								$totalCasesResolvedRegionalPardon = 0;
								$totalCasesResolvedOtherParol = 0;
								$totalCasesResolvedOtherPardon = 0;
								$totalCasesResolvedPSOther = 0;
								$totalN = 0;

								foreach ($fieldOffice->payload as $key1 => $value1){


									$payload->table = "F21T7_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvParolIV += $upon->count;

									$payload->table = "F21T7_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverSupvPardonIV += $upon->count;

									$totalCarryOverSupvIV = $totalCarryOverSupvParolIV + $totalCarryOverSupvPardonIV;		
									
									$payload->table = "F21T8_PAROL"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefRcvSupvParol += $upon->count;

									$payload->table = "F21T8_PARDON"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefRcvSupvPardon += $upon->count;

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedFinalParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedFinalPardon += $upon->count;

									$totalCasesResolvedFinal = $totalCasesResolvedFinalParol + $totalCasesResolvedFinalPardon;	

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedArrestParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedArrestPardon += $upon->count;
									$totalCasesResolvedArrest = $totalCasesResolvedArrestParol + $totalCasesResolvedArrestPardon;		
									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedDeathParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedDeathPardon += $upon->count;
									$totalCasesResolvedDeath = $totalCasesResolvedDeathParol + $totalCasesResolvedDeathPardon;

									$payload->table = "F21T13_PAROL"; $payload->filter_field = ""; $payload->filter_val = "";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedRegionalParol += $upon->count;

									$payload->table = "F21T13_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedRegionalPardon += $upon->count;
									$totalCasesResolvedRegional = $totalCasesResolvedRegionalParol + $totalCasesResolvedRegionalPardon;		

									$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedOtherParol += $upon->count;

									$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS";  $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCasesResolvedOtherPardon += $upon->count;
									$totalCasesResolvedPSOther = $totalCasesResolvedOtherParol + $totalCasesResolvedOtherPardon;	

									$totalSupervCasesHandledPR = $totalCarryOverSupvParolIV + $totalRefRcvSupvParol;
									$totalSupervCasesHandledPD = $totalCarryOverSupvPardonIV + $totalRefRcvSupvPardon;
									$totalSupervCasesHandled   = $totalSupervCasesHandledPR  + $totalSupervCasesHandledPD;

									$totalCasesDropPR = $totalCasesResolvedFinalParol +
														$totalCasesResolvedArrestParol +
														$totalCasesResolvedDeathParol +
														$totalCasesResolvedRegionalParol +
														$totalCasesResolvedOtherParol;

									$totalCasesDropPD = $totalCasesResolvedFinalPardon +
														$totalCasesResolvedArrestPardon +
														$totalCasesResolvedDeathPardon +
														$totalCasesResolvedRegionalPardon +
														$totalCasesResolvedOtherPardon;
									$totalCasesDrop   = $totalCasesDropPR + $totalCasesDropPD;
									
									$totalActiveSuperVisionPR = $totalSupervCasesHandledPR - $totalCasesDropPR;
									$totalActiveSuperVisionPD = $totalSupervCasesHandledPD - $totalCasesDropPD;
									$totalActiveSuperVision = $totalActiveSuperVisionPR + $totalActiveSuperVisionPD;

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalSupervCasesHandledPR'=> $totalSupervCasesHandledPR,
													'totalSupervCasesHandledPD'=> $totalSupervCasesHandledPD,
													'totalSupervCasesHandled'=> $totalSupervCasesHandled,
													'totalCasesResolvedFinalParol'=> $totalCasesResolvedFinalParol,
													'totalCasesResolvedFinalPardon'=> $totalCasesResolvedFinalPardon,
													'totalCasesResolvedFinal'=> $totalCasesResolvedFinal,
													'totalCasesResolvedArrestParol'=> $totalCasesResolvedArrestParol,
													'totalCasesResolvedArrestPardon'=> $totalCasesResolvedArrestPardon,
													'totalCasesResolvedArrest'=> $totalCasesResolvedArrest,
													'totalCasesResolvedDeathParol'=> $totalCasesResolvedDeathParol,
													'totalCasesResolvedDeathPardon'=> $totalCasesResolvedDeathPardon,
													'totalCasesResolvedDeath'=> $totalCasesResolvedDeath,
													'totalCasesResolvedRegionalParol'=> $totalCasesResolvedRegionalParol,
													'totalCasesResolvedRegionalPardon'=> $totalCasesResolvedRegionalPardon,
													'totalCasesResolvedRegional'=> $totalCasesResolvedRegional,
													'totalCasesResolvedOtherParol'=> $totalCasesResolvedOtherParol,
													'totalCasesResolvedOtherPardon'=> $totalCasesResolvedOtherPardon,
													'totalCasesResolvedPSOther'=> $totalCasesResolvedPSOther,
													'totalCasesDropPR'=> $totalCasesDropPR,
													'totalCasesDropPD'=> $totalCasesDropPD,
													'totalCasesDrop' => $totalCasesDrop,
													'totalActiveSuperVisionPR'=> $totalActiveSuperVisionPR,
													'totalActiveSuperVisionPD'=> $totalActiveSuperVisionPD,
													'totalActiveSuperVision' => $totalActiveSuperVision,
													 
										);


							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	

				case 'quarterly_f7':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
								
							$getTotalCarryAdultInvestigation = 0;
							$getTotalCarryAdultInvestigationRpi = 0;
							$getTotalCarryAdultInvestigationTpi = 0;
							$getTotalCarryJICLInvestigation = 0;
							$getTotalCarryJICLInvestigationJRPI = 0;
							$getTotalCarryJICLInvestigationJTPI = 0;

							$carryOverInvestigationAdult = 0;
							$carryOverInvestigationJICL = 0;
							$carryOverInvestigationTotal = 0;
							
							$rcvInvestigationJICLMilNewJPI = 0;
							$rcvInvestigationJICLMilNewJTPI = 0;
							$rcvInvestigationAdultMilNewJPI = 0;
							$rcvInvestigationAdultMilNewJTPI = 0;
							$rcvInvestigationJICLMilNew = 0;
							$rcvInvestigationAdultMilNe = 0;
							$rcvInvestigationTotalMilNew = 0;
							
							$rcvInvestigationJICLCivilNewJPI = 0;
							$rcvInvestigationJICLCivilNewJTPI = 0;
							$rcvInvestigationAdultCivilNewJPI = 0;
							$rcvInvestigationAdultCivilNewJTPI = 0;
							$rcvInvestigationJICLCivilNew = 0;
							$rcvInvestigationAdultCivilNew = 0;
							$rcvInvestigationTotalCivilNew = 0;

							$rcvInvestigationJICLNew = 0;
							$rcvInvestigationAdultNew = 0;
							$rcvInvestigationTotalNew = 0;
							$rcvInvestigationJICLRPI = 0;
							$rcvInvestigationAdultRPI = 0;
							$rcvInvestigationTotalRPI = 0;
							$rcvInvestigationJICL = 0;
							$rcvInvestigationAdult = 0;
							$rcvInvestigationTotal = 0;

							$totalHandled = 0;

							$getTotalActedAdultInvestigationTransfer = 0;
							$getTotalActedJICLInvestigationTransfer = 0;
							$actedInvestigationAdultTransfer = 0;
							$actedInvestigationJICLTransfer = 0;
							$actedInvestigationTotalTransfer = 0;

							$getTotalNotActedAdultInvestigationRecalled = 0;
							$getTotalNotActedJICLInvestigationRecalled = 0;
							$getTotalNotActedTotalInvestigationRecalled = 0;
							$notactedAdultInvestigationRecalled = 0;
							$notactedJICLInvestigationRecalled = 0;
							$notactedTotalInvestigationRecalled = 0;

							$notactedAdultInvestigationWarrant = 0;
							$notactedJICLInvestigationWarrant = 0;
							$notactedTotalInvestigationWarrant = 0;
							
							$getTotalNotActedAdultInvestigationWarrant = 0;
							$getTotalNotActedJICLInvestigationWarrant = 0;
							$getTotalNotActedTotalInvestigationWarrant = 0;

							$rcvRef = 0;
							$rcvRefGranted = 0;
							$rcvRefDenial = 0;
							$totalRef = 0;
							$totalGrant = 0;
							$totalDenial = 0;

							$getTotalActedAdultInvestigationGrant = 0;
							$getTotalActedAdultInvestigationDenial = 0;
							$actedInvestigationAdultGrant = 0;
							$actedInvestigationAdultDenial = 0;
							$getTotalActedJICLInvestigationGrant = 0;
							$getTotalActedJICLInvestigationDenial = 0;
							$actedInvestigationJICLGrant = 0;
							$actedInvestigationJICLDenial = 0;
							$actedInvestigationTotalGrant = 0;
							$actedInvestigationTotalDenial = 0;

							$getTotalActedAdultInvestigationManifest = 0;
							$getTotalActedJICLInvestigationManifest = 0;
							$actedInvestigationAdultManifest = 0;
							$actedInvestigationJICLManifest = 0;
							$actedInvestigationTotalManifest = 0;

							$totalInvRefref = 0;

							if($fieldOffice->status == 'SUCCESS'){
								foreach ($fieldOffice->payload as $key1 => $value1){	

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"PI"));
									$getTotalCarryAdultInvestigation += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"RPI"));
									$getTotalCarryAdultInvestigationRpi += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"TPI"));
									$getTotalCarryAdultInvestigationTpi += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"JPI"));
									$getTotalCarryJICLInvestigation += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"JRPI"));
									$getTotalCarryJICLInvestigationJRPI += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalCarryInvestigation($payload,"JTPI"));
									$getTotalCarryJICLInvestigationJTPI += $upon;

									$carryOverInvestigationAdult = $getTotalCarryAdultInvestigation + $getTotalCarryAdultInvestigationRpi + $getTotalCarryAdultInvestigationTpi;

									$carryOverInvestigationJICL = $getTotalCarryJICLInvestigation + $getTotalCarryJICLInvestigationJRPI + $getTotalCarryJICLInvestigationJTPI;

									$carryOverInvestigationTotal = $carryOverInvestigationAdult + $carryOverInvestigationJICL;
									/////////

									$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1";$payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLCivilNewJPI += $upon->count;
									
									$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1";$payload->field_office = $value1->NAME; 
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLCivilNewJTPI += $upon->count;

									$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultCivilNewJPI += $upon->count;
									
									$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultCivilNewJTPI += $upon->count;

									$rcvInvestigationJICLCivilNew = $rcvInvestigationJICLCivilNewJPI + $rcvInvestigationJICLCivilNewJTPI;
									$rcvInvestigationAdultCivilNew = $rcvInvestigationAdultCivilNewJPI + $rcvInvestigationAdultCivilNewJTPI;
									$rcvInvestigationTotalCivilNew = $rcvInvestigationJICLCivilNew + $rcvInvestigationAdultCivilNew;
									/////////////

									$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLMilNewJPI += $upon->count;
									
									$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLMilNewJTPI += $upon->count;

									$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultMilNewJPI += $upon->count;
									
									$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultMilNewJTPI += $upon->count;

									$rcvInvestigationJICLMilNew  = $rcvInvestigationJICLMilNewJPI + $rcvInvestigationJICLMilNewJTPI;
									$rcvInvestigationAdultMilNew  = $rcvInvestigationAdultMilNewJPI + $rcvInvestigationAdultMilNewJTPI;
									$rcvInvestigationTotalMilNew = $rcvInvestigationJICLMilNew + $rcvInvestigationAdultMilNew;

									///////////
									$rcvInvestigationJICLNew = $rcvInvestigationJICLCivilNew + $rcvInvestigationJICLMilNew;
									$rcvInvestigationAdultNew = $rcvInvestigationAdultCivilNew + $rcvInvestigationAdultMilNew;
									$rcvInvestigationTotalNew = $rcvInvestigationJICLNew + $rcvInvestigationAdultNew;


									$payload->filter = "JRPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationJICLRPI += $upon->count;

									$payload->filter = "RPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$rcvInvestigationAdultRPI += $upon->count;

									$rcvInvestigationTotalRPI = $rcvInvestigationAdultRPI + $rcvInvestigationJICLRPI;

									$rcvInvestigationJICL = $rcvInvestigationJICLNew + $rcvInvestigationJICLRPI;
									$rcvInvestigationAdult = $rcvInvestigationAdultNew + $rcvInvestigationAdultRPI;
									$rcvInvestigationTotal = $rcvInvestigationJICL + $rcvInvestigationAdult;
									$totalHandled = $carryOverInvestigationTotal + $rcvInvestigationTotal;
									///////////////////

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","",0,1));
									$getTotalActedAdultInvestigationTransfer += $upon;

									$payload->field_office = $value1->NAME;
									$upon =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","",0,1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","",0,1));
									$getTotalActedJICLInvestigationTransfer += $upon;

									$actedInvestigationAdultTransfer = $getTotalActedAdultInvestigationTransfer;
									$actedInvestigationJICLTransfer  = $getTotalActedJICLInvestigationTransfer;
									$actedInvestigationTotalTransfer = $getTotalActedAdultInvestigationTransfer + $getTotalActedJICLInvestigationTransfer;

									////////////////////////
									$payload->field_office = $value1->NAME;
									$upon =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","Recall"));
									$getTotalNotActedAdultInvestigationRecalled += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","Recall")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","Recall"));
									$getTotalNotActedJICLInvestigationRecalled += $upon;

									$getTotalNotActedTotalInvestigationRecalled = $getTotalNotActedAdultInvestigationRecalled + $getTotalNotActedJICLInvestigationRecalled;

									$notactedAdultInvestigationRecalled = $getTotalNotActedAdultInvestigationRecalled;
									$notactedJICLInvestigationRecalled = $getTotalNotActedJICLInvestigationRecalled;
									$notactedTotalInvestigationRecalled = $getTotalNotActedTotalInvestigationRecalled;


									$payload->field_office = $value1->NAME;
									$upon =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","Warrant"))  + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","Warrant")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","Warrant"));
									$getTotalNotActedAdultInvestigationWarrant += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","Warrant "))  + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","Warrant ")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","Warrant"));
									$getTotalNotActedJICLInvestigationWarrant += $upon;

									$getTotalNotActedTotalInvestigationWarrant = $getTotalNotActedAdultInvestigationWarrant + $getTotalNotActedJICLInvestigationWarrant;

									$notactedAdultInvestigationWarrant = $getTotalNotActedAdultInvestigationWarrant;
									$notactedJICLInvestigationWarrant  = $getTotalNotActedJICLInvestigationWarrant;
									$notactedTotalInvestigationWarrant = $getTotalNotActedTotalInvestigationWarrant;
									$totalRef = $notactedTotalInvestigationRecalled + $notactedTotalInvestigationWarrant;

									// /////////////
									
									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","FOR GRANT"));
									$getTotalActedAdultInvestigationGrant += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","FOR DENIAL"));
									$getTotalActedAdultInvestigationDenial += $upon;

									$actedInvestigationAdultGrant = $getTotalActedAdultInvestigationGrant;
									$actedInvestigationAdultDenial = $getTotalActedAdultInvestigationDenial;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","FOR GRANT")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","FOR GRANT"));
									$getTotalActedJICLInvestigationGrant += $upon;

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","FOR DENIAL")) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","FOR DENIAL"));
									$getTotalActedJICLInvestigationDenial += $upon;

									$actedInvestigationJICLGrant = $getTotalActedJICLInvestigationGrant;
									$actedInvestigationJICLDenial = $getTotalActedJICLInvestigationDenial;

									$actedInvestigationTotalGrant = $getTotalActedAdultInvestigationGrant  + $getTotalActedJICLInvestigationGrant;
									$actedInvestigationTotalDenial = $getTotalActedAdultInvestigationDenial  + $getTotalActedJICLInvestigationDenial;
									

									$payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"PI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"RPI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"TPI","",1));
									$getTotalActedAdultInvestigationManifest += $upon;

									$payload->field_office = $value1->NAME;
									$upon =  json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JPI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JRPI","",1)) + json_decode($this->Cmis_F5PCS_model->getTotalReferralsActedReport($payload,"JTPI","",1));
									$getTotalActedJICLInvestigationManifest += $upon;

									$actedInvestigationAdultManifest = $getTotalActedAdultInvestigationManifest;
									$actedInvestigationJICLManifest = $getTotalActedJICLInvestigationManifest;
									$actedInvestigationTotalManifest = $getTotalActedAdultInvestigationManifest + $getTotalActedJICLInvestigationManifest;
									$totalInvRefref = $actedInvestigationTotalGrant +
												$actedInvestigationTotalDenial +
												$actedInvestigationTotalTransfer +
												$actedInvestigationTotalManifest 
												;
									$totalN = $actedInvestigationTotalGrant + $actedInvestigationTotalDenial + $actedInvestigationTotalManifest;
								};
							}
									$data[] = array('REGION' => $value->VALUE_,
													'totalHandled' => $totalHandled,
													'totalN' => $totalN,

													// 'actedInvestigationTotalTransfer' => $actedInvestigationTotalTransfer,
													// 'totalInvRefref' => $totalInvRefref,
													// 'notactedTotalInvestigationRecalled' => $notactedTotalInvestigationRecalled,
													// 'notactedTotalInvestigationWarrant' => $notactedTotalInvestigationWarrant,
													// 'totalRef' => $totalRef,

												);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'quarterly_f8':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							
							if($fieldOffice->status == 'SUCCESS'){
								
								$totalCarryOverInvestigation = 0;
								$totalRcvTotalInvestigation = 0;
								$totalCasesHandled = 0;
								$totalRefParoleGrantActed = 0;
								$totalRefParoleDeniedActed = 0;
								$totalRefCommGrantActed = 0;
								$totalRefCommDeniedActed = 0;
								$totalRefCondGrantActed = 0;
								$totalRefCondDeniedActed = 0;
								$totalRefAbsGrantActed = 0;
								$totalRefAbsDeniedActed = 0;
								$totalPPIR = 0;
								$totalTransferredSubmitted = 0;
								$totalRefOthers = 0;
								$totalInvRef = 0;
								$disRate = 0;
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverInvestigation += $upon->count;

									$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcvTotalInvestigation += $upon->count;
									
									$totalCasesHandled = $totalCarryOverInvestigation + $totalRcvTotalInvestigation;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefParoleGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefParoleDeniedActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCommGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCommDeniedActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCondGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefCondDeniedActed += $upon->count;
									
									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefAbsGrantActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Denial"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefAbsDeniedActed += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalTransferredSubmitted += $upon->count;

									$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Other"; $payload->additional_operator = "="; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRefOthers += $upon->count;

									$totalPPIR = $totalRefParoleGrantActed +
										$totalRefParoleDeniedActed +
										$totalRefCommGrantActed +
										$totalRefCommDeniedActed +
										$totalRefCondGrantActed +
										$totalRefCondDeniedActed +
										$totalRefAbsGrantActed +
										$totalRefAbsDeniedActed ;
									
									$totalInvRef = $totalPPIR + $totalTransferredSubmitted + $totalRefOthers;

								}

									$data[] = array('REGION' => $value->VALUE_,
													'totalCasesHandled'=> $totalCasesHandled,
													'totalPPIR'=> $totalPPIR,
													'totalInvRef'=> $totalInvRef,										);
							}
							
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;	
				case 'quarterly_f9':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							$totalCarryOver = 0;
							$totalRcv = 0;
							$totalInvestigation = 0;
							$totalCmpltd = 0;
							$totalActive = 0;
							if($fieldOffice->status == 'SUCCESS'){

								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->filter = ""; $payload->table = "F5T5"; $payload->field_office = $value1->NAME;
									$carryOver = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCarryOver += $carryOver->count;


									$payload->filter = ""; $payload->table = "F5T6_RCV"; $payload->field_office = $value1->NAME;
									$rcv = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalRcv += $rcv->count;

									$totalInvestigation = $totalCarryOver + $totalRcv;

									$payload->filter = ""; $payload->table = "F5T6_CMPLTD"; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F5PCS_model->callProcedure1($payload));
									$totalCmpltd += $upon->count;
									$totalActive = $totalInvestigation - $totalCmpltd;
								};
							}
							

							

							$data[] = array('REGION' => $value->VALUE_,
											'ID' => $value->ID,
											'carryOver' => $totalCarryOver,
											'totalRcv' => $totalRcv,
											'totalInvestigation' => $totalInvestigation,
											'totalCmpltd' => $totalCmpltd,
											'totalActive' => $totalActive,

										);

							#var_dump();
							#break;
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;

				case 'quarterly_f10':
						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){

							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));

							if($fieldOffice->status == 'SUCCESS'){
								$totalCarryOverCI = 0;
								// (B)
								$totalRcvCI = 0;
								$totalCountCI = 0;
								$totalCmpltdCI = 0;
								$totalCountActiveCI = 0;
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									$payload->table = "F21T5"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOverCI += $upon->count;

									$payload->table = "F21T6_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcvCI += $upon->count;

									$totalCountCI = $totalCarryOverCI + $totalRcvCI;		

									$payload->table = "F21T6_CMPLTD"; $payload->filter_field = ""; $payload->filter_val = ""; 
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCmpltdCI += $upon->count;

									$totalCountActiveCI = $totalCountCI - $totalCmpltdCI;		

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOverCI'=> $totalCarryOverCI,
													'totalRcvCI'=> $totalRcvCI,
													'totalCountCI'=> $totalCountCI,
													'totalCmpltdCI'=> $totalCmpltdCI,
													'totalCountActiveCI'=> $totalCountActiveCI,
										);

							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'quarterly_f11':

						//var_dump($payload);
						$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
						#var_dump($array);
						$data = array();
						foreach ($array['payload'] as $key => $value){
							$payload->REGION = $value->ID;
							$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
							// (A)
							
							$totalCarryOver = 0;
							// (B)
							$totalRcv = 0;
							// (C)
							$totalCasesHandled = 0;
							// (D)
							$totalCompltd = 0;
							// (E)
							$totalCourtesy = 0;

							if($fieldOffice->status == 'SUCCESS'){
								
								
								foreach ($fieldOffice->payload as $key1 => $value1){

									// (A) CARRY OVER
									$payload->table = "F5T12"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCarryOver += $upon->count;

									
									// (B) COURTESY REFERRALS RECEIVED	
									$payload->table = "F5T13_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalRcv += $upon->count;

									//( C)
									$totalCasesHandled = $totalCarryOver + $totalRcv;

									// (D) DISPOSED
									$payload->table = "F5T13_TERM"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
									$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
									$totalCompltd += $upon->count;
									
									// (E)
									$totalCourtesy = $totalCasesHandled - $totalCompltd;

								}
									$data[] = array('REGION' => $value->VALUE_,
													'totalCarryOver'=> $totalCarryOver,
													'totalRcv'=> $totalRcv,
													'totalCasesHandled'=> $totalCasesHandled,
													'totalCompltd'=> $totalCompltd,
													'totalCourtesy'=> $totalCourtesy,
										);
							}
						}
						$response = array('status' => 'SUCCESS',
								  'message' => 'SUCCESS',
								  'payload' => $data);
						echo json_encode($response);
					break;
				case 'quarterly_f12':

						//var_dump($payload);
					$array = (array)json_decode($this->Pis_model->fetchAllRegion2());
					#var_dump($array);
					$data = array();
					foreach ($array['payload'] as $key => $value){
						$payload->REGION = $value->ID;
						$fieldOffice = json_decode($this->Pis_model->fetchFieldOfficeByRegion($payload));
						
						if($fieldOffice->status == 'SUCCESS'){
							
							$totalCarryOverSupv = 0;
							$totalRcvSupv = 0;
							$totalCourtesySupvParol = 0;
							$totalCourtesySupvPardon = 0;
							$totalCourtesySupvTotal = 0;
							$totalTermSupv = 0;
							$totalActCourtesySupvParol = 0;
							$totalActCourtesySupvPardon = 0;
							$totalActCourtesySupv = 0;

							$totalCarryOverSupvParol = 0;
							$totalCarryOverSupvPardon = 0;
							$totalRcvSupvParol = 0;
							$totalRcvSupvPardon = 0;

							$totalTermSupvParol = 0;
							$totalTermSupvPardon = 0;

							foreach ($fieldOffice->payload as $key1 => $value1){
								
								$payload->table = "F21T14_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverSupvParol += $upon->count;

								$payload->table = "F21T14_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalCarryOverSupvPardon += $upon->count;
								$totalCarryOverSupv = $totalCarryOverSupvParol + $totalCarryOverSupvPardon;

								$payload->table = "F21T15_RCV_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME;
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRcvSupvParol += $upon->count;

								$payload->table = "F21T15_RCV_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalRcvSupvPardon += $upon->count;
								$totalRcvSupv = $totalRcvSupvParol + $totalRcvSupvPardon;	

								$totalCourtesySupvParol  = $totalCarryOverSupvParol  + $totalRcvSupvParol;		
								$totalCourtesySupvPardon = $totalCarryOverSupvPardon + $totalRcvSupvPardon;		
								$totalCourtesySupvTotal  = $totalCourtesySupvParol   + $totalCourtesySupvPardon;		

								$payload->table = "F21T15_TERM_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTermSupvParol += $upon->count;

								$payload->table = "F21T15_TERM_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; $payload->field_office = $value1->NAME; 
								$upon = json_decode($this->Cmis_F21PCS_model->callProcedure1($payload));
								$totalTermSupvPardon += $upon->count;
								$totalTermSupv = $totalTermSupvParol + $totalTermSupvPardon;

								$totalActCourtesySupvParol = $totalCourtesySupvParol - $totalTermSupvParol;		
								$totalActCourtesySupvPardon = $totalCourtesySupvPardon - $totalTermSupvPardon;		
								$totalActCourtesySupv = $totalActCourtesySupvParol + $totalActCourtesySupvPardon;		
								
							}
								$data[] = array('REGION' => $value->VALUE_,
												'totalCarryOverSupvParol'=> $totalCarryOverSupvParol,
												'totalCarryOverSupvPardon'=> $totalCarryOverSupvPardon,
												'totalCarryOverSupv'=> $totalCarryOverSupv,
												'totalRcvSupvParol'=> $totalRcvSupvParol,
												'totalRcvSupvPardon'=> $totalRcvSupvPardon,
												'totalRcvSupv'=> $totalRcvSupv,
												'totalCourtesySupvParol'=> $totalCourtesySupvParol,
												'totalCourtesySupvPardon'=> $totalCourtesySupvPardon,
												'totalCourtesySupvTotal'=> $totalCourtesySupvTotal,
												'totalTermSupvParol'=> $totalTermSupvParol,
												'totalTermSupvPardon'=> $totalTermSupvPardon,
												'totalTermSupv'=> $totalTermSupv,
												'totalActCourtesySupvParol'=> $totalActCourtesySupvParol,
												'totalActCourtesySupvPardon'=> $totalActCourtesySupvPardon,
												'totalActCourtesySupv'=> $totalActCourtesySupv,

									);

						}
					}
					$response = array('status' => 'SUCCESS',
							  'message' => 'SUCCESS',
							  'payload' => $data);
					echo json_encode($response);
				break;	
			}
		}
	}


}