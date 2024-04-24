<?php 
	
class Cmis_F5PCS_model extends CI_Model
{


	public function __construct() {
        header('Access-Control-Allow-Origin: *');
    	header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    	parent::__construct();
	}

	public function getSummary($payload){
		//var_dump($payload);
		$responseresponse = array();

		$getTotalCarryAdultInvestigation = $this->getTotalCarryInvestigation($payload,"PI");
		$getTotalCarryAdultInvestigationRpi = $this->getTotalCarryInvestigation($payload,"RPI");
		$getTotalCarryAdultInvestigationTpi = $this->getTotalCarryInvestigation($payload,"TPI");

		$getTotalCarryJICLInvestigation = $this->getTotalCarryInvestigation($payload,"JPI");
		$getTotalCarryJICLInvestigationJRPI = $this->getTotalCarryInvestigation($payload,"JRPI");
		$getTotalCarryJICLInvestigationJTPI = $this->getTotalCarryInvestigation($payload,"JTPI");


		$response['carryOverInvestigationAdult'] = $getTotalCarryAdultInvestigation + $getTotalCarryAdultInvestigationRpi +$getTotalCarryAdultInvestigationTpi;
		$response['carryOverInvestigationJICL'] = $getTotalCarryJICLInvestigation + $getTotalCarryJICLInvestigationJRPI + $getTotalCarryJICLInvestigationJTPI;
		$response['carryOverInvestigationTotal']  = $response['carryOverInvestigationAdult'] + $response['carryOverInvestigationJICL'];


		/*
		$getTotalReceivedAdultInvestigation = $this->getTotalReferralsReceived($payload,"PI") + $this->getTotalReferralsReceived($payload,"RPI") + $this->getTotalReferralsReceived($payload,"TPI");
		$getTotalReceivedJICLInvestigation = $this->getTotalReferralsReceived($payload,"JPI") + $this->getTotalReferralsReceived($payload,"JRPI") + $this->getTotalReferralsReceived($payload,"JTPI");

		$response['rcvInvestigationJICLNew'] = $getTotalReceivedJICLInvestigation;
		$response['rcvInvestigationAdultNew'] = $getTotalReceivedAdultInvestigation;
		$response['rcvInvestigationTotalNew'] = $getTotalReceivedAdultInvestigation + $getTotalReceivedJICLInvestigation;
		*/

		// CIVIL = 1
		$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
		$rcvInvestigationJICLCivilNewJPI = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
		$rcvInvestigationJICLCivilNewJTPI = json_decode($this->callProcedure1($payload));

		$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
		$rcvInvestigationAdultCivilNewJPI = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "1"; 
		$rcvInvestigationAdultCivilNewJTPI = json_decode($this->callProcedure1($payload));


		$response['rcvInvestigationJICLCivilNew'] = (object)array("count"=> ($rcvInvestigationJICLCivilNewJPI->count + $rcvInvestigationJICLCivilNewJTPI->count));
		$response['rcvInvestigationAdultCivilNew'] = (object)array("count"=> ($rcvInvestigationAdultCivilNewJPI->count + $rcvInvestigationAdultCivilNewJTPI->count));
		$response['rcvInvestigationTotalCivilNew'] = (object)array("count"=> ($response['rcvInvestigationJICLCivilNew']->count + $response['rcvInvestigationAdultCivilNew']->count));

		// CIVIL = 0
		$payload->filter = "JPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; 
		$rcvInvestigationJICLMilNewJPI = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "JTPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; 
		$rcvInvestigationJICLMilNewJTPI = json_decode($this->callProcedure1($payload));

		$payload->filter = "PI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; 
		$rcvInvestigationAdultMilNewJPI = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "TPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = "is_civil"; $payload->filter_val = "0"; 
		$rcvInvestigationAdultMilNewJTPI = json_decode($this->callProcedure1($payload));


		$response['rcvInvestigationJICLMilNew'] = (object)array("count"=> ($rcvInvestigationJICLMilNewJPI->count + $rcvInvestigationJICLMilNewJTPI->count));
		$response['rcvInvestigationAdultMilNew'] = (object)array("count"=> ($rcvInvestigationAdultMilNewJPI->count + $rcvInvestigationAdultMilNewJTPI->count));
		#echo $response['rcvInvestigationJICLMilNew']->count;
		$response['rcvInvestigationTotalMilNew'] = (object)array("count"=> ($response['rcvInvestigationJICLMilNew']->count + $response['rcvInvestigationAdultMilNew']->count));

		//
		
		$response['rcvInvestigationJICLNew'] = (object)array("count"=> ($response['rcvInvestigationJICLCivilNew']->count + $response['rcvInvestigationJICLMilNew']->count));
		$response['rcvInvestigationAdultNew'] = (object)array("count"=> ($response['rcvInvestigationAdultCivilNew']->count + $response['rcvInvestigationAdultMilNew']->count));
		$response['rcvInvestigationTotalNew'] = (object)array("count"=> ($response['rcvInvestigationJICLNew']->count + $response['rcvInvestigationAdultNew']->count));


		$payload->filter = "JRPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['rcvInvestigationJICLRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPI"; $payload->table = "F5T2_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['rcvInvestigationAdultRPI'] = json_decode($this->callProcedure1($payload));

		$response['rcvInvestigationTotalRPI'] = (object)array("count"=> ($response['rcvInvestigationAdultRPI']->count + $response['rcvInvestigationJICLRPI']->count));


		$response['rcvInvestigationJICL'] = $response['rcvInvestigationJICLNew']->count + $response['rcvInvestigationJICLRPI']->count;
		$response['rcvInvestigationAdult'] = $response['rcvInvestigationAdultNew']->count + $response['rcvInvestigationAdultRPI']->count;
		$response['rcvInvestigationTotal'] = $response['rcvInvestigationJICL'] + $response['rcvInvestigationAdult'];



		$response['CaseHandledAdult']  =  $response['rcvInvestigationAdult'] + $response['carryOverInvestigationAdult'];
		$response['CaseHandledJICL']  = $response['rcvInvestigationJICL'] + $response['carryOverInvestigationJICL'];
		$response['CaseHandledTotal']  = $response['CaseHandledAdult'] + $response['CaseHandledJICL'];

		$getTotalActedAdultInvestigation = $this->getTotalReferralsActed($payload,"PI") +  $this->getTotalReferralsActed($payload,"RPI") +  $this->getTotalReferralsActed($payload,"TPI");
		$getTotalActedJICLInvestigation = $this->getTotalReferralsActed($payload,"JPI") + $this->getTotalReferralsActed($payload,"JRPI") + $this->getTotalReferralsActed($payload,"JTPI");

		$response['actedInvestigationAdult'] = $getTotalActedAdultInvestigation;
		$response['actedInvestigationJICL'] = $getTotalActedJICLInvestigation;
		$response['actedInvestigationTotal'] = $getTotalActedJICLInvestigation + $getTotalActedAdultInvestigation;

		$getTotalActedAdultInvestigationGrant = $this->getTotalReferralsActedReport($payload,"PI","FOR GRANT") + $this->getTotalReferralsActedReport($payload,"RPI","FOR GRANT") + $this->getTotalReferralsActedReport($payload,"TPI","FOR GRANT");
		$getTotalActedAdultInvestigationDenial = $this->getTotalReferralsActedReport($payload,"PI","FOR DENIAL") + $this->getTotalReferralsActedReport($payload,"RPI","FOR DENIAL") + $this->getTotalReferralsActedReport($payload,"TPI","FOR DENIAL");

		$response['actedInvestigationAdultGrant'] = $getTotalActedAdultInvestigationGrant;
		$response['actedInvestigationAdultDenial'] = $getTotalActedAdultInvestigationDenial;

		$getTotalActedJICLInvestigationGrant = $this->getTotalReferralsActedReport($payload,"JPI","FOR GRANT") + $this->getTotalReferralsActedReport($payload,"JRPI","FOR GRANT") + $this->getTotalReferralsActedReport($payload,"JTPI","FOR GRANT");
		$getTotalActedJICLInvestigationDenial = $this->getTotalReferralsActedReport($payload,"JPI","FOR DENIAL") + $this->getTotalReferralsActedReport($payload,"JRPI","FOR DENIAL") + $this->getTotalReferralsActedReport($payload,"JTPI","FOR DENIAL");

		$response['actedInvestigationJICLGrant'] = $getTotalActedJICLInvestigationGrant;
		$response['actedInvestigationJICLDenial'] = $getTotalActedJICLInvestigationDenial;

		$response['actedInvestigationTotalGrant'] = $getTotalActedAdultInvestigationGrant  + $getTotalActedJICLInvestigationGrant;
		$response['actedInvestigationTotalDenial'] = $getTotalActedAdultInvestigationDenial  + $getTotalActedJICLInvestigationDenial;


		$getTotalActedAdultInvestigationManifest = $this->getTotalReferralsActedReport($payload,"PI","",1) + $this->getTotalReferralsActedReport($payload,"RPI","",1) + $this->getTotalReferralsActedReport($payload,"TPI","",1);
		$getTotalActedJICLInvestigationManifest =  $this->getTotalReferralsActedReport($payload,"JPI","",1) + $this->getTotalReferralsActedReport($payload,"JRPI","",1) + $this->getTotalReferralsActedReport($payload,"JTPI","",1);

		$response['actedInvestigationAdultManifest'] = $getTotalActedAdultInvestigationManifest;
		$response['actedInvestigationJICLManifest']  = $getTotalActedJICLInvestigationManifest;
		$response['actedInvestigationTotalManifest'] = $getTotalActedAdultInvestigationManifest + $getTotalActedJICLInvestigationManifest;

		$getTotalActedAdultInvestigationTransfer = $this->getTotalReferralsActedReport($payload,"PI","",0,1) + $this->getTotalReferralsActedReport($payload,"RPI","",0,1) + $this->getTotalReferralsActedReport($payload,"TPI","",0,1);
		$getTotalActedJICLInvestigationTransfer =  $this->getTotalReferralsActedReport($payload,"JPI","",0,1) + $this->getTotalReferralsActedReport($payload,"JRPI","",0,1) + $this->getTotalReferralsActedReport($payload,"JTPI","",0,1);
		$response['actedInvestigationAdultTransfer'] = $getTotalActedAdultInvestigationTransfer;
		$response['actedInvestigationJICLTransfer']  = $getTotalActedJICLInvestigationTransfer;
		$response['actedInvestigationTotalTransfer'] = $getTotalActedAdultInvestigationTransfer + $getTotalActedJICLInvestigationTransfer;
		
		$response['actedInvestigationTotalJICL'] = $getTotalActedJICLInvestigationManifest + $getTotalActedJICLInvestigationGrant + $getTotalActedJICLInvestigationDenial;
		$response['actedInvestigationTotalADULT'] = $getTotalActedAdultInvestigationManifest + $getTotalActedAdultInvestigationGrant + $getTotalActedAdultInvestigationDenial;
		$response['actedInvestigationTotalTOTAL'] = $response['actedInvestigationTotalJICL'] +  $response['actedInvestigationTotalADULT'];

		$response['actedInvestigationTotalJICLSubmitted'] = $getTotalActedJICLInvestigationGrant + $getTotalActedJICLInvestigationDenial;
		$response['actedInvestigationTotalADULTSubmitted'] = $getTotalActedAdultInvestigationGrant + $getTotalActedAdultInvestigationDenial;
		$response['actedInvestigationTotalTOTALSubmitted'] = $response['actedInvestigationTotalJICLSubmitted'] +  $response['actedInvestigationTotalADULTSubmitted'];



		$response['actedInvestigationTotalReportJICL'] = $response['actedInvestigationTotalJICLSubmitted'] + $response['actedInvestigationJICLManifest'];
		$response['actedInvestigationTotalReportADULT'] = $response['actedInvestigationTotalADULTSubmitted'] + $response['actedInvestigationAdultManifest'];
		$response['actedInvestigationTotalReportTOTAL'] = $response['actedInvestigationTotalReportJICL'] +  $response['actedInvestigationTotalReportADULT'];



		$getTotalNotActedAdultInvestigationRecalled =  $this->getTotalReferralsNotActedReport($payload,"PI","Recall") + $this->getTotalReferralsNotActedReport($payload,"RPI","Recall") + $this->getTotalReferralsNotActedReport($payload,"TPI","Recall");
		$getTotalNotActedJICLInvestigationRecalled = $this->getTotalReferralsNotActedReport($payload,"JPI","Recall") + $this->getTotalReferralsNotActedReport($payload,"JRPI","Recall") + $this->getTotalReferralsNotActedReport($payload,"JTPI","Recall");
		$getTotalNotActedTotalInvestigationRecalled = $getTotalNotActedAdultInvestigationRecalled + $getTotalNotActedJICLInvestigationRecalled;


		$response['notactedAdultInvestigationRecalled'] = $getTotalNotActedAdultInvestigationRecalled;
		$response['notactedJICLInvestigationRecalled'] = $getTotalNotActedJICLInvestigationRecalled;
		$response['notactedTotalInvestigationRecalled'] = $getTotalNotActedTotalInvestigationRecalled;


		$getTotalNotActedAdultInvestigationWarrant =   $this->getTotalReferralsNotActedReport($payload,"PI","Warrant") + $this->getTotalReferralsNotActedReport($payload,"RPI","Warrant") + $this->getTotalReferralsNotActedReport($payload,"TPI","Warrant");
		$getTotalNotActedJICLInvestigationWarrant =  $this->getTotalReferralsNotActedReport($payload,"JPI","Warrant ") + $this->getTotalReferralsNotActedReport($payload,"JRPI","Warrant ") + $this->getTotalReferralsNotActedReport($payload,"JTPI","Warrant");
		$getTotalNotActedTotalInvestigationWarrant = $getTotalNotActedAdultInvestigationWarrant + $getTotalNotActedJICLInvestigationWarrant;

		$response['notactedAdultInvestigationWarrant'] = $getTotalNotActedAdultInvestigationWarrant;
		$response['notactedJICLInvestigationWarrant'] = $getTotalNotActedJICLInvestigationWarrant;
		$response['notactedTotalInvestigationWarrant'] = $getTotalNotActedTotalInvestigationWarrant;

		$getTotalNotActedAdultInvestigation  =  $getTotalNotActedAdultInvestigationRecalled + $getTotalNotActedAdultInvestigationWarrant;
		$getTotalNotActedJICLInvestigation =  $getTotalNotActedJICLInvestigationRecalled + $getTotalNotActedJICLInvestigationWarrant;
		$getTotalNotActedInvestigation = $getTotalNotActedAdultInvestigation + $getTotalNotActedJICLInvestigation;

		$response['notactedInvestigationAdultTotal'] = $getTotalNotActedAdultInvestigation;
		$response['notactedInvestigationJICLTotal'] = $getTotalNotActedJICLInvestigation;
		$response['notactedInvestigationTotal'] = $getTotalNotActedInvestigation;

		//II.E, II.E.1, II.E.2 start
		$ii_e_1_adult =  $this->getTotalInvestigationCasesCourtIssued($payload,"PI","Recall") + $this->getTotalInvestigationCasesCourtIssued($payload,"RPI","Recall") + $this->getTotalInvestigationCasesCourtIssued($payload,"TPI","Recall");
		$ii_e_1_JICL = $this->getTotalInvestigationCasesCourtIssued($payload,"JPI","Recall") + $this->getTotalInvestigationCasesCourtIssued($payload,"JRPI","Recall") + $this->getTotalInvestigationCasesCourtIssued($payload,"JTPI","Recall");
		$ii_e_1_total = $ii_e_1_adult + $ii_e_1_JICL;


		$response['ii_e_1_adult'] = $ii_e_1_adult;
		$response['ii_e_1_JICL'] = $ii_e_1_JICL;
		$response['ii_e_1_total'] = $ii_e_1_total;


		$ii_e_2_adult =   $this->getTotalInvestigationCasesCourtIssued($payload,"PI","Warrant") + $this->getTotalInvestigationCasesCourtIssued($payload,"RPI","Warrant") + $this->getTotalInvestigationCasesCourtIssued($payload,"TPI","Warrant");
		$ii_e_2_JICL =  $this->getTotalInvestigationCasesCourtIssued($payload,"JPI","Warrant ") + $this->getTotalInvestigationCasesCourtIssued($payload,"JRPI","Warrant ") + $this->getTotalInvestigationCasesCourtIssued($payload,"JTPI","Warrant");
		$ii_e_2_total = $ii_e_2_adult + $ii_e_2_JICL;

		$response['ii_e_2_adult'] = $ii_e_2_adult;
		$response['ii_e_2_JICL'] = $ii_e_2_JICL;
		$response['ii_e_2_total'] = $ii_e_2_total;

		$total_adult  =  $ii_e_1_adult + $ii_e_2_adult;
		$total_JICL =  $ii_e_1_JICL + $ii_e_2_JICL;
		$total_adult_JICL = $total_adult + $total_JICL;

		$response['total_adult'] = $total_adult;
		$response['total_JICL'] = $total_JICL;
		$response['total_adult_JICL'] = $total_adult_JICL;
		//II.E, II.E.1, II.E.2 end

		$response['activeInvestigationTotalJICL'] = $response['CaseHandledJICL'] - $response['actedInvestigationJICL'] - $response['notactedInvestigationJICLTotal'];
		$response['activeInvestigationTotalADULT'] = $response['CaseHandledAdult'] - $response['actedInvestigationAdult'] - $response['notactedInvestigationAdultTotal'];
		$response['activeInvestigationTotalTOTAL'] = $response['activeInvestigationTotalJICL'] +  $response['activeInvestigationTotalADULT'];

		$payload->filter = "JPI"; $payload->table = "F5T3"; 
		$response['carryOverDispositionTotalJICLJPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPI"; $payload->table = "F5T3"; 
		$response['carryOverDispositionTotalJICLJRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPI"; $payload->table = "F5T3"; 
		$response['carryOverDispositionTotalJICLJTPI'] = json_decode($this->callProcedure1($payload));

		$response['carryOverDispositionTotalJICL'] = (object)array("count"=> ($response['carryOverDispositionTotalJICLJPI']->count + $response['carryOverDispositionTotalJICLJRPI']->count + $response['carryOverDispositionTotalJICLJTPI']->count));

		$payload->filter = "RPI"; $payload->table = "F5T3"; 
		$response['carryOverDispositionTotalAdultRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "PI"; $payload->table = "F5T3"; 
		$response['carryOverDispositionTotalAdultPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPI"; $payload->table = "F5T3"; 
		$response['carryOverDispositionTotalAdultTPI'] = json_decode($this->callProcedure1($payload));

		$response['carryOverDispositionTotalAdult'] = (object)array("count"=> ($response['carryOverDispositionTotalAdultRPI']->count + $response['carryOverDispositionTotalAdultPI']->count + $response['carryOverDispositionTotalAdultTPI']->count));
		$response['carryOverDispositionTotalTotal'] = (object)array("count"=> ($response['carryOverDispositionTotalJICL']->count + $response['carryOverDispositionTotalAdult']->count));




		$response['tobeactedDispositionTotalJICL'] = (object)array("count"=> ($response['carryOverDispositionTotalJICL']->count + $response['actedInvestigationTotalReportJICL']  ));
		$response['tobeactedDispositionTotalAdult'] = (object)array("count"=> ($response['carryOverDispositionTotalAdult']->count + $response['actedInvestigationTotalReportADULT']  ));
		$response['tobeactedDispositionTotalTotal'] = (object)array("count"=> ($response['tobeactedDispositionTotalJICL']->count + $response['tobeactedDispositionTotalAdult']->count));
		

		/*$response[''] = (object)array("count"=> ($response['carryOverDispositionTotalJICL']->count + $response['submittedDispositionTotalJICL']->count  ));
		$response[''] = (object)array("count"=> ($response['carryOverDispositionTotalAdult']->count + $response['submittedDispositionTotalAdult']->count  ));
		$response[''] = (object)array("count"=> ($response['tobeactedDispositionTotalJICL']->count + $response['tobeactedDispositionTotalAdult']->count  ));*/
		
		$payload->filter = "JPI"; $payload->table = "F5T4"; 
		$response['casedDispositionTotalJICLJPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPI"; $payload->table = "F5T4"; 
		$response['casedDispositionTotalJICLJRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPI"; $payload->table = "F5T4"; 
		$response['casedDispositionTotalJICLJTPI'] = json_decode($this->callProcedure1($payload));

		$response['casedDispositionTotalJICL'] = (object)array("count"=> ($response['casedDispositionTotalJICLJPI']->count + $response['casedDispositionTotalJICLJRPI']->count + $response['casedDispositionTotalJICLJTPI']->count));

		$payload->filter = "PI"; $payload->table = "F5T4"; 
		$response['casedDispositionTotalAdultPI'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "RPI"; $payload->table = "F5T4"; 
		$response['casedDispositionTotalAdultRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPI"; $payload->table = "F5T4"; 
		$response['casedDispositionTotalAdultTPI'] = json_decode($this->callProcedure1($payload));

		$response['casedDispositionTotalAdult'] = (object)array("count"=> ($response['casedDispositionTotalAdultPI']->count + $response['casedDispositionTotalAdultRPI']->count + $response['casedDispositionTotalAdultTPI']->count));


		$response['casedDispositionTotalTotal'] = (object)array("count"=> ($response['casedDispositionTotalJICL']->count + $response['casedDispositionTotalAdult']->count));

		
		$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
		$response['casedDispositionGrantJICLJPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
		$response['casedDispositionGrantJICLJRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
		$response['casedDispositionGrantJICLJTPI'] = json_decode($this->callProcedure1($payload));

		$response['casedDispositionGrantJICL'] = (object)array("count"=> ($response['casedDispositionGrantJICLJPI']->count + $response['casedDispositionGrantJICLJRPI']->count+ $response['casedDispositionGrantJICLJTPI']->count));

		
		$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
		$response['casedDispositionGrantAdultPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
		$response['casedDispositionGrantAdultRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Granted"; 
		$response['casedDispositionGrantAdultTPI'] = json_decode($this->callProcedure1($payload));

		$response['casedDispositionGrantAdult'] = (object)array("count"=> ($response['casedDispositionGrantAdultPI']->count + $response['casedDispositionGrantAdultRPI']->count+ $response['casedDispositionGrantAdultTPI']->count));

		$response['casedDispositionGrantTotal'] = (object)array("count"=> ($response['casedDispositionGrantJICL']->count + $response['casedDispositionGrantAdult']->count));
		

		$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
		$response['casedDispositionDeniedJICLJPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
		$response['casedDispositionDeniedJICLJRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
		$response['casedDispositionDeniedJICJTPI'] = json_decode($this->callProcedure1($payload));

		
		$response['casedDispositionDeniedJICL'] = (object)array("count"=> ($response['casedDispositionDeniedJICLJPI']->count + $response['casedDispositionDeniedJICLJRPI']->count+ $response['casedDispositionDeniedJICJTPI']->count));

		$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
		$response['casedDispositionDeniedAdultPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
		$response['casedDispositionDeniedAdultRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Deni"; 
		$response['casedDispositionDeniedAdultTPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = Null; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Denied"; 
		$response['casedDispositionDeniedData'] = json_decode($this->callProcedure1AA($payload));

		$response['casedDispositionDeniedAdult'] = (object)array("count"=> ($response['casedDispositionDeniedAdultPI']->count + $response['casedDispositionDeniedAdultRPI']->count+ $response['casedDispositionDeniedAdultTPI']->count));



		$response['casedDispositionDeniedTotal'] = (object)array("count"=> ($response['casedDispositionDeniedJICL']->count + $response['casedDispositionDeniedAdult']->count));
		



		$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
		$response['casedDispositionDiedJICLJPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
		$response['casedDispositionDiedJICLJRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
		$response['casedDispositionDiedJICLJTPI'] = json_decode($this->callProcedure1($payload));

		$response['casedDispositionDiedJICL'] = (object)array("count"=> ($response['casedDispositionDiedJICLJPI']->count + $response['casedDispositionDiedJICLJRPI']->count+ $response['casedDispositionDiedJICLJTPI']->count));



		$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
		$response['casedDispositionDiedAdultPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
		$response['casedDispositionDiedAdultRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
		$response['casedDispositionDiedAdultTPI'] = json_decode($this->callProcedure1($payload));

		$response['casedDispositionDiedAdult'] = (object)array("count"=> ($response['casedDispositionDiedAdultPI']->count + $response['casedDispositionDiedAdultRPI']->count+ $response['casedDispositionDiedAdultTPI']->count));

		$payload->filter = Null; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Dismissed"; 
		$response['casedDispositionDismissedData'] = json_decode($this->callProcedure1AA($payload));

		$response['casedDispositionDiedTotal'] = (object)array("count"=> ($response['casedDispositionDiedJICL']->count + $response['casedDispositionDiedAdult']->count));
		

		$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
		$response['casedDispositionWithJICLJPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
		$response['casedDispositionWithJICLJRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
		$response['casedDispositionWithJICLJTPI'] = json_decode($this->callProcedure1($payload));

		$response['casedDispositionWithJICL'] = (object)array("count"=> ($response['casedDispositionWithJICLJPI']->count + $response['casedDispositionWithJICLJRPI']->count+ $response['casedDispositionWithJICLJTPI']->count));


		$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
		$response['casedDispositionWithAdultPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
		$response['casedDispositionWithAdultRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "With"; 
		$response['casedDispositionWithAdultTPI'] = json_decode($this->callProcedure1($payload));

		$response['casedDispositionWithAdult'] = (object)array("count"=> ($response['casedDispositionWithAdultPI']->count + $response['casedDispositionWithAdultRPI']->count+ $response['casedDispositionWithAdultTPI']->count));


		$response['casedDispositionWithTotal'] = (object)array("count"=> ($response['casedDispositionWithJICL']->count + $response['casedDispositionWithAdult']->count));

		
		$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Reinv"; 
		$response['casedDispositionReinvJICLJPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Reinv"; 
		$response['casedDispositionReinvJICLJRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Reinv"; 
		$response['casedDispositionReinvJICLJTPI'] = json_decode($this->callProcedure1($payload));
		
		$response['casedDispositionReinvJICL'] = (object)array("count"=> ($response['casedDispositionReinvJICLJPI']->count + $response['casedDispositionReinvJICLJRPI']->count+ $response['casedDispositionReinvJICLJTPI']->count));



		$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Reinv"; 
		$response['casedDispositionReinvAdultPI'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Reinv"; 
		$response['casedDispositionReinvAdultRPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Reinv"; 
		$response['casedDispositionReinvAdultTPI'] = json_decode($this->callProcedure1($payload));



		$response['casedDispositionReinvAdult'] = (object)array("count"=> ($response['casedDispositionReinvAdultPI']->count + $response['casedDispositionReinvAdultRPI']->count + $response['casedDispositionReinvAdultTPI']->count));


		$response['casedDispositionReinvTotal'] = (object)array("count"=> ($response['casedDispositionReinvJICL']->count + $response['casedDispositionReinvAdult']->count));


		/*New Others 2021-01-16*/
		$payload->filter = "JPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['casedDispositionOtherJICLJPI'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "JRPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['casedDispositionOtherJICLJRPI'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "JTPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['casedDispositionOtherJICLTPI'] = json_decode($this->callProcedure1($payload));
		$response['casedDispositionOtherJICL'] = (object)array("count"=> ($response['casedDispositionOtherJICLJPI']->count + $response['casedDispositionOtherJICLJRPI']->count+ $response['casedDispositionOtherJICLTPI']->count));

		$payload->filter = "PI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['casedDispositionOtherAdultPI'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "RPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['casedDispositionOtherAdultRPI'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "TPI"; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['casedDispositionOtherAdultTPI'] = json_decode($this->callProcedure1($payload));

		$response['casedDispositionOtherAdult'] = (object)array("count"=> ($response['casedDispositionOtherAdultPI']->count + $response['casedDispositionOtherAdultRPI']->count + $response['casedDispositionOtherAdultTPI']->count));
		$response['casedDispositionOtherTotal'] = (object)array("count"=> ($response['casedDispositionOtherJICL']->count + $response['casedDispositionOtherAdult']->count));

		$payload->filter = Null; $payload->table = "F5T4"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['casedDispositionOtherData'] = json_decode($this->callProcedure1AA($payload));
		/*New Others*/



		$response['casePendingDispositionJICLTotal'] = (object)array("count"=> ($response['tobeactedDispositionTotalJICL']->count - $response['casedDispositionTotalJICL']->count));
		$response['casePendingDispositionAdultTotal'] = (object)array("count"=> ($response['tobeactedDispositionTotalAdult']->count - $response['casedDispositionTotalAdult']->count));
		$response['casePendingDispositionTotalTotal'] = (object)array("count"=> ($response['casePendingDispositionJICLTotal']->count + $response['casePendingDispositionAdultTotal']->count));
		
		
		$payload->filter = "JCPI"; $payload->table = "F5T5"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalCarryOverCPIJICL'] = json_decode($this->callProcedure1($payload));


		$payload->filter = "CPI"; $payload->table = "F5T5"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalCarryOverCPIAdultCPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "FBCI"; $payload->table = "F5T5"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalCarryOverCPIAdultFBCI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RCPI"; $payload->table = "F5T5"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalCarryOverCPIAdultRCPI'] = json_decode($this->callProcedure1($payload));

		$response['totalCarryOverCPIAdult'] = (object)array("count"=> ($response['totalCarryOverCPIAdultCPI']->count + $response['totalCarryOverCPIAdultFBCI']->count+ $response['totalCarryOverCPIAdultRCPI']->count));


		$response['totalCarryOverCPITotal'] = (object)array("count"=> ($response['totalCarryOverCPIJICL']->count + $response['totalCarryOverCPIAdult']->count));

		$payload->filter = "JCPI"; $payload->table = "F5T6_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvCPIJICL'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "FBCI"; $payload->table = "F5T6_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvCPIAdultFB'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "CPI"; $payload->table = "F5T6_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvCPIAdultCPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RCPI"; $payload->table = "F5T6_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvCPIAdultRCPI'] = json_decode($this->callProcedure1($payload));

		$response['totalRcvCPIAdult'] = (object)array("count"=> ($response['totalRcvCPIAdultFB']->count + $response['totalRcvCPIAdultCPI']->count+ $response['totalRcvCPIAdultRCPI']->count));


		$response['totalRcvCPITotal'] = (object)array("count"=> ($response['totalRcvCPIJICL']->count + $response['totalRcvCPIAdult']->count));


		$response['totalHandledCPIJICL'] = (object)array("count"=> ($response['totalCarryOverCPIJICL']->count + $response['totalRcvCPIJICL']->count));
		$response['totalHandledCPIAdult'] = (object)array("count"=> ($response['totalCarryOverCPIAdult']->count + $response['totalRcvCPIAdult']->count));
		$response['totalHandledCPITotal'] = (object)array("count"=> ($response['totalHandledCPIJICL']->count + $response['totalHandledCPIAdult']->count));
		
		$payload->filter = "JCPI"; $payload->table = "F5T6_CMPLTD"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalCmpltdCPIJICL'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "CPI"; $payload->table = "F5T6_CMPLTD"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalCmpltdCPIAdultCPI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "FBCI"; $payload->table = "F5T6_CMPLTD"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalCmpltdCPIAdultFBCI'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RCPI"; $payload->table = "F5T6_CMPLTD"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalCmpltdCPIAdultRCPI'] = json_decode($this->callProcedure1($payload));

		$response['totalCmpltdCPIAdult'] = (object)array("count"=> ($response['totalCmpltdCPIAdultCPI']->count + $response['totalCmpltdCPIAdultFBCI']->count+ $response['totalCmpltdCPIAdultRCPI']->count));


		$response['totalCmpltdCPITotal'] = (object)array("count"=> ($response['totalCmpltdCPIJICL']->count + $response['totalCmpltdCPIAdult']->count));

		$response['totalActiveCPIJICL'] = (object)array("count"=> ($response['totalHandledCPIJICL']->count - $response['totalCmpltdCPIJICL']->count));
		$response['totalActiveCPIAdult'] = (object)array("count"=> ($response['totalHandledCPIAdult']->count - $response['totalCmpltdCPIAdult']->count));
		$response['totalActiveCPITotal'] = (object)array("count"=> ($response['totalHandledCPITotal']->count - $response['totalCmpltdCPITotal']->count));
		

		$payload->filter = "JPS"; $payload->table = "F5T7"; $payload->filter_field = ""; $payload->filter_val = "";
		$response['totalCarryOverSupvJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T7"; $payload->filter_field = ""; $payload->filter_val = "";
		$response['totalCarryOverSupvJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T7"; $payload->filter_field = ""; $payload->filter_val = "";
		$response['totalCarryOverSupvJICLJPRS'] = json_decode($this->callProcedure1($payload));

		$response['totalCarryOverSupvJICL'] = (object)array("count"=> ($response['totalCarryOverSupvJICLJPS']->count + $response['totalCarryOverSupvJICLJTPS']->count+ $response['totalCarryOverSupvJICLJPRS']->count));


		$payload->filter = "PS"; $payload->table = "F5T7"; $payload->filter_field = ""; $payload->filter_val = "";
		$response['totalCarryOverSupvAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T7"; $payload->filter_field = ""; $payload->filter_val = "";
		$response['totalCarryOverSupvAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T7"; $payload->filter_field = ""; $payload->filter_val = "";
		$response['totalCarryOverSupvAdultTPS'] = json_decode($this->callProcedure1($payload));


		$response['totalCarryOverSupvAdult'] = (object)array("count"=> ($response['totalCarryOverSupvAdultPS']->count + $response['totalCarryOverSupvAdultTPS']->count+ $response['totalCarryOverSupvAdultRPS']->count));

		$response['totalCarryOverSupvTotal'] = (object)array("count"=> ($response['totalCarryOverSupvJICL']->count + $response['totalCarryOverSupvAdult']->count));

		$payload->filter = "JPS"; $payload->table = "F5T8"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvSupvJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T8"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvSupvJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T8"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvSupvJICLJTPS'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvJICL'] = (object)array("count"=> ($response['totalRcvSupvJICLJPS']->count + $response['totalRcvSupvJICLJRPS']->count+ $response['totalRcvSupvJICLJTPS']->count));

		
		$payload->filter = "PS"; $payload->table = "F5T8"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvSupvAdultPS'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "TPS"; $payload->table = "F5T8"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvSupvAdultTPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T8"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalRcvSupvAdultRPS'] = json_decode($this->callProcedure1($payload));


		//LOCAL

		$payload->filter = "JPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Local"; 
		$response['totalRcvSupvJICLJPSLocal'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Local"; 
		$response['totalRcvSupvJICLJRPSLocal'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Local"; 
		$response['totalRcvSupvJICLJTPSLocal'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvJICLLocal'] = (object)array("count"=> ($response['totalRcvSupvJICLJPSLocal']->count + $response['totalRcvSupvJICLJRPSLocal']->count+ $response['totalRcvSupvJICLJTPSLocal']->count));

		$payload->filter = "PS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Local"; 
		$response['totalRcvSupvAdultPSLocal'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Local"; 
		$response['totalRcvSupvAdultRPSLocal'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Local"; 
		$response['totalRcvSupvAdultTPSLocal'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvAdultLocal'] = (object)array("count"=> ($response['totalRcvSupvAdultPSLocal']->count + $response['totalRcvSupvAdultRPSLocal']->count+ $response['totalRcvSupvAdultTPSLocal']->count));

		$response['totalRcvSupvLocal'] = (object)array("count"=> ($response['totalRcvSupvJICLLocal']->count + $response['totalRcvSupvAdultLocal']->count));
		

		//DIRECT

		$payload->filter = "JPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Direct"; 
		$response['totalRcvSupvJICLJPSDirect'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Direct"; 
		$response['totalRcvSupvJICLJRPSDirect'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Direct"; 
		$response['totalRcvSupvJICLJTPSDirect'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvJICLDirect'] = (object)array("count"=> ($response['totalRcvSupvJICLJPSDirect']->count + $response['totalRcvSupvJICLJRPSDirect']->count+ $response['totalRcvSupvJICLJTPSDirect']->count));

		$payload->filter = "PS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Direct"; 
		$response['totalRcvSupvAdultPSDirect'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Direct"; 
		$response['totalRcvSupvAdultRPSDirect'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Direct"; 
		$response['totalRcvSupvAdultTPSDirect'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvAdultDirect'] = (object)array("count"=> ($response['totalRcvSupvAdultPSDirect']->count + $response['totalRcvSupvAdultRPSDirect']->count+ $response['totalRcvSupvAdultTPSDirect']->count));


		$response['totalRcvSupvDirect'] = (object)array("count"=> ($response['totalRcvSupvJICLDirect']->count + $response['totalRcvSupvAdultDirect']->count));
		
		//Transfer

		$payload->filter = "JPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Transfer From"; 
		$response['totalRcvSupvJICLJPSTransfer'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Transfer From"; 
		$response['totalRcvSupvJICLJRPSTransfer'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Transfer From"; 
		$response['totalRcvSupvJICLJTPSTransfer'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvJICLTransfer'] = (object)array("count"=> ($response['totalRcvSupvJICLJPSTransfer']->count + $response['totalRcvSupvJICLJRPSTransfer']->count+ $response['totalRcvSupvJICLJTPSTransfer']->count));

		$payload->filter = "PS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Transfer From"; 
		$response['totalRcvSupvAdultPSTransfer'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Transfer From"; 
		$response['totalRcvSupvAdultRPSTransfer'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Transfer From"; 
		$response['totalRcvSupvAdultTPSTransfer'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvAdultTransfer'] = (object)array("count"=> ($response['totalRcvSupvAdultPSTransfer']->count + $response['totalRcvSupvAdultRPSTransfer']->count+ $response['totalRcvSupvAdultTPSTransfer']->count));


		$response['totalRcvSupvTransfer'] = (object)array("count"=> ($response['totalRcvSupvJICLTransfer']->count + $response['totalRcvSupvAdultTransfer']->count));

		//Recon		
		$payload->filter = "JPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Recon"; 
		$response['totalRcvSupvJICLJPSRecon'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Recon"; 
		$response['totalRcvSupvJICLJRPSRecon'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Recon"; 
		$response['totalRcvSupvJICLJTPSRecon'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvJICLRecon'] = (object)array("count"=> ($response['totalRcvSupvJICLJPSRecon']->count + $response['totalRcvSupvJICLJRPSRecon']->count+ $response['totalRcvSupvJICLJTPSRecon']->count));

		$payload->filter = "PS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Recon"; 
		$response['totalRcvSupvAdultPSRecon'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Recon"; 
		$response['totalRcvSupvAdultRPSRecon'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Recon"; 
		$response['totalRcvSupvAdultTPSRecon'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvAdultRecon'] = (object)array("count"=> ($response['totalRcvSupvAdultPSRecon']->count + $response['totalRcvSupvAdultRPSRecon']->count+ $response['totalRcvSupvAdultTPSRecon']->count));


		$response['totalRcvSupvRecon'] = (object)array("count"=> ($response['totalRcvSupvJICLRecon']->count + $response['totalRcvSupvAdultRecon']->count));

		//Militar		
		$payload->filter = "JPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Militar"; 
		$response['totalRcvSupvJICLJPSMilitar'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Militar"; 
		$response['totalRcvSupvJICLJRPSMilitar'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Militar"; 
		$response['totalRcvSupvJICLJTPSMilitar'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvJICLMilitar'] = (object)array("count"=> ($response['totalRcvSupvJICLJPSMilitar']->count + $response['totalRcvSupvJICLJRPSMilitar']->count+ $response['totalRcvSupvJICLJTPSMilitar']->count));

		$payload->filter = "PS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Militar"; 
		$response['totalRcvSupvAdultPSMilitar'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Militar"; 
		$response['totalRcvSupvAdultRPSMilitar'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T8"; $payload->filter_field = "referral_type"; $payload->filter_val = "Militar"; 
		$response['totalRcvSupvAdultTPSMilitar'] = json_decode($this->callProcedure1($payload));
		$response['totalRcvSupvAdultMilitar'] = (object)array("count"=> ($response['totalRcvSupvAdultPSMilitar']->count + $response['totalRcvSupvAdultRPSMilitar']->count+ $response['totalRcvSupvAdultTPSMilitar']->count));


		$response['totalRcvSupvMilitar'] = (object)array("count"=> ($response['totalRcvSupvJICLMilitar']->count + $response['totalRcvSupvAdultMilitar']->count));
		//DONE


		$response['totalRcvSupvAdult'] = (object)array("count"=> ($response['totalRcvSupvAdultPS']->count + $response['totalRcvSupvAdultTPS']->count+ $response['totalRcvSupvAdultRPS']->count));

		
		$response['totalRcvSupvTotal'] = (object)array("count"=> ($response['totalRcvSupvJICL']->count + $response['totalRcvSupvAdult']->count));

		$response['totalHandledPSJICL'] = (object)array("count"=> ($response['totalCarryOverSupvJICL']->count + $response['totalRcvSupvJICL']->count));
		$response['totalHandledPSAdult'] = (object)array("count"=> ($response['totalCarryOverSupvAdult']->count + $response['totalRcvSupvAdult']->count));
		$response['totalHandledPSTotal'] = (object)array("count"=> ($response['totalHandledPSJICL']->count + $response['totalHandledPSAdult']->count));
		
		//TODO
		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalDrpSupvJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalDrpSupvJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalDrpSupvJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvJICL'] = (object)array("count"=> ($response['totalDrpSupvJICLJPS']->count + $response['totalDrpSupvJICLJRPS']->count +$response['totalDrpSupvJICLJTPS']->count  ));
		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalDrpSupvAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalDrpSupvAdultTPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalDrpSupvAdultRPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalDrpSupvAdult'] = (object)array("count"=> ($response['totalDrpSupvAdultPS']->count + $response['totalDrpSupvAdultTPS']->count +$response['totalDrpSupvAdultRPS']->count  ));
		
		$response['totalDrpSupvTotal'] = (object)array("count"=> ($response['totalDrpSupvJICL']->count + $response['totalDrpSupvAdult']->count));


		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalDrpSupvTermJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalDrpSupvTermJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalDrpSupvTermJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvTermJICL'] = (object)array("count"=> ($response['totalDrpSupvTermJICLJPS']->count + $response['totalDrpSupvTermJICLJRPS']->count +$response['totalDrpSupvTermJICLJTPS']->count  ));
		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalDrpSupvTermAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalDrpSupvTermAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalDrpSupvTermAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalDrpSupvTermAdult'] = (object)array("count"=> ($response['totalDrpSupvTermAdultPS']->count + $response['totalDrpSupvTermAdultRPS']->count + $response['totalDrpSupvTermAdultTPS']->count));


		$response['totalDrpSupvTermTotal'] = (object)array("count"=> ($response['totalDrpSupvTermAdult']->count + $response['totalDrpSupvTermJICL']->count));


		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalDrpSupvFullTermJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalDrpSupvFullTermJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalDrpSupvFullTermJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvFullTermJICL'] = (object)array("count"=> ($response['totalDrpSupvFullTermJICLJPS']->count + $response['totalDrpSupvFullTermJICLJRPS']->count + $response['totalDrpSupvFullTermJICLJTPS']->count));

		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalDrpSupvFullTermAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalDrpSupvFullTermAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalDrpSupvFullTermAdultTPS'] = json_decode($this->callProcedure1($payload));
		
		$response['totalDrpSupvFullTermAdult'] = (object)array("count"=> ($response['totalDrpSupvFullTermAdultPS']->count + $response['totalDrpSupvFullTermAdultRPS']->count + $response['totalDrpSupvFullTermAdultTPS']->count));


		$response['totalDrpSupvFullTermTotal'] = (object)array("count"=> ($response['totalDrpSupvFullTermAdult']->count + $response['totalDrpSupvFullTermJICL']->count));
	

		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalDrpSupvEarlyTermJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalDrpSupvEarlyTermJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalDrpSupvEarlyTermJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvEarlyTermJICL'] = (object)array("count"=> ($response['totalDrpSupvEarlyTermJICLJPS']->count + $response['totalDrpSupvEarlyTermJICLJRPS']->count + $response['totalDrpSupvEarlyTermJICLJTPS']->count));
		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalDrpSupvEarlyTermAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalDrpSupvEarlyTermAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalDrpSupvEarlyTermAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalDrpSupvEarlyTermAdult'] = (object)array("count"=> ($response['totalDrpSupvEarlyTermAdultPS']->count + $response['totalDrpSupvEarlyTermAdultRPS']->count + $response['totalDrpSupvEarlyTermAdultTPS']->count));


		$response['totalDrpSupvEarlyTermTotal'] = (object)array("count"=> ($response['totalDrpSupvEarlyTermAdult']->count + $response['totalDrpSupvEarlyTermJICL']->count));

		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalDrpSupvDiedTermJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalDrpSupvDiedTermJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalDrpSupvDiedTermJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvDiedTermJICL'] = (object)array("count"=> ($response['totalDrpSupvDiedTermJICLJPS']->count + $response['totalDrpSupvDiedTermJICLJRPS']->count + $response['totalDrpSupvDiedTermJICLJTPS']->count));

		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalDrpSupvDiedTermAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalDrpSupvDiedTermAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalDrpSupvDiedTermAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalDrpSupvDiedTermAdult'] = (object)array("count"=> ($response['totalDrpSupvDiedTermAdultPS']->count + $response['totalDrpSupvDiedTermAdultRPS']->count + $response['totalDrpSupvDiedTermAdultTPS']->count));


		$response['totalDrpSupvDiedTermTotal'] = (object)array("count"=> ($response['totalDrpSupvDiedTermAdult']->count + $response['totalDrpSupvDiedTermJICL']->count));



		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalDrpSupvRevocJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalDrpSupvRevocJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalDrpSupvRevocJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvRevocJICL'] = (object)array("count"=> ($response['totalDrpSupvRevocJICLJPS']->count + $response['totalDrpSupvRevocJICLJRPS']->count + $response['totalDrpSupvRevocJICLJTPS']->count));


		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalDrpSupvRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalDrpSupvRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalDrpSupvRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalDrpSupvRevocAdult'] = (object)array("count"=> ($response['totalDrpSupvRevocAdultPS']->count + $response['totalDrpSupvRevocAdultRPS']->count + $response['totalDrpSupvRevocAdultTPS']->count));

		$response['totalDrpSupvRevocTotal'] = (object)array("count"=> ($response['totalDrpSupvRevocAdult']->count + $response['totalDrpSupvRevocJICL']->count));

		
		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalDrpSupvAbsRevocJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalDrpSupvAbsRevocJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalDrpSupvAbsRevocJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvAbsRevocJICL'] = (object)array("count"=> ($response['totalDrpSupvAbsRevocJICLJPS']->count + $response['totalDrpSupvAbsRevocJICLJRPS']->count + $response['totalDrpSupvAbsRevocJICLJTPS']->count));
		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalDrpSupvAbsRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalDrpSupvAbsRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalDrpSupvAbsRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalDrpSupvAbsRevocAdult'] = (object)array("count"=> ($response['totalDrpSupvAbsRevocAdultPS']->count + $response['totalDrpSupvAbsRevocAdultRPS']->count + $response['totalDrpSupvAbsRevocAdultTPS']->count));


		$response['totalDrpSupvAbsRevocTotal'] = (object)array("count"=> ($response['totalDrpSupvAbsRevocJICL']->count + $response['totalDrpSupvAbsRevocAdult']->count));

		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalDrpSupvCommRevocJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalDrpSupvCommRevocJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalDrpSupvCommRevocJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvCommRevocJICL'] = (object)array("count"=> ($response['totalDrpSupvCommRevocJICLJPS']->count + $response['totalDrpSupvCommRevocJICLJRPS']->count + $response['totalDrpSupvCommRevocJICLJTPS']->count));

		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalDrpSupvCommRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalDrpSupvCommRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalDrpSupvCommRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalDrpSupvCommRevocAdult'] = (object)array("count"=> ($response['totalDrpSupvCommRevocAdultPS']->count + $response['totalDrpSupvCommRevocAdultRPS']->count + $response['totalDrpSupvCommRevocAdultTPS']->count));


		$response['totalDrpSupvCommRevocTotal'] = (object)array("count"=> ($response['totalDrpSupvCommRevocAdult']->count + $response['totalDrpSupvCommRevocJICL']->count));


		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalDrpSupvViolRevocJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalDrpSupvViolRevocJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalDrpSupvViolRevocJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvViolRevocJICL'] = (object)array("count"=> ($response['totalDrpSupvViolRevocJICLJPS']->count + $response['totalDrpSupvViolRevocJICLJRPS']->count + $response['totalDrpSupvViolRevocJICLJTPS']->count));
		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalDrpSupvViolRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalDrpSupvViolRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalDrpSupvViolRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalDrpSupvViolRevocAdult'] = (object)array("count"=> ($response['totalDrpSupvViolRevocAdultPS']->count + $response['totalDrpSupvViolRevocAdultRPS']->count + $response['totalDrpSupvViolRevocAdultTPS']->count));


		$response['totalDrpSupvViolRevocTotal'] = (object)array("count"=> ($response['totalDrpSupvViolRevocAdult']->count + $response['totalDrpSupvViolRevocJICL']->count));

		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalDrpSupvOtherRevocJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalDrpSupvOtherRevocJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalDrpSupvOtherRevocJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvOtherRevocJICL'] = (object)array("count"=> ($response['totalDrpSupvOtherRevocJICLJPS']->count + $response['totalDrpSupvOtherRevocJICLJRPS']->count + $response['totalDrpSupvOtherRevocJICLJTPS']->count));


		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalDrpSupvOtherRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalDrpSupvOtherRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalDrpSupvOtherRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
			

		$response['totalDrpSupvOtherRevocAdult'] = (object)array("count"=> ($response['totalDrpSupvOtherRevocAdultPS']->count + $response['totalDrpSupvOtherRevocAdultRPS']->count + $response['totalDrpSupvOtherRevocAdultTPS']->count));



		$payload->filter = Null; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalDrpSupvOtherRevocData'] = json_decode($this->callProcedure1AA($payload));

		$response['totalDrpSupvOtherRevocTotal'] = (object)array("count"=> ($response['totalDrpSupvOtherRevocAdult']->count + $response['totalDrpSupvOtherRevocJICL']->count));


		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalDrpSupvExtRevocJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalDrpSupvExtRevocJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalDrpSupvExtRevocJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvExtRevocJICL'] = (object)array("count"=> ($response['totalDrpSupvExtRevocJICLJPS']->count + $response['totalDrpSupvExtRevocJICLJRPS']->count + $response['totalDrpSupvExtRevocJICLJTPS']->count));

		
		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalDrpSupvExtRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalDrpSupvExtRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalDrpSupvExtRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		
		$response['totalDrpSupvExtRevocAdult'] = (object)array("count"=> ($response['totalDrpSupvExtRevocAdultPS']->count + $response['totalDrpSupvExtRevocAdultRPS']->count + $response['totalDrpSupvExtRevocAdultTPS']->count));


		$response['totalDrpSupvExtRevocTotal'] = (object)array("count"=> ($response['totalDrpSupvExtRevocAdult']->count + $response['totalDrpSupvExtRevocJICL']->count));

		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalDrpSupvTransferJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalDrpSupvTransferJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalDrpSupvTransferJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvTransferJICL'] = (object)array("count"=> ($response['totalDrpSupvTransferJICLJPS']->count + $response['totalDrpSupvTransferJICLJRPS']->count + $response['totalDrpSupvTransferJICLJTPS']->count));


		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalDrpSupvTransferAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalDrpSupvTransferAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalDrpSupvTransferAdultTPS'] = json_decode($this->callProcedure1($payload));
		
		$response['totalDrpSupvTransferAdult'] = (object)array("count"=> ($response['totalDrpSupvTransferAdultPS']->count + $response['totalDrpSupvTransferAdultRPS']->count + $response['totalDrpSupvTransferAdultTPS']->count));

		$response['totalDrpSupvTransferTotal'] = (object)array("count"=> ($response['totalDrpSupvTransferJICL']->count + $response['totalDrpSupvTransferAdult']->count));

		// Others IV - D - 4 table 11
		$payload->filter = "JPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalDrpSupvOthersJICLJPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JRPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalDrpSupvOthersJICLJRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "JTPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalDrpSupvOthersJICLJTPS'] = json_decode($this->callProcedure1($payload));

		$response['totalDrpSupvOthersJICL'] = (object)array("count"=> ($response['totalDrpSupvOthersJICLJPS']->count + $response['totalDrpSupvOthersJICLJRPS']->count + $response['totalDrpSupvOthersJICLJTPS']->count));


		$payload->filter = "PS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalDrpSupvOthersAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalDrpSupvOthersAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T11"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalDrpSupvOthersAdultTPS'] = json_decode($this->callProcedure1($payload));
		
		$response['totalDrpSupvOthersAdult'] = (object)array("count"=> ($response['totalDrpSupvOthersAdultPS']->count + $response['totalDrpSupvOthersAdultRPS']->count + $response['totalDrpSupvOthersAdultTPS']->count));

		$response['totalDrpSupvOthersTotal'] = (object)array("count"=> ($response['totalDrpSupvOthersJICL']->count + $response['totalDrpSupvOthersAdult']->count));



		$response['totalActivePSJICL'] = (object)array("count"=> ($response['totalHandledPSJICL']->count - $response['totalDrpSupvJICL']->count));
		$response['totalActivePSAdult'] = (object)array("count"=> ($response['totalHandledPSAdult']->count - $response['totalDrpSupvAdult']->count));
		$response['totalActivePSTotal'] = (object)array("count"=> ($response['totalActivePSJICL']->count + $response['totalActivePSAdult']->count));
		
		//
		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalSubSupvJICL'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalSubSupvAdultPS'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalSubSupvAdultTPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['totalSubSupvAdultRPS'] = json_decode($this->callProcedure1($payload));
		
		$response['totalSubSupvAdult'] = (object)array("count"=> ($response['totalSubSupvAdultPS']->count + $response['totalSubSupvAdultTPS']->count+ $response['totalSubSupvAdultRPS']->count));
		$response['totalSubSupvTotal'] = (object)array("count"=> ($response['totalSubSupvJICL']->count + $response['totalSubSupvAdult']->count));


		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalSubSupvTermJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalSubSupvTermAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalSubSupvTermAdultTPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Term"; 
		$response['totalSubSupvTermAdultRPS'] = json_decode($this->callProcedure1($payload));


		$response['totalSubSupvTermAdult'] = (object)array("count"=> ($response['totalSubSupvTermAdultPS']->count + $response['totalSubSupvTermAdultTPS']->count+ $response['totalSubSupvTermAdultRPS']->count));


		$response['totalSubSupvTermTotal'] = (object)array("count"=> ($response['totalSubSupvTermAdult']->count + $response['totalSubSupvTermJICL']->count));


		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalSubSupvFullTermJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalSubSupvFullTermAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalSubSupvFullTermAdultTPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Full Term"; 
		$response['totalSubSupvFullTermAdultRPS'] = json_decode($this->callProcedure1($payload));

		$response['totalSubSupvFullTermAdult'] = (object)array("count"=> ($response['totalSubSupvFullTermAdultPS']->count + $response['totalSubSupvFullTermAdultTPS']->count+ $response['totalSubSupvFullTermAdultRPS']->count));
		$response['totalSubSupvFullTermTotal'] = (object)array("count"=> ($response['totalSubSupvFullTermAdult']->count + $response['totalSubSupvFullTermJICL']->count));
	

		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalSubSupvEarlyTermJICL'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalSubSupvEarlyTermAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalSubSupvEarlyTermAdultTPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Early Term"; 
		$response['totalSubSupvEarlyTermAdultRPS'] = json_decode($this->callProcedure1($payload));


		$response['totalSubSupvEarlyTermAdult'] = (object)array("count"=> ($response['totalSubSupvEarlyTermAdultPS']->count + $response['totalSubSupvEarlyTermAdultTPS']->count + $response['totalSubSupvEarlyTermAdultRPS']->count));


		$response['totalSubSupvEarlyTermTotal'] = (object)array("count"=> ($response['totalSubSupvEarlyTermAdult']->count + $response['totalSubSupvEarlyTermJICL']->count));

		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalSubSupvDiedTermJICL'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalSubSupvDiedTermAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalSubSupvDiedTermAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Termination - Died"; 
		$response['totalSubSupvDiedTermAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalSubSupvDiedTermAdult'] = (object)array("count"=> ($response['totalSubSupvDiedTermAdultPS']->count + $response['totalSubSupvDiedTermAdultTPS']->count + $response['totalSubSupvDiedTermAdultRPS']->count));



		$response['totalSubSupvDiedTermTotal'] = (object)array("count"=> ($response['totalSubSupvDiedTermAdult']->count + $response['totalSubSupvDiedTermJICL']->count));

		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalSubSupvRevocJICL'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalSubSupvRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalSubSupvRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revoc"; 
		$response['totalSubSupvRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalSubSupvRevocAdult'] = (object)array("count"=> ($response['totalSubSupvRevocAdultPS']->count + $response['totalSubSupvRevocAdultTPS']->count+ $response['totalSubSupvRevocAdultRPS']->count));


		$response['totalSubSupvRevocTotal'] = (object)array("count"=> ($response['totalSubSupvRevocAdult']->count + $response['totalSubSupvRevocJICL']->count));

		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalSubSupvAbsRevocJICL'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalSubSupvAbsRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalSubSupvAbsRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Abscond"; 
		$response['totalSubSupvAbsRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalSubSupvAbsRevocAdult'] =  (object)array("count"=> ($response['totalSubSupvAbsRevocAdultPS']->count + $response['totalSubSupvAbsRevocAdultTPS']->count + $response['totalSubSupvAbsRevocAdultRPS']->count));



		$response['totalSubSupvAbsRevocTotal'] = (object)array("count"=> ($response['totalSubSupvAbsRevocJICL']->count + $response['totalSubSupvAbsRevocAdult']->count));



		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalSubSupvCommRevocJICL'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalSubSupvCommRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalSubSupvCommRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Commission"; 
		$response['totalSubSupvCommRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalSubSupvCommRevocAdult'] = (object)array("count"=> ($response['totalSubSupvCommRevocAdultPS']->count + $response['totalSubSupvCommRevocAdultTPS']->count+ $response['totalSubSupvCommRevocAdultRPS']->count));


		$response['totalSubSupvCommRevocTotal'] = (object)array("count"=> ($response['totalSubSupvCommRevocAdult']->count + $response['totalSubSupvCommRevocJICL']->count));


		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalSubSupvViolRevocJICL'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalSubSupvViolRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalSubSupvViolRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Viol"; 
		$response['totalSubSupvViolRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalSubSupvViolRevocAdult'] = (object)array("count"=> ($response['totalSubSupvViolRevocAdultPS']->count + $response['totalSubSupvViolRevocAdultTPS']->count+ $response['totalSubSupvViolRevocAdultRPS']->count));


		$response['totalSubSupvViolRevocTotal'] = (object)array("count"=> ($response['totalSubSupvViolRevocAdult']->count + $response['totalSubSupvViolRevocJICL']->count));

		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalSubSupvOtherRevocJICL'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalSubSupvOtherRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalSubSupvOtherRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalSubSupvOtherRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		
		$response['totalSubSupvOtherRevocAdult'] = (object)array("count"=> ($response['totalSubSupvOtherRevocAdultPS']->count + $response['totalSubSupvOtherRevocAdultTPS']->count+ $response['totalSubSupvOtherRevocAdultRPS']->count));

		$payload->filter = Null; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Revocation - Other"; 
		$response['totalSubSupvOtherRevocData'] = json_decode($this->callProcedure1AA($payload));

		$response['totalSubSupvOtherRevocTotal'] = (object)array("count"=> ($response['totalSubSupvOtherRevocAdult']->count + $response['totalSubSupvOtherRevocJICL']->count));


		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalSubSupvExtRevocJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalSubSupvExtRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalSubSupvExtRevocAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Extension"; 
		$response['totalSubSupvExtRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		

		$response['totalSubSupvExtRevocAdult'] = (object)array("count"=> ($response['totalSubSupvExtRevocAdultPS']->count + $response['totalSubSupvExtRevocAdultTPS']->count + $response['totalSubSupvExtRevocAdultRPS']->count));


		$response['totalSubSupvExtRevocTotal'] = (object)array("count"=> ($response['totalSubSupvExtRevocAdult']->count + $response['totalSubSupvExtRevocJICL']->count));


		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalSubSupvTransRevocJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalSubSupvTransRevocAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalSubSupvTransRevocAdultRPS'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Transfer"; 
		$response['totalSubSupvTransRevocAdultTPS'] = json_decode($this->callProcedure1($payload));
		$response['totalSubSupvTransRevocAdult'] =  (object)array("count"=> ($response['totalSubSupvTransRevocAdultPS']->count + $response['totalSubSupvTransRevocAdultTPS']->count + $response['totalSubSupvTransRevocAdultRPS']->count));


		$response['totalSubSupvTransRevocTotal'] = (object)array("count"=> ($response['totalSubSupvTransRevocJICL']->count + $response['totalSubSupvTransRevocAdult']->count));

		// Others - IV - E - 1 - E
		$payload->filter = "J"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalSubSupvOthersJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "PS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalSubSupvOthersAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalSubSupvOthersAdultRPS'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "TPS"; $payload->table = "F5T9"; $payload->filter_field = "disposed_decision"; $payload->filter_val = "Others"; 
		$response['totalSubSupvOthersAdultTPS'] = json_decode($this->callProcedure1($payload));
		
		$response['totalSubSupvOthersAdult'] =  (object)array("count"=> ($response['totalSubSupvOthersAdultPS']->count + $response['totalSubSupvOthersAdultRPS']->count + $response['totalSubSupvOthersAdultTPS']->count));


		$response['totalSubSupvOthersTotal'] = (object)array("count"=> ($response['totalSubSupvOthersJICL']->count + $response['totalSubSupvOthersAdult']->count));
		//


		$payload->filter = "J"; $payload->table = "F5T10"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['carryOverDispSupvJICL'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T10"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['carryOverDispSupvAdultTPS'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "PS"; $payload->table = "F5T10"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['carryOverDispSupvAdultPS'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "RPS"; $payload->table = "F5T10"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['carryOverDispSupvAdultRPS'] = json_decode($this->callProcedure1($payload));

		$response['carryOverDispSupvAdult'] = (object)array("count"=> ($response['carryOverDispSupvAdultTPS']->count + $response['carryOverDispSupvAdultPS']->count + $response['carryOverDispSupvAdultRPS']->count));

		$response['carryOverDispSupvTotal'] = (object)array("count"=> ($response['carryOverDispSupvJICL']->count + $response['carryOverDispSupvAdult']->count));
		
		$payload->filter = "J"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Term"; 
		$response['carryOverDispTermSupvJICL'] = json_decode($this->callProcedure1($payload));
		
		$payload->filter = "PS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Term"; 
		$response['carryOverDispTermSupvAdultPS'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "RPS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Term"; 
		$response['carryOverDispTermSupvAdultRPS'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "TPS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Term"; 
		$response['carryOverDispTermSupvAdultTPS'] = json_decode($this->callProcedure1($payload));

		$response['carryOverDispTermSupvAdult'] = (object)array("count"=> ($response['carryOverDispTermSupvAdultPS']->count + $response['carryOverDispTermSupvAdultTPS']->count + $response['carryOverDispTermSupvAdultRPS']->count));

		$response['carryOverDispTermSupvTotal'] = (object)array("count"=> ($response['carryOverDispTermSupvJICL']->count + $response['carryOverDispTermSupvAdult']->count));
		

		$payload->filter = "J"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Revoc"; 
		$response['carryOverDispRevocSupvJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "PS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Revoc"; 
		$response['carryOverDispRevocSupvAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Revoc"; 
		$response['carryOverDispRevocSupvAdultRPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "TPS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Revoc"; 
		$response['carryOverDispRevocSupvAdultTPS'] = json_decode($this->callProcedure1($payload));
		$response['carryOverDispRevocSupvAdult'] = (object)array("count"=> ($response['carryOverDispRevocSupvAdultPS']->count + $response['carryOverDispRevocSupvAdultTPS']->count+ $response['carryOverDispRevocSupvAdultRPS']->count));
		$response['carryOverDispRevocSupvTotal'] = (object)array("count"=> ($response['carryOverDispRevocSupvJICL']->count + $response['carryOverDispRevocSupvAdult']->count));
		

		$payload->filter = "J"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Ext"; 
		$response['carryOverDispExtSupvJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "PS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Ext"; 
		$response['carryOverDispExtSupvAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Ext"; 
		$response['carryOverDispExtSupvAdultRPS'] = json_decode($this->callProcedure1($payload));


		$payload->filter = "TPS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Ext"; 
		$response['carryOverDispExtSupvAdultTPS'] = json_decode($this->callProcedure1($payload));
		$response['carryOverDispExtSupvAdult'] = (object)array("count"=> ($response['carryOverDispExtSupvAdultPS']->count + $response['carryOverDispExtSupvAdultTPS']->count+ $response['carryOverDispExtSupvAdultRPS']->count));
		$response['carryOverDispExtSupvTotal'] = (object)array("count"=> ($response['carryOverDispExtSupvJICL']->count + $response['carryOverDispExtSupvAdult']->count));

		//@TRANS

		$payload->filter = "J"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Trans"; 
		$response['carryOverDispTransSupvJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "PS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Trans"; 
		$response['carryOverDispTransSupvAdultPS'] = json_decode($this->callProcedure1($payload));

		$payload->filter = "RPS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Trans"; 
		$response['carryOverDispTransSupvAdultRPS'] = json_decode($this->callProcedure1($payload));


		$payload->filter = "TPS"; $payload->table = "F5T10"; $payload->filter_field = "submitted_decision"; $payload->filter_val = "Trans"; 
		$response['carryOverDispTransSupvAdultTPS'] = json_decode($this->callProcedure1($payload));
		$response['carryOverDispTransSupvAdult'] = (object)array("count"=> ($response['carryOverDispTransSupvAdultPS']->count + $response['carryOverDispTransSupvAdultTPS']->count+ $response['carryOverDispTransSupvAdultRPS']->count));
		$response['carryOverDispTransSupvTotal'] = (object)array("count"=> ($response['carryOverDispTransSupvJICL']->count + $response['carryOverDispTransSupvAdult']->count));





		
		$response['tobeactedPSJICL'] = (object)array("count"=> ($response['carryOverDispSupvJICL']->count + $response['totalSubSupvJICL']->count));
		$response['tobeactedPSAdult'] = (object)array("count"=> ($response['carryOverDispSupvAdult']->count + $response['totalSubSupvAdult']->count));
		$response['tobeactedPSTotal'] = (object)array("count"=> ($response['tobeactedPSJICL']->count + $response['tobeactedPSAdult']->count));
		
		$response['totalPendingDispPSJICL'] = (object)array("count"=> ($response['tobeactedPSJICL']->count - $response['totalDrpSupvJICL']->count));
		$response['totalPendingDispPSAdult'] = (object)array("count"=> ($response['tobeactedPSAdult']->count - $response['totalDrpSupvAdult']->count));
		$response['totalPendingDispPSTotal'] = (object)array("count"=> ($response['totalPendingDispPSJICL']->count + $response['totalPendingDispPSAdult']->count));
		

		$payload->filter = "JCPS"; $payload->table = "F5T12"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['carryOverCPSJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "CPS"; $payload->table = "F5T12"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['carryOverCPSAdult'] = json_decode($this->callProcedure1($payload));
		$response['carryOverCPSTotal'] = (object)array("count"=> ($response['carryOverCPSJICL']->count + $response['carryOverCPSAdult']->count));
		

		$payload->filter = "JCPS"; $payload->table = "F5T13_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['rcvCPSJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "CPS"; $payload->table = "F5T13_RCV"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['rcvCPSAdult'] = json_decode($this->callProcedure1($payload));
		$response['rcvCPSTotal'] = (object)array("count"=> ($response['rcvCPSJICL']->count + $response['rcvCPSAdult']->count));
		

		$response['totalHandledCPSJICL'] = (object)array("count"=> ($response['carryOverCPSJICL']->count + $response['rcvCPSJICL']->count));
		$response['totalHandledCPSAdult'] = (object)array("count"=> ($response['carryOverCPSAdult']->count + $response['rcvCPSAdult']->count));
		$response['totalHandledCPSTotal'] = (object)array("count"=> ($response['totalHandledCPSJICL']->count + $response['totalHandledCPSAdult']->count));

		$payload->filter = "JCPS"; $payload->table = "F5T13_TERM"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['actCPSJICL'] = json_decode($this->callProcedure1($payload));
		$payload->filter = "CPS"; $payload->table = "F5T13_TERM"; $payload->filter_field = ""; $payload->filter_val = ""; 
		$response['actCPSAdult'] = json_decode($this->callProcedure1($payload));
		$response['actCPSTotal'] = (object)array("count"=> ($response['actCPSJICL']->count + $response['actCPSAdult']->count));
		

		$response['totalActiveCPSJICL'] = (object)array("count"=> ($response['totalHandledCPSJICL']->count - $response['actCPSJICL']->count));
		$response['totalActiveCPSAdult'] = (object)array("count"=> ($response['totalHandledCPSAdult']->count - $response['actCPSAdult']->count));
		$response['totalActiveCPSTotal'] = (object)array("count"=> ($response['totalActiveCPSJICL']->count + $response['totalActiveCPSAdult']->count));
		


		$response2 = array(
			'status' => 'SUCCESS',
			'payload' => $response
		);
		
		return json_encode($response2);

	}



	public function getTotalCarryInvestigation($payload,$option){
		if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL")
		{	
			$this->db->where('field_office', $payload->field_office);
		}
		if((isset($payload->Y_M) && !empty($payload->Y_M)) && (isset($payload->END_Y_M) && !empty($payload->END_Y_M)))
		{
			$this->db->where('Y_M >=', $payload->Y_M);
			$this->db->where('Y_M <=', $payload->END_Y_M);
		}else
		{
			$this->db->where('Y_M ', $payload->Y_M);
		}

		$this->db->LIKE('docket_no', $option, 'after');
		$this->db->where('status',1);
		$this->db->order_by("docket_no","asc");
		$sql = $this->db->get('F5T1');
		$response = $sql->num_rows();
		return $response;
	}

	public function getTotalReferralsReceived($payload,$option){
		if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL")
		{	
			$this->db->where('field_office', $payload->field_office);
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->where('Y_M', $payload->Y_M);
		}
		$this->db->LIKE('docket_no', $option, 'after');
		$this->db->where('status',1);
		$this->db->order_by("docket_no","asc");
		$sql = $this->db->get('F5T2_RCV');
		$response = $sql->num_rows();
		return $response;
	}

	public function getTotalReferralsActed($payload,$option){
		if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL")
		{	
			$this->db->where('field_office', $payload->field_office);
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->where('Y_M', $payload->Y_M);
		}
		$this->db->LIKE('docket_no', $option, 'after');
		$this->db->where('status',1);
		$this->db->order_by("docket_no","asc");
		$sql = $this->db->get('F5T2_ACTED');
		$response = $sql->num_rows();
		return $response;
	}

	public function getTotalReferralsActedReport($payload,$option,$decision,$manifest = 0,$transfer=0){
		if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL")
		{	
			$this->db->where('field_office', $payload->field_office);
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->where('Y_M', $payload->Y_M);
		}
		if($manifest == 1){
			$this->db->where('manifest_date is NOT NULL',NULL, FALSE);	
		}
		if($transfer == 1){
			$this->db->where('transfer_date is NOT NULL',NULL, FALSE);	
		}
		

		if($decision != ""){
			$this->db->LIKE('ppo_recommendation', $decision);
		}	

		$this->db->LIKE('docket_no', $option, 'after');
		$this->db->where('status',1);
		$this->db->order_by("docket_no","asc");
		$sql = $this->db->get('F5T2_ACTED');
		$response = $sql->num_rows();
		return $response;
	}



	public function getTotalReferralsNotActedReport($payload,$option,$decision){
		if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL")
		{	
			$this->db->where('field_office', $payload->field_office);
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->where('Y_M', $payload->Y_M);
		}
	
		
		
		if($decision != ""){
			$this->db->LIKE('disposed_decision', $decision);
		}	

		$this->db->LIKE('docket_no', $option, 'after');
		$this->db->where('status',1);
		$this->db->order_by("docket_no","asc");
		$sql = $this->db->get('F5T2_NOTACTED');
		$response = $sql->num_rows();
		return $response;
	}

	public function getTotalInvestigationCasesCourtIssued($payload,$option,$decision){
		if(isset($payload->field_office) && $payload->field_office != null && $payload->field_office != "ALL")
		{	
			$this->db->where('field_office', $payload->field_office);
		}
		if(isset($payload->Y_M) && !empty($payload->Y_M))
		{
			$this->db->where('Y_M', $payload->Y_M);
		}
	
		
		
		if($decision != ""){
			$this->db->LIKE('disposed_decision', $decision);
		}	

		$this->db->LIKE('docket_no', $option, 'after');
		$this->db->where('status',1);
		$this->db->order_by("docket_no","asc");
		$sql = $this->db->get('F5T4');
		$response = $sql->num_rows();
		return $response;
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

	    $query = $this->db->query("CALL F5CaseloadQuery1('".$start_date."','".$end_date."','".$field_office."','".$filter."','".$table."','".$filter_field."','".$filter_val."')");

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
	public function callProcedure1A($payload){
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
		#print_r($payload);

		$s = "CALL F5CaseloadQuery1_data('".$start_date."','".$end_date."','".$field_office."','".$filter."','".$table."','".$filter_field."','".$filter_val."');";
		echo $s;
	    $query = $this->db->query($s);

		if($query){
			if($query->num_rows() > 0){
				$data = $query->result();
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




	


	

}



?>