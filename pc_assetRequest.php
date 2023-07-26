<?php

use vbac\assetRequestRecord;
use vbac\personTable;

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
include_once 'includes/modalAssetHelp.html';
include_once 'includes/modalConfirmEducation.html';
include_once 'includes/modalDoTheEducation.html';
include_once 'includes/modalMissingPrereq.html';
include_once 'includes/modalObtainCtid.html';
include_once 'includes/modalSaveFeedback.html';

ob_flush();
$assetRequest->createJsCtidLookup();
?>

</div>
<script type="text/javascript">

document.myCnum = '<?= $myCnum;?>';

$('#assetHelp').on('click',function(){
	$('#assetHelpModal').modal('show');
});

</script>
