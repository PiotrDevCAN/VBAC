<?php
use vbac\personPortalLiteRecord;
use vbac\personPortalLiteTable;
use vbac\allTables;
use vbac\personTable;

$personRecord = new personPortalLiteRecord();
$personTable = new personPortalLiteTable(allTables::$PERSON_PORTAL_LITE);
$headerCells = $personTable->headerRowForDatatable();

?>
<div class='container'>
<h1 id='portalTitle'>Person Portal - Lite</h1>
</div>

<div class='container-fluid'>
<h3>Person Database</h3>

<button id='reportAction' 		class='btn btn-primary btn-sm '>Action Mode</button>
<!-- <button id='reportOffboarding' 	class='btn btn-primary btn-sm '>Offboarding Report</button> -->
<!-- <button id='reportOffboarded' 	class='btn btn-primary btn-sm '>Offboarded Report</button> -->
<button id='reportPes'    		class='btn btn-primary btn-sm '>PES Report</button>
<button id='reportRevalidation' class='btn btn-primary btn-sm '>Revalidation Report</button>
<button id='reportMgrsCbn'      class='btn btn-primary btn-sm '>Mgrs CBN Report</button>
<button id='reportSquads'       class='btn btn-primary btn-sm '>Squad Details</button>
<button id='reportAll'  		class='btn btn-primary btn-sm '>Show all Columns</button>
&nbsp;
<button id='reportRemoveOffb' class='btn btn-warning btn-sm '>Hide Offboarding</button>
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
<div id='personDatabaseDiv' class='portalDiv'>
<table id='personTable' class='table table-striped table-bordered compact'   style='width:100%'>
<thead>
<?=$headerCells;?></thead>
<tbody>
</tbody>
<tfoot><?=$headerCells;?></tfoot>
</table>
</div>
</div>

<?php
$person = new personPortalLiteRecord();
$person->amendPesStatusModal();
$person->savingBoardingDetailsModal();
$person->editPersonModal();
$person->editAgileSquadModal();
$person->portalReportSaveModal();
$person->confirmChangeFmFlagModal();
$person->confirmOffboardingModal();
$person->confirmSendPesEmailModal();
?>

<script>

$(document).ready(function(){

	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="popover"]').popover();

	var person = new personRecord();
	var PersonPortalLite = new personPortalLite();
	PersonPortalLite.initialiseDataTable('<?=personTable::PORTAL_ONLY_ACTIVE ?>');
	PersonPortalLite.listenForReportPes();
	PersonPortalLite.listenForReportAction();
	PersonPortalLite.listenForReportRevalidation();
	PersonPortalLite.listenForReportOffboarding();
	PersonPortalLite.listenForReportOffboarded();
	PersonPortalLite.listenForReportMgrsCbn();
	PersonPortalLite.listenForReportSquads();
	PersonPortalLite.listenForReportRemoveOffb();
	PersonPortalLite.listenForReportReset();
	PersonPortalLite.listenForReportReload();
	PersonPortalLite.listenForReportAll();
	person.listenForReportSave();
	person.listenForReportSaveConfirm();
	person.listenForEditPesStatus();
	person.listenForSavePesStatus();
	person.listenForEditAgileNumber();
	person.listenForSelectAgileNumber();
	person.listenForSaveAgileNumber();
	person.listenForClearAgileNumber();
	person.listenForInitiatePesFromPortal();
	person.listenForEditPerson(<?=$_SESSION['isFm'] ? "'yes'" : "'no'";?>);
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
	person.listenForStopPes();
	person.listenForChangeFm();
	person.listenForResetChangeFm();
	person.listenForConfirmChangeFm();

});


</script>