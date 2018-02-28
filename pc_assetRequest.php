<?php

use vbac\assetRequestRecord;
use vbac\personTable;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<div class='row'>
<h2>Asset Request</h2>
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
$assetRequest->education();
?>
</div>
<?php
$assetRequest->education();
$assetRequest->confirmEducationModal();
$assetRequest->ctIdRequiredModal();
$assetRequest->missingPrereqModal();

ob_flush();
$assetRequest->createJsCtidLookup();
?>

<script type="text/javascript">
<?php
if($myCnum) {
    ?>
    $(document).ready(function() {
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
     	AssetRequest.showEducationConfirmationModal(); // been tweaked to use the Requestees NOTESID
     	AssetRequest.listenForEducationConfirmation();
     	AssetRequest.listenForNoEducation();
     	AssetRequest.listenForSelectRequestee();
     	AssetRequest.listenForEnteringCtid();
     	AssetRequest.listenForSelectLocation();
     	AssetRequest.listenForSelectAsset();
     	AssetRequest.listenForSaveAssetRequest();
     	AssetRequest.listenForAddPrereq();
     	AssetRequest.listenForIgnorePrereq();
     	AssetRequest.listenForClosingPrereqModal();
     	AssetRequest.countCharsInTextarea();
    });
    <?php
}
?>
</script>
