<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;

ini_set('MEMORY_LIMIT', '3072M');

$assetTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

$headerCells = assetRequestsTable::portalHeaderCells();

?>
<div class='container'>
<h1 id='portalTitle'>Asset Request Portal</h1>
</div>

<div class='container-fluid'>
<h3>Asset Requests</h3>

<div class='row'>
<button id='reportShowAll'  		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>All Requests&nbsp;<span class="badge" id='countAll'>**</span></button>
<button id='reportShowAwaitingIam'  class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Awaiting IAM&nbsp;<span class="badge" id='countAwaitingIam'>**</span></button>
<button id='reportShowExportable'   class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>PMO To Raise&nbsp;<span class="badge" id='countPmoForExport'>**</span></button>
<button id='reportShowExported'     class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Exported&nbsp;<span class="badge" id='countPmoExported'>**</span></button>
<button id='reportShowBauRaised'    class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Raised BAU&nbsp;<span class="badge" id='countBauRaised'>**</span></button>
<button id='reportShowNonBauRaised' class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Raised non-BAU&nbsp;<span class="badge" id='countNonBauRaised'>**</span></button>
<button id='reportShowUserRaised'   class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>User Raised&nbsp;<span class="badge" id='countNonPmoForExport'>**</span></button>
<button id='reportShowUid' 		    class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi accessUser'>Show UID</button>
&nbsp;
<button id='exportBauForOrderIt' 	class='btn btn-dark btn-sm accessBasedBtn accessPmo accessCdi'><span class="glyphicon glyphicon-envelope" >&nbsp;</span>Export BAU for LBG&nbsp;<span class="badge" id='countBauForExport'>**</span></button>
<button id='exportNonBauForOrderIt'	class='btn btn-dark btn-sm accessBasedBtn accessPmo accessCdi'><span class="glyphicon glyphicon-envelope" >&nbsp;</span>Export non-BAU for LBG&nbsp;<span class="badge" id='countNonBauExport'>**</span></button>
&nbsp;
<button id='mapVarbToOrderIt' 		class='btn btn-info btn-sm accessBasedBtn accessPmo accessCdi'><span class="glyphicon glyphicon-modal-window" >&nbsp;</span>Map to LBG Ref</button>
<button id='setOrderItStatus' 		class='btn btn-info btn-sm accessBasedBtn accessPmo accessCdi'><span class="glyphicon glyphicon-modal-window" >&nbsp;</span>Set LBG Status</button>
&nbsp;
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
</div>
<div class='row'>
<a class='btn btn-sm btn-link accessBasedBtn accessPmo accessCdi' href='/dn_tracker.php'><i class="glyphicon glyphicon-download-alt"></i> LBG Tracker</a>
<a class='btn btn-sm btn-link accessBasedBtn accessPmo accessCdi' href='/dn_varbTrackingReport.php'><i class="glyphicon glyphicon-download-alt"></i> Workflow Tracker</a>
</div>
<div class='row'>

</div>

<div id='assetRequestsDatatablesDiv' class='portalDiv'>
<table id='assetPortalTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<tr><?=$headerCells;?></tr>
</thead>
<tbody>
</tbody>
<tfoot><tr><?=$headerCells;?></tr></tfoot>
</table>
</div>
</div>

<?php

$assetTable->exportResultsModal();
$assetTable->editUidModal();
$assetTable->mapVarbToOrderItModal();
$assetTable->setOitStatusModal();
$assetTable->approveRejectModal();
$assetTable->confirmReturnedModal();
$assetTable->justificationEditModal();
$assetTable->amendOrderItModal();

$isFm   = $_SESSION['isFm']   ? ".not('.accessFm')"   : null;
$isCdi  = $_SESSION['isCdi']  ? ".not('.accessCdi')"  : null;
$isPmo  = $_SESSION['isPmo']  ? ".not('.accessPmo')"  : null;
$isPes  = $_SESSION['isPes']  ? ".not('.accessPes')"  : null;
$isUser = $_SESSION['isUser'] ? ".not('.accessUser')" : null;
$myCnum = personTable::myCnum();

?>
<input type='hidden' id='isPmo' value='<?=$_SESSION['isPmo']?>' />
<input type='hidden' id='isFm' value='<?=$_SESSION['isFm']?>' />
<input type='hidden' id='myCnum' value='<?=$myCnum?>' />

<script type="text/javascript">

var dateReturned;
var returnedPicker;

$(document).ready(function(){
	$('.accessBasedBtn')<?=$isFm?><?=$isPmo?><?=$isCdi?><?=$isUser?>.remove();
});

</script>