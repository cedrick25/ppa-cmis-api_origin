<?php 
	
class Cmis_F21PCS_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function getSummary($payload){
		
		$payload->table = "F21T1"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverInvestigation = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalRcvTotalInvestigation = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T2_RCV"; $payload->filter_field = "prison_type"; $payload->filter_val = "Prison"; 
		$totalRcvPrisonInvestigation = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T2_RCV"; $payload->filter_field = "prison_type"; $payload->filter_val = "Jail"; 
		$totalRcvJailInvestigation = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T2_RCV"; $payload->filter_field = "prison_type"; $payload->filter_val = "Penal"; 
		$totalRcvPenalInvestigation = json_decode($this->callProcedure1($payload));

		$totalCasesHandled = (object)array("count"=> ($totalCarryOverInvestigation->count + $totalRcvTotalInvestigation->count));

		$payload->table = "F21T2_ACTED"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalRefActed = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = null; $payload->additional_operator = "!="; 
		$totalReportSubmitted = json_decode($this->callProcedure2($payload));

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Granted"; 
		$totalRefParoleGrantActed = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Parole - For Denial"; 
		$totalRefParoleDeniedActed = json_decode($this->callProcedure1($payload));

		$totalRefParoleActed = (object)array("count"=> ($totalRefParoleGrantActed->count + $totalRefParoleDeniedActed->count));

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Granted"; 
		$totalRefCommGrantActed = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Commutation - For Denial"; 
		$totalRefCommDeniedActed = json_decode($this->callProcedure1($payload));

		$totalRefCommActed = (object)array("count"=> ($totalRefCommGrantActed->count + $totalRefCommDeniedActed->count));		

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Granted"; 
		$totalRefCondGrantActed = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Conditional Pardon - For Denial"; 
		$totalRefCondDeniedActed = json_decode($this->callProcedure1($payload));

		$totalRefCondActed = (object)array("count"=> ($totalRefCondGrantActed->count + $totalRefCondDeniedActed->count));		

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Granted"; 
		$totalRefAbsGrantActed = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Absolute Pardon - For Denial"; 
		$totalRefAbsDeniedActed = json_decode($this->callProcedure1($payload));

		$totalRefAbsActed = (object)array("count"=> ($totalRefAbsGrantActed->count + $totalRefAbsDeniedActed->count));		


		$payload->table = "F21T2_ACTED"; $payload->filter_field = "ppo_recommendation"; $payload->filter_val = "Other"; $payload->additional_operator = "="; 
		$totalRefOthers = json_decode($this->callProcedure2($payload));

		$payload->table = "F21T2_ACTED"; $payload->filter_field = "transfer_date"; $payload->filter_val = "20"; 
		$totalTransferredSubmitted = json_decode($this->callProcedure1($payload));

		$totalActivePreParole = (object)array("count"=> ($totalCasesHandled->count - $totalRefActed->count));		


		$payload->table = "F21T3"; $payload->filter_field = ""; $payload->filter_val = "Pardon - For Denial"; 
		$totalCarryOverPendingResolution = json_decode($this->callProcedure1($payload));

		$totalCasestobeActed = (object)array("count"=> ($totalCarryOverPendingResolution->count + $totalReportSubmitted->count));		


		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "PAROLE - Granted"; 
		$totalCasesResolvedGrantedParole = json_decode($this->callProcedure1($payload));
		
		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "COMMUTATION - Granted"; 
		$totalCasesResolvedGrantedComm = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "CONDITIONAL - Granted"; 
		$totalCasesResolvedGrantedCond = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ABSOLUTE - Granted"; 
		$totalCasesResolvedGrantedAbs = json_decode($this->callProcedure1($payload));

		$totalCasesResolvedGranted = (object)array("count"=> ($totalCasesResolvedGrantedParole->count + $totalCasesResolvedGrantedComm->count + $totalCasesResolvedGrantedCond->count + $totalCasesResolvedGrantedAbs->count));		


		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "PAROLE - Denial"; 
		$totalCasesResolvedDeniedParole = json_decode($this->callProcedure1($payload));
		
		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "COMMUTATION - Denial"; 
		$totalCasesResolvedDeniedComm = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "CONDITIONAL - Denial"; 
		$totalCasesResolvedDeniedCond = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ABSOLUTE - Denial"; 
		$totalCasesResolvedDeniedAbs = json_decode($this->callProcedure1($payload));

		$totalCasesResolvedDenied = (object)array("count"=> ($totalCasesResolvedDeniedParole->count + $totalCasesResolvedDeniedComm->count + $totalCasesResolvedDeniedCond->count + $totalCasesResolvedDeniedAbs->count));		


		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "PAROLE - Cancelled"; 
		$totalCasesResolvedCancelledParole = json_decode($this->callProcedure1($payload));
		
		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "COMMUTATION - Cancelled"; 
		$totalCasesResolvedCancelledComm = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "CONDITIONAL - Cancelled"; 
		$totalCasesResolvedCancelledCond = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ABSOLUTE - Cancelled"; 
		$totalCasesResolvedCancelledAbs = json_decode($this->callProcedure1($payload));

		$totalCasesResolvedCancelled = (object)array("count"=> ($totalCasesResolvedCancelledParole->count + $totalCasesResolvedCancelledComm->count + $totalCasesResolvedCancelledCond->count));		

		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Died"; 
		$totalCasesResolvedDied = json_decode($this->callProcedure1($payload));


		$payload->table = "F21T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Other"; 
		$totalCasesResolvedOther = json_decode($this->callProcedure1($payload));

		$totalCasesResolvedCount = (object)array("count"=> ($totalCasesResolvedGranted->count + $totalCasesResolvedDenied->count + $totalCasesResolvedCancelled->count + $totalCasesResolvedDied->count+ $totalCasesResolvedOther->count));		

		$totalCasesPendingResolution = (object)array("count"=> ($totalCasestobeActed->count - $totalCasesResolvedCount->count));		


		$payload->table = "F21T5"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverCI = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T6_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalRcvCI = json_decode($this->callProcedure1($payload));

		$totalCountCI = (object)array("count"=> ($totalCarryOverCI->count + $totalRcvCI->count ));		

		$payload->table = "F21T6_CMPLTD"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCmpltdCI = json_decode($this->callProcedure1($payload));

		$totalCountActiveCI = (object)array("count"=> ($totalCountCI->count - $totalCmpltdCI->count ));		


		$payload->table = "F21T7_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverSupvParolIV = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T7_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverSupvPardonIV = json_decode($this->callProcedure1($payload));

		$totalCarryOverSupvIV = (object)array("count"=> ($totalCarryOverSupvParolIV->count + $totalCarryOverSupvPardonIV->count ));		


		$payload->table = "F21T8_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalRefRcvSupvParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T8_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalRefRcvSupvPardon = json_decode($this->callProcedure1($payload));

		$totalRefRcvSupv = (object)array("count"=> ($totalRefRcvSupvParol->count + $totalRefRcvSupvPardon->count ));		


		$totalSupvCasesHandledParol = (object)array("count"=> ($totalCarryOverSupvParolIV->count + $totalRefRcvSupvParol->count ));		
		$totalSupvCasesHandledPardon = (object)array("count"=> ($totalCarryOverSupvPardonIV->count + $totalRefRcvSupvPardon->count ));		
		$totalSupvCasesHandled = (object)array("count"=> ($totalSupvCasesHandledParol->count + $totalSupvCasesHandledPardon->count ));		

		//V-D1
		$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL"; 
		$totalCasesResolvedFinalParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "FINAL"; 
		$totalCasesResolvedFinalPardon = json_decode($this->callProcedure1($payload));
		$totalCasesResolvedFinal = (object)array("count"=> ($totalCasesResolvedFinalParol->count + $totalCasesResolvedFinalPardon->count ));		

		$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST"; 
		$totalCasesResolvedArrestParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "ARREST"; 
		$totalCasesResolvedArrestPardon = json_decode($this->callProcedure1($payload));
		$totalCasesResolvedArrest = (object)array("count"=> ($totalCasesResolvedArrestParol->count + $totalCasesResolvedArrestPardon->count ));		

		$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
		$totalCasesResolvedDeathParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
		$totalCasesResolvedDeathPardon = json_decode($this->callProcedure1($payload));
		$totalCasesResolvedDeath = (object)array("count"=> ($totalCasesResolvedDeathParol->count + $totalCasesResolvedDeathPardon->count ));		

		$payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
		$totalCasesResolvedOtherParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
		$totalCasesResolvedOtherPardon = json_decode($this->callProcedure1($payload));
		$totalCasesResolvedPSOther = (object)array("count"=> ($totalCasesResolvedOtherParol->count + $totalCasesResolvedOtherPardon->count ));		


		$totalCasesResolvedBoard = (object)array("count"=> ( $totalCasesResolvedFinal->count + $totalCasesResolvedArrest->count + $totalCasesResolvedDeath->count + $totalCasesResolvedPSOther->count));

		/*New 2021-01-16*/
		$payload->filter = Null; $payload->table = "F21T11_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
		$totalCasesResolvedPSOtherParolData = json_decode($this->callProcedure1AA($payload));

		$payload->filter = Null; $payload->table = "F21T11_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
		$totalCasesResolvedPSOtherPardonData = json_decode($this->callProcedure1AA($payload));
		/*New 2021-01-16*/


		//VI-D1
		$payload->table = "F21T13_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCasesResolvedRegionalParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T13_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCasesResolvedRegionalPardon = json_decode($this->callProcedure1($payload));
		$totalCasesResolvedRegional = (object)array("count"=> ($totalCasesResolvedRegionalParol->count + $totalCasesResolvedRegionalPardon->count ));		

		$totalSupervisionCasesDropped = (object)array("count"=> ($totalCasesResolvedFinal->count +  $totalCasesResolvedArrest->count +  $totalCasesResolvedDeath->count +  $totalCasesResolvedRegional->count + $totalCasesResolvedPSOther->count));		

// 		SUMMARY
// INFRACTION
// DEATH
		$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; 
		$SupvActedSummaryParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "SUMMARY"; 
		$SupvActedSummaryPardon = json_decode($this->callProcedure1($payload));
		$SupvActedSummaryTotal = (object)array("count"=> ($SupvActedSummaryParol->count + $SupvActedSummaryPardon->count ));		

		$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
		$SupvActedInfraParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "INFRACTION"; 
		$SupvActedInfraPardon = json_decode($this->callProcedure1($payload));
		$SupvActedInfraTotal = (object)array("count"=> ($SupvActedInfraParol->count + $SupvActedInfraPardon->count ));		

		$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
		$SupvActedDeathParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "DEATH"; 
		$SupvActedDeathPardon = json_decode($this->callProcedure1($payload));
		$SupvActedDeathTotal = (object)array("count"=> ($SupvActedDeathParol->count + $SupvActedDeathPardon->count ));		

		/*New 2021-01-16 */
		$payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
		$SupvActedOtherParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
		$SupvActedOtherPardon = json_decode($this->callProcedure1($payload));
		$SupvActedOtherTotal = (object)array("count"=> ($SupvActedOtherParol->count + $SupvActedOtherPardon->count ));		


		$payload->filter = Null; $payload->table = "F21T9_PAROL"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
		$SupvActedOtherParolData = json_decode($this->callProcedure1AA($payload));

		$payload->filter = Null; $payload->table = "F21T9_PARDON"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "OTHERS"; 
		$SupvActedOtherPardonData = json_decode($this->callProcedure1AA($payload));

		/*New 2021-01-16 */


		$totalReportSubmittedtoBoard = (object)array("count"=> ( $SupvActedSummaryTotal->count + $SupvActedInfraTotal->count + $SupvActedDeathTotal->count+ $SupvActedOtherTotal->count));

		$payload->table = "F21T9_PAROL"; $payload->filter_field = "transfer"; $payload->filter_val = "20"; 
		$totalReportSubmittedRegionalParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T9_PARDON"; $payload->filter_field = "transfer"; $payload->filter_val = "20"; 
		$totalReportSubmittedRegionalPardon = json_decode($this->callProcedure1($payload));
		$totalReportSubmittedRegional = (object)array("count"=> ( $totalReportSubmittedRegionalParol->count + $totalReportSubmittedRegionalPardon->count));

		$totalSupervisionCasesActed =  (object)array("count"=> ( $totalReportSubmittedtoBoard->count + $totalReportSubmittedRegional->count ));
		$totalActiveSupervisionCasesParole =  (object)array("count"=> ( $totalSupvCasesHandledParol->count - $totalCasesResolvedFinalParol->count - $totalCasesResolvedArrestParol->count - $totalCasesResolvedDeathParol->count - $totalCasesResolvedRegionalParol->count - $totalCasesResolvedOtherParol->count  ));
		$totalActiveSupervisionCasesPardon =  (object)array("count"=> ( $totalSupvCasesHandledPardon->count - $totalCasesResolvedFinalPardon->count - $totalCasesResolvedArrestPardon->count - $totalCasesResolvedDeathPardon->count - $totalCasesResolvedRegionalPardon->count    - $totalCasesResolvedOtherPardon->count));
		$totalActiveSupervisionCases =  (object)array("count"=> ( $totalActiveSupervisionCasesParole->count + $totalActiveSupervisionCasesPardon->count ));


		//V.A.
		$payload->table = "F21T10_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverPendingParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T10_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverPendingPardon = json_decode($this->callProcedure1($payload));
		$totalCarryOverPending = (object)array("count"=> ($totalCarryOverPendingParol->count + $totalCarryOverPendingPardon->count ));		

		$totalCasesToActedByBoardParol = (object)array("count"=> ($totalCarryOverPendingParol->count + $SupvActedSummaryParol->count +$SupvActedInfraParol->count +  $SupvActedDeathParol->count + $SupvActedOtherParol->count ));		

		$totalCasesToActedByBoardPardon = (object)array("count"=> ($totalCarryOverPendingPardon->count + $SupvActedSummaryPardon->count + $SupvActedInfraPardon->count + $SupvActedDeathPardon->count +$SupvActedDeathPardon->count));		

		$totalCasesToActedByBoard = (object)array("count"=> ($totalCasesToActedByBoardParol->count + $totalCasesToActedByBoardPardon->count + $SupvActedDeathPardon->count ));		

		$totalCasesPendingResolutionBoardParol = (object)array("count"=> ($totalCasesToActedByBoardParol->count - $totalCasesResolvedFinalParol->count - $totalCasesResolvedArrestParol->count - $totalCasesResolvedDeathParol->count - $totalCasesResolvedOtherParol->count ));		
		$totalCasesPendingResolutionBoardPardon = (object)array("count"=> ($totalCasesToActedByBoardPardon->count - $totalCasesResolvedFinalPardon->count - $totalCasesResolvedArrestPardon->count - $totalCasesResolvedDeathPardon->count - $totalCasesResolvedOtherPardon->count  ));		
		$totalCasesPendingResolutionBoard = (object)array("count"=> ($totalCasesPendingResolutionBoardParol->count + $totalCasesPendingResolutionBoardPardon->count));		


		$payload->table = "F21T12_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverPendingRegionalParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T12_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverPendingRegionalPardon = json_decode($this->callProcedure1($payload));
		$totalCarryOverPendingRegional = (object)array("count"=> ($totalCarryOverPendingRegionalParol->count + $totalCarryOverPendingRegionalPardon->count ));		

		$totalCasesToActedByRegionalPardon = (object)array("count"=> ($totalCarryOverPendingRegionalParol->count + $totalReportSubmittedRegionalParol->count ));		
		$totalCasesToActedByRegionalParol = (object)array("count"=> ($totalCarryOverPendingRegionalPardon->count + $totalReportSubmittedRegionalPardon->count ));		
		$totalCasesToActedByRegional = (object)array("count"=> ($totalCasesToActedByRegionalPardon->count + $totalCasesToActedByRegionalParol->count ));		

		$totalCasesPendingResolutionRegionalParol = (object)array("count"=> ($totalCarryOverPendingRegionalParol->count - $totalCasesResolvedRegionalParol->count   ));		
		$totalCasesPendingResolutionRegionalPardon = (object)array("count"=> ($totalCarryOverPendingRegionalPardon->count - $totalCasesResolvedRegionalPardon->count));		
		$totalCasesPendingResolutionRegional = (object)array("count"=> ($totalCasesPendingResolutionRegionalParol->count + $totalCasesPendingResolutionRegionalPardon->count));		


		$payload->table = "F21T14_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverSupvParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T14_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalCarryOverSupvPardon = json_decode($this->callProcedure1($payload));
		$totalCarryOverSupv = (object)array("count"=> ($totalCarryOverSupvParol->count + $totalCarryOverSupvPardon->count ));		


		$payload->table = "F21T15_RCV_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalRcvSupvParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T15_RCV_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalRcvSupvPardon = json_decode($this->callProcedure1($payload));
		$totalRcvSupv = (object)array("count"=> ($totalRcvSupvParol->count + $totalRcvSupvPardon->count ));		

		$totalCourtesySupvParol = (object)array("count"=> ($totalCarryOverSupvParol->count + $totalRcvSupvParol->count   ));		
		$totalCourtesySupvPardon = (object)array("count"=> ($totalCarryOverSupvPardon->count + $totalRcvSupvPardon->count));		
		$totalCourtesySupvTotal = (object)array("count"=> ($totalCourtesySupvParol->count + $totalCourtesySupvPardon->count));		


		$payload->table = "F21T15_TERM_PAROL"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalTermSupvParol = json_decode($this->callProcedure1($payload));

		$payload->table = "F21T15_TERM_PARDON"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$totalTermSupvPardon = json_decode($this->callProcedure1($payload));
		$totalTermSupv = (object)array("count"=> ($totalTermSupvParol->count + $totalTermSupvPardon->count ));		
		

		$totalActCourtesySupvParol = (object)array("count"=> ($totalCourtesySupvParol->count - $totalTermSupvParol->count ));		
		$totalActCourtesySupvPardon = (object)array("count"=> ($totalCourtesySupvPardon->count - $totalTermSupvPardon->count ));		
		$totalActCourtesySupv = (object)array("count"=> ($totalActCourtesySupvParol->count + $totalActCourtesySupvPardon->count ));		



		$payload = array(
			"totalCarryOverInvestigation"=>$totalCarryOverInvestigation,
			"totalRcvTotalInvestigation"=>$totalRcvTotalInvestigation,
			"totalRcvPrisonInvestigation"=>$totalRcvPrisonInvestigation,
			"totalRcvJailInvestigation"=>$totalRcvJailInvestigation,
			"totalRcvPenalInvestigation"=>$totalRcvPenalInvestigation,
			"totalCasesHandled"=>$totalCasesHandled,
			"totalRefActed"=>$totalRefActed,
			"totalReportSubmitted"=>$totalReportSubmitted,
			"totalRefParoleActed"=>$totalRefParoleActed,
			"totalRefParoleGrantActed"=>$totalRefParoleGrantActed,
			"totalRefParoleDeniedActed"=>$totalRefParoleDeniedActed,
			"totalRefCommActed"=>$totalRefCommActed,
			"totalRefCommGrantActed"=>$totalRefCommGrantActed,
			"totalRefCommDeniedActed"=>$totalRefCommDeniedActed,
			"totalRefCondActed"=>$totalRefCondActed,
			"totalRefCondGrantActed"=>$totalRefCondGrantActed,
			"totalRefCondDeniedActed"=>$totalRefCondDeniedActed,
			"totalRefAbsActed"=>$totalRefAbsActed,
			"totalRefAbsGrantActed"=>$totalRefAbsGrantActed,
			"totalRefAbsDeniedActed"=>$totalRefAbsDeniedActed,
			"totalRefOthers"=>$totalRefOthers,
			"totalTransferredSubmitted"=>$totalTransferredSubmitted,
			"totalActivePreParole"=>$totalActivePreParole,
			"totalCarryOverPendingResolution"=>$totalCarryOverPendingResolution,
			"totalCasestobeActed"=>$totalCasestobeActed,
			"totalCasesResolvedGranted"=>$totalCasesResolvedGranted,
			"totalCasesResolvedGrantedParole"=>$totalCasesResolvedGrantedParole,
			"totalCasesResolvedGrantedComm"=>$totalCasesResolvedGrantedComm,
			"totalCasesResolvedGrantedCond"=>$totalCasesResolvedGrantedCond,
			"totalCasesResolvedGrantedAbs"=>$totalCasesResolvedGrantedAbs,
			"totalCasesResolvedDenied"=>$totalCasesResolvedDenied,
			"totalCasesResolvedDeniedParole"=>$totalCasesResolvedDeniedParole,
			"totalCasesResolvedDeniedComm"=>$totalCasesResolvedDeniedComm,
			"totalCasesResolvedDeniedCond"=>$totalCasesResolvedDeniedCond,
			"totalCasesResolvedDeniedAbs"=>$totalCasesResolvedDeniedAbs,
			"totalCasesResolvedCancelled"=>$totalCasesResolvedCancelled,
			"totalCasesResolvedCancelledParole"=>$totalCasesResolvedCancelledParole,
			"totalCasesResolvedCancelledComm"=>$totalCasesResolvedCancelledComm,
			"totalCasesResolvedCancelledCond"=>$totalCasesResolvedCancelledCond,
			"totalCasesResolvedCancelledAbs"=>$totalCasesResolvedCancelledAbs,
			"totalCasesResolvedDied"=>$totalCasesResolvedDied,
			"totalCasesResolvedOther"=>$totalCasesResolvedOther,
			"totalCasesResolvedCount"=>$totalCasesResolvedCount,
			"totalCasesPendingResolution"=>$totalCasesPendingResolution,
			"totalCarryOverCI"=>$totalCarryOverCI,
			"totalRcvCI"=>$totalRcvCI,
			"totalCountCI"=>$totalCountCI,
			"totalCmpltdCI"=>$totalCmpltdCI,
			"totalCountActiveCI"=>$totalCountActiveCI,
			"totalCarryOverSupvParol"=>$totalCarryOverSupvParol,
			"totalCarryOverSupvPardon"=>$totalCarryOverSupvPardon,
			"totalCarryOverSupv"=>$totalCarryOverSupv,
			"totalRefRcvSupv"=>$totalRefRcvSupv,
			"totalRefRcvSupvParol"=>$totalRefRcvSupvParol,
			"totalRefRcvSupvPardon"=>$totalRefRcvSupvPardon,
			"totalSupvCasesHandled"=>$totalSupvCasesHandled,
			"totalSupvCasesHandledParol"=>$totalSupvCasesHandledParol,
			"totalSupvCasesHandledPardon"=>$totalSupvCasesHandledPardon,
			"totalCasesResolvedFinalParol"=>$totalCasesResolvedFinalParol,
			"totalCasesResolvedFinalPardon"=>$totalCasesResolvedFinalPardon,
			"totalCasesResolvedFinal"=>$totalCasesResolvedFinal,
			"totalCasesResolvedArrestParol"=>$totalCasesResolvedArrestParol,
			"totalCasesResolvedArrestPardon"=>$totalCasesResolvedArrestPardon,
			"totalCasesResolvedArrest"=>$totalCasesResolvedArrest,
			"totalCasesResolvedDeathParol"=>$totalCasesResolvedDeathParol,
			"totalCasesResolvedDeathPardon"=>$totalCasesResolvedDeathPardon,
			"totalCasesResolvedDeath"=>$totalCasesResolvedDeath,
			"totalCasesResolvedPSOther"=>$totalCasesResolvedPSOther,
			"totalCasesResolvedRegionalParol"=>$totalCasesResolvedRegionalParol,
			"totalCasesResolvedRegionalPardon"=>$totalCasesResolvedRegionalPardon,
			"totalCasesResolvedRegional"=>$totalCasesResolvedRegional,
			"totalSupervisionCasesDropped"=>$totalSupervisionCasesDropped,
			"SupvActedSummaryParol"=>$SupvActedSummaryParol,
			"SupvActedSummaryPardon"=>$SupvActedSummaryPardon,
			"SupvActedSummaryTotal"=>$SupvActedSummaryTotal,
			"SupvActedInfraParol"=>$SupvActedInfraParol,
			"SupvActedInfraPardon"=>$SupvActedInfraPardon,
			"SupvActedInfraTotal"=>$SupvActedInfraTotal,
			"SupvActedDeathParol"=>$SupvActedDeathParol,
			"SupvActedDeathPardon"=>$SupvActedDeathPardon,
			"SupvActedDeathTotal"=>$SupvActedDeathTotal,
			"totalReportSubmittedtoBoard"=>$totalReportSubmittedtoBoard,
			"totalReportSubmittedRegionalParol"=>$totalReportSubmittedRegionalParol,
			"totalReportSubmittedRegionalPardon"=>$totalReportSubmittedRegionalPardon,
			"totalReportSubmittedRegional"=>$totalReportSubmittedRegional,
			"totalSupervisionCasesActed"=>$totalSupervisionCasesActed,
			"totalActiveSupervisionCasesParole"=>$totalActiveSupervisionCasesParole,
			"totalActiveSupervisionCasesPardon"=>$totalActiveSupervisionCasesPardon,
			"totalActiveSupervisionCases"=>$totalActiveSupervisionCases,
			"totalCarryOverPendingParol"=>$totalCarryOverPendingParol,
			"totalCarryOverPendingPardon"=>$totalCarryOverPendingPardon,
			"totalCarryOverPending"=>$totalCarryOverPending,
			"totalCasesToActedByBoardParol"=>$totalCasesToActedByBoardParol,
			"totalCasesToActedByBoardPardon"=>$totalCasesToActedByBoardPardon,
			"totalCasesToActedByBoard"=>$totalCasesToActedByBoard,
			"totalCasesResolvedBoard"=>$totalCasesResolvedBoard,
			"totalCasesPendingResolutionBoardParol"=>$totalCasesPendingResolutionBoardParol,
			"totalCasesPendingResolutionBoardPardon"=>$totalCasesPendingResolutionBoardPardon,
			"totalCasesPendingResolutionBoard"=>$totalCasesPendingResolutionBoard,
			"totalCarryOverPendingRegionalParol"=>$totalCarryOverPendingRegionalParol,
			"totalCarryOverPendingRegionalPardon"=>$totalCarryOverPendingRegionalPardon,
			"totalCarryOverPendingRegional"=>$totalCarryOverPendingRegional,
			"totalCasesToActedByRegionalPardon"=>$totalCasesToActedByRegionalPardon,
			"totalCasesToActedByRegionalParol"=>$totalCasesToActedByRegionalParol,
			"totalCasesToActedByRegional"=>$totalCasesToActedByRegional,
			"totalCasesPendingResolutionRegionalParol"=>$totalCasesPendingResolutionRegionalParol,
			"totalCasesPendingResolutionRegionalPardon"=>$totalCasesPendingResolutionRegionalPardon,
			"totalCasesPendingResolutionRegional"=>$totalCasesPendingResolutionRegional,
			"totalCarryOverSupvParol"=>$totalCarryOverSupvParol,
			"totalCarryOverSupvPardon"=>$totalCarryOverSupvPardon,
			"totalCarryOverSupv"=>$totalCarryOverSupv,
			"totalRcvSupvParol"=>$totalRcvSupvParol,
			"totalRcvSupvPardon"=>$totalRcvSupvPardon,
			"totalRcvSupv"=>$totalRcvSupv,
			"totalCourtesySupvParol"=>$totalCourtesySupvParol,
			"totalCourtesySupvPardon"=>$totalCourtesySupvPardon,
			"totalCourtesySupvTotal"=>$totalCourtesySupvTotal,
			"totalTermSupvParol"=>$totalTermSupvParol,
			"totalTermSupvPardon"=>$totalTermSupvPardon,
			"totalTermSupv"=>$totalTermSupv,
			"totalActCourtesySupvParol"=>$totalActCourtesySupvParol,
			"totalActCourtesySupvPardon"=>$totalActCourtesySupvPardon,
			"totalActCourtesySupv"=>$totalActCourtesySupv,
			"totalCarryOverSupvParolIV"=>$totalCarryOverSupvParolIV,
			"totalCarryOverSupvPardonIV"=>$totalCarryOverSupvPardonIV,
			"totalCarryOverSupvIV"=>$totalCarryOverSupvIV,


			"SupvActedOtherParol"=>$SupvActedOtherParol,
			"SupvActedOtherPardon"=>$SupvActedOtherPardon,
			"SupvActedOtherTotal"=>$SupvActedOtherTotal,

			"SupvActedOtherParolData"=>$SupvActedOtherParolData,
			"SupvActedOtherPardonData"=>$SupvActedOtherPardonData,
			
			"totalCasesResolvedPSOtherParolData"=>$totalCasesResolvedPSOtherParolData,
			"totalCasesResolvedPSOtherPardonData"=>$totalCasesResolvedPSOtherPardonData,
			);

		$response = array(
					'status' => 'SUCCESS',
					'message' => 'SUCCESS',
					'payload' => $payload
				);

		return json_encode($response);

	}




	public function callProcedure1($payload){
		#print_r($payload);
		$field_office = "";
		$start_date = "";
		$end_date = "";
		$table = "";
		$filter_field = "";
		$filter_val = "";
		if(isset($payload->field_office)){
			$field_office = $payload->field_office;
		}
		if(isset($payload->Y_M)){
			$start_date = $payload->Y_M;
		}
		if(isset($payload->Y_M)){
			$end_date = $payload->Y_M;
		}
		if(isset($payload->END_Y_M)){
			$end_date = $payload->END_Y_M;	
		}
		if(isset($payload->table)){
			$table = $payload->table;
		}
		if(isset($payload->filter_field)){
			$filter_field = $payload->filter_field;
		}

		if(isset($payload->filter_val)){
			$filter_val = $payload->filter_val;
		}
		#print_r($payload);

	    $query = $this->db->query("CALL F21CaseloadQuery1('".$start_date."','".$end_date."','".$field_office."','".$table."','".$filter_field."','".$filter_val."')");

		if($query){
			if($query->num_rows() > 0){
				$data = $query->row();
					$response = $data;
					$query->next_result(); 
					$query->free_result(); 
				return json_encode($response);
			}else{
					$response = array('status' => 'ERROR',
						  'message' => 'Fail Retrieving Data');
				return json_encode($response);
		
			}
		}else{
				$response = array('status' => 'ERROR',
								  'message' => 'ERROR FETCHING RECORDS',
								  'error_code' => mysqli_error($this->con));
				return json_encode($response);
		}
	}
	public function carryOver($payload){
		#print_r($payload);
		$field_office = "";
		$start_date = "";
		$end_date = "";
		$table = "";
		$filter_field = "";
		$filter_val = "";
		if(isset($payload->field_office)){
			$field_office = $payload->field_office;
		}
		if(isset($payload->Y_M)){
			$start_date = $payload->Y_M;
			$end_date = $payload->Y_M;
		}
		if(isset($payload->END_Y_M)){
			$end_date = $payload->END_Y_M;	
		}
		if(isset($payload->filter)){
			$filter = $payload->filter;
		}
		if(isset($payload->table)){
			$table = $payload->table;
		}
		if(isset($payload->filter_field)){
			$filter_field = $payload->filter_field;
		}

		if(isset($payload->filter_val)){
			$filter_val = $payload->filter_val;
		}
		#print_r($payload);

	    $query = $this->db->query("CALL CarryOver('".$start_date."','".$start_date."','".$field_office."','".$filter."','".$table."','".$filter_field."','".$filter_val."')");

		if($query){
			if($query->num_rows() > 0){
				$data = $query->row();
					$response = $data;
					$query->next_result(); 
					$query->free_result(); 
				return json_encode($response);
			}else{
					$response = array('status' => 'ERROR',
						  'message' => 'Fail Retrieving Data');
				return json_encode($response);
		
			}
		}else{
				$response = array('status' => 'ERROR',
								  'message' => 'ERROR FETCHING RECORDS',
								  'error_code' => mysqli_error($this->con));
				return json_encode($response);
		}
	}
	public function callProcedure2($payload){
		#print_r($payload);
		$field_office = "";
		$start_date = "";
		$end_date = "";
		$table = "";
		$filter_field = "";
		$filter_val = "";
		$additional_operator = "";
		if(isset($payload->field_office)){
			$field_office = $payload->field_office;
		}
		if(isset($payload->Y_M)){
			$start_date = $payload->Y_M;
		}
		if(isset($payload->Y_M)){
			$end_date = $payload->Y_M;
		}
		if(isset($payload->END_Y_M)){
			$end_date = $payload->END_Y_M;	
		}
		if(isset($payload->table)){
			$table = $payload->table;
		}
		if(isset($payload->filter_field)){
			$filter_field = $payload->filter_field;
		}

		if(isset($payload->filter_val)){
			$filter_val = $payload->filter_val;
		}
		if(isset($payload->additional_operator)){
			$additional_operator = $payload->additional_operator;
		}
		#print_r($payload);

	    $query = $this->db->query("CALL F21CaseloadQuery2('".$start_date."','".$end_date."','".$field_office."','".$table."','".$filter_field."','".$filter_val."','".$additional_operator."')");

		if($query){
			if($query->num_rows() > 0){
				$data = $query->row();
					$response = $data;
					$query->next_result(); 
					$query->free_result(); 
				return json_encode($response);
			}else{
					$response = array('status' => 'ERROR',
						  'message' => 'Fail Retrieving Data');
				return json_encode($response);
		
			}
		}else{
				$response = array('status' => 'ERROR',
								  'message' => 'ERROR FETCHING RECORDS',
								  'error_code' => mysqli_error($this->con));
				return json_encode($response);
		}
	}

	

	public function callProcedure1AA($payload){
		$this->db->reconnect();
		#print_r($payload);
		$field_office = "";
		$start_date = "";
		$end_date = "";
		$table = "";
		$filter_field = "";
		$filter_val = "";
		$filter = "";
		if(isset($payload->field_office)){
			$field_office = $payload->field_office;
		}
		if(isset($payload->Y_M)){
			$start_date = $payload->Y_M;
			$end_date = $payload->Y_M;
		}
		if(isset($payload->END_Y_M)){
			$end_date = $payload->END_Y_M;	
		}
		if(isset($payload->filter)){
			$filter = $payload->filter;
		}
		if(isset($payload->table)){
			$table = $payload->table;
		}
		if(isset($payload->filter_field)){
			$filter_field = $payload->filter_field;
		}

		if(isset($payload->filter_val)){
			$filter_val = $payload->filter_val;
		}
		$query_string = "SELECT * FROM ".$table." WHERE docket_no LIKE '%".$filter."%' and Y_M >= '".$start_date."' and Y_M <= '".$end_date."' and status = 1 and field_office='".$field_office."' and ".$filter_field." LIKE '%".$filter_val."%'";
		#echo $query_string;

		$query = $this->db->query($query_string);
		if($query){
			if($query->num_rows() > 0){
				$data = $query->result();
					$response = $data;
					#$query->next_result(); 
					$query->free_result(); 
				return json_encode($response);
			}else{
					$response = array('status' => 'ERROR',
						  'message' => 'Fail Retrieving Data');
				return json_encode($response);
		
			}
		}else{
				$response = array('status' => 'ERROR',
								  'message' => 'ERROR FETCHING RECORDS',
								  'error_code' => mysqli_error($this->con));
				return json_encode($response);
		}
	}
	

}



?>