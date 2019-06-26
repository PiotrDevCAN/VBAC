<?php
use vbac\personRecord;

$personRecord = new personRecord();
$headerCells = $personRecord->htmlHeaderCells();


?>
<div class='container'>
<h1 id='portalTitle'>Person Portal</h1>
</div>

<div class='container-fluid'>
<h3>Person Database</h3>

<button id='reportAction' 		class='btn btn-primary btn-sm '>Action Mode</button>
<button id='reportOffboarding' 	class='btn btn-primary btn-sm '>Offboarding Report</button>
<button id='reportOffboarded' 	class='btn btn-primary btn-sm '>Offboarded Report</button>
<button id='reportPes'    		class='btn btn-primary btn-sm '>PES Report</button>
<button id='reportRevalidation' class='btn btn-primary btn-sm '>Revalidation Report</button>
<button id='reportMgrsCbn'      class='btn btn-primary btn-sm '>Mgrs CBN Report</button>
<button id='reportAll'  		class='btn btn-primary btn-sm '>Show all Columns</button>
&nbsp;
<button id='reportRemoveOffb' class='btn btn-warning btn-sm '>Hide Offboarded/ing</button>
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
<div id='personDatabaseDiv' class='portalDiv'>
<table id='personTable' class='table table-striped table-bordered compact'   style='width:100%'>
<thead>
<tr><?=$headerCells;?></tr></thead>
<tbody>
</tbody>
<tfoot><tr><?=$headerCells;?></tr>
</tfoot>
</table>
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
	person.initialiseDataTable();
	person.listenForReportPes();
	person.listenForReportAction();
	person.listenForReportRevalidation();
	person.listenForReportOffboarding();
	person.listenForReportOffboarded();
	person.listenForReportMgrsCbn();
	person.listenForReportRemoveOffb();
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
//	person.listenForCancelPes(); Don't let them cancel 2018/12/19

<?php
if(isset($_GET['open'])){
   ?>
   $(document).on('init.dt',function(){
	   $('#footerNOTESID').val('<?=trim($_GET['open']);?>').trigger('change');
	   console.log($('.btnEditPerson'));
	   $('.btnEditPerson').trigger('click');
   });
   <?php
}
?>


});
</script>