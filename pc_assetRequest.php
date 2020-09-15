<?php

use vbac\assetRequestRecord;
use vbac\personTable;
use itdq\AuditTable;

set_time_limit(0);
ob_start();
?>
<div class='container greyablePage'>
<div class='row'>
<div class='col-sm-4'>
<h2>Asset Request</h2>
</div>
<div class='col-sm-1 col-sm-offset-7'>
<button type='button' id='assetHelp' class='btn btn-info' >Help</button>
</div>


</div>

<div class='row'>
<?php
$mode = assetRequestRecord::$modeDEFINE;
$assetRequest = new assetRequestRecord();
$myCnum = personTable::myCnum();
if(!$myCnum){
    $assetRequest->unknownUser();
} else {
    $assetRequest->displayForm($mode);
}
// $assetRequest->education();
?>
</div>
<?php
$assetRequest->helpModal();
$assetRequest->doTheEducationModal();
$assetRequest->confirmEducationModal();
$assetRequest->ctIdRequiredModal();
$assetRequest->missingPrereqModal();
$assetRequest->saveFeedbackModal();

ob_flush();
$assetRequest->createJsCtidLookup();
?>

</div>
<script type="text/javascript">
<?php
if($myCnum) {
    ?>
    $(document).ready(function() {
    	$('[data-toggle="tooltip"]').tooltip();
    	$('.toggle').bootstrapToggle();
    	$('#requestees').select2({
        	width:'100%',
      		placeholder: 'Request For:',
    		allowClear: true});
    	$('#approvingManager').select2({
        	width:'100%',
      		placeholder: 'Approving Manager:',
    		allowClear: true});
    	$('.locationFor').select2({
        	width:'100%',
      		placeholder: 'Approved Location',
    		allowClear: true,
//     		ajax: {
//     		    url: '/ajax/select2Locations.php',
//     		    dataType: 'json'
//     		    }
	    });
     	var AssetRequest = new assetRequest();
     	AssetRequest.listenForSelectRequestee();
     	AssetRequest.listenForEnteringCtid();
     	AssetRequest.listenForSelectLocation();
     	AssetRequest.listenForSelectAsset();
     	AssetRequest.listenForSaveAssetRequest();
     	AssetRequest.listenForToggleReturnRequest();
     	AssetRequest.listenForClosingSaveFeedbackModal();
     	AssetRequest.listenForAddPrereq();
     	AssetRequest.listenForIgnorePrereq();
     	AssetRequest.listenForClosingPrereqModal();
     	AssetRequest.countCharsInTextarea();
    });
    <?php
}
?>


$('#assetHelp').on('click',function(){
	$('#assetHelpModal').modal('show');
});



</script>
