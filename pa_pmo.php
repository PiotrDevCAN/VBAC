<?php
use vbac\personRecord;

?>
<div class='container'>
<h1 id='portalTitle'>Person Portal</h1>
</div>

<div class='container-fluid'>
<h3>Person Database</h3>

<button id='reportAction' 		class='btn btn-primary btn-sm '>Action Mode</button>
<button id='reportOffboarding' 	class='btn btn-primary btn-sm '>Offboarding Report</button>
<button id='reportPes'    		class='btn btn-primary btn-sm '>PES Report</button>
<button id='reportRevalidation' class='btn btn-primary btn-sm '>Revalidation Report</button>
<button id='reportAll'  		class='btn btn-primary btn-sm '>Show all Columns</button>
&nbsp;
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
<div id='personDatabaseDiv' class='portalDiv'>
</div>
</div>

<?php
$person = new personRecord();
$person->amendPesStatusModal();
$person->savingBoardingDetailsModal();
$person->editPersonModal();
$person->portalReportSaveModal();
$person->confirmChangeFmFlagModal();
$person->confirmOffboardingModal();
$person->confirmSendPesEmailModal();
?>

<script>
$(document).ready(function(){

	$('[data-toggle="tooltip"]').tooltip();

	
	var person = new personRecord();
	person.initialisePersonTable();
	person.listenForReportPes();
	person.listenForReportAction();
	person.listenForReportRevalidation();
	person.listenForReportOffboarding();
	person.listenForReportReset();
	person.listenForReportReload();
	person.listenForReportAll();
	person.listenForReportSave();
	person.listenForReportSaveConfirm();
	person.listenForEditPesStatus();
	person.listenForSavePesStatus();
	person.listenForInitiatePesFromPortal();
	person.listenForEditPerson();
	person.listenForAccountOrganisation();
    person.listenForCtbRtb();
	person.listenForToggleFmFlag();
	person.listenForConfirmFmFlag();
	person.listenforUpdateBoarding();
	person.listenForStopOffBoarding();
	person.listenForDeoffBoarding();
	person.listenForOffBoardingCompleted();
	person.listenForBtnOffboarding();
	person.listenForClearCtid();
	person.listenForSetPmoStatus();
	person.listenforSendPesEmail();
	person.listenforConfirmSendPesEmail();
	person.listenForbtnTogglePesTrackerStatusDetails();
	
});






</script>