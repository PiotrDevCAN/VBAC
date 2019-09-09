<?php
use vbac\personRecord;
use vbac\personWithSubPRecord;
use vbac\staticDataSubPlatformTable;
use vbac\personTable;

$personRecord = new personWithSubPRecord();
$headerCells = $personRecord->htmlHeaderCells();

staticDataSubPlatformTable::prepareJsonObjectForSubPlatformSelect();

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
<button id='reportRemoveOffb' class='btn btn-warning btn-sm '>Hide Offboarding</button>
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

function changeSubplatform(dataCategory){
    $("#subPlatform").select2({
        data:dataCategory,
        placeholder:'Select'
    })
    .attr('disabled',false)
    .attr('required',true);

};

$(document).on( "change", '#work_stream', function(e){
    console.log('changing workstream');
	console.log(e);
	console.log($('.accountOrganisation:checked').val());
	console.log($('#subPlatform').parents('.storeSelections').data('selections'));

	if($('.accountOrganisation:checked').val()=='BAU'){
       	var workstream = $('#work_stream').val();
   		var workstreamId = workstreamDetails[workstream];
   		if($('#subPlatform').data('select2')){
   		   	$("#subPlatform").select2("destroy");
   			$("#subPlatform").html("<option><option>");
   			changeSubplatform( platformWithinStream[workstreamId] );
   			var selections = $('#subPlatform').parents('.storeSelections').data('selections');
   			$("#subPlatform").val(selections).trigger('change');
   		}
	} else {
       $("#subPlatform").select2({
       placeholder:'Select'
   		})
   		.val('')
   		.trigger('change')
   		.attr('disabled',true)
   		.attr('required',false);
	}

});




$(document).ready(function(){

	$('[data-toggle="tooltip"]').tooltip();




	var person = new personRecord();
	var personWithSubP = new personWithSubPRecord();
	personWithSubP.initialiseDataTable('<?=personTable::PORTAL_ONLY_ACTIVE ?>');

//	person.initialiseDataTable();
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