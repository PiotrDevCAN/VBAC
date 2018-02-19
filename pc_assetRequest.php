<?php

use vbac\assetRequestRecord;
use vbac\personTable;

set_time_limit(0);
?>

<div class='container'>
<div class='row'>
<!-- <div class='col-sm-2'></div> -->
<!-- <div class='col-sm-8'> -->
<h2>Asset Request</h2>
</div>
<!-- </div> -->


<div class='row'>
<!-- <div class='col-sm-2'></div> -->
<!-- <div class='col-sm-8'> -->
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
<!-- </div> -->
<!-- <div class='col-sm-2'></div> -->
<!-- </div> -->
</div>
<?php
$assetRequest->educationModal();
$assetRequest->ctIdRequiredModal()
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
    	$('.locationFor').select2({
        	width:'100%',
      		placeholder: 'Location',
    		allowClear: true,
//     		ajax: {
//     		    url: '/ajax/select2Locations.php',
//     		    dataType: 'json'
//     		    }
	    });
     	var AssetRequest = new assetRequest();
     	AssetRequest.showEducationConfirmationModal();
     	AssetRequest.listenForEducationConfirmation();
     	AssetRequest.listenForNoEducation();
     	AssetRequest.listenForSelectRequestee();
     	AssetRequest.listenForEnteringCtid();
     	AssetRequest.listenForSelectLocation();
     	AssetRequest.countCharsInTextarea();
    });
    <?php
}
?>
</script>
