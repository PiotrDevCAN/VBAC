<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;

$assetTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
?>
<div class='container'>
<h1 id='portalTitle'>Asset Request Portal</h1>
</div>

<div class='container-fluid'>
<h3>Asset Requests</h3>

<div class='row'>
<button id='reportShowAll'  		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Show All Requests</button>
<button id='reportShowExportable'   class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Show PMO To Raise&nbsp;<span class="badge" id='countPmoForExport'>**</span></button>
<button id='reportShowUserRaised'   class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Show User Raised&nbsp;<span class="badge" id='countNonPmoForExport'>**</span></button>
<button id='exportBauForOrderIt' 	class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Export BAU for Order IT&nbsp;<span class="badge" id='countBauForExport'>**</span></button>
<button id='exportNonBauForOrderIt'	class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Export non-BAU for Order IT&nbsp;<span class="badge" id='countNonBauExport'>**</span></button>
<button id='mapVarbToOrderIt' 		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Map vARB to Order IT</button>
<button id='setOrderItStatus' 		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Set Order IT Status</button>
<button id='reportShowUid' 		    class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi accessUser'>Show UID</button>
&nbsp;
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
&nbsp;
<a class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi' href='/dn_tracker.php'><i class="glyphicon glyphicon-download-alt"></i> Tracker</a>
<a class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi' href='/dn_assetRequestExtract.php'><i class="glyphicon glyphicon-download-alt"></i> Full Extract</a>
</div>
<div class='row'>

</div>

<div id='assetRequestsDatatablesDiv' class='portalDiv'>
</div>
</div>


<?php

$assetTable->exportResultsModal();
$assetTable->editUidModal();
$assetTable->mapVarbToOrderItModal();
$assetTable->setOitStatusModal();
$assetTable->approveRejectModal();
$assetTable->confirmReturnedModal();

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

<script>

var dateReturned;
var returnedPicker;


$(document).ready(function(){

	$('.accessBasedBtn')<?=$isFm?><?=$isPmo?><?=$isCdi?><?=$isUser?>.remove();

	var AssetPortal = new assetPortal();
	AssetPortal.initialiseAssetRequestPortal();

 	AssetPortal.listenForExportBauButton();
 	AssetPortal.listenForExportNonBauButton();
 	AssetPortal.listenForMapVarbButton();
 	AssetPortal.listenForDeVarbButton();
 	AssetPortal.listenForMapVarbModalShown();
 	AssetPortal.listenForSetOitStatusButton();
 	AssetPortal.listenForSetOitStatusModalShown();

 	AssetPortal.listenForAssetReturned();
	AssetPortal.listenForConfirmedAssetReturnedModalShown();
 	AssetPortal.listenForConfirmedAssetReturned();

 	AssetPortal.listenForReportReset();
 	AssetPortal.listenForReportReload();
 	AssetPortal.listenForReportShowAll();
 	AssetPortal.listenForReportShowExportable();
 	AssetPortal.listenForReportShowUserRaised();
 	AssetPortal.listenForReportShowUid();
 	AssetPortal.listenForEditUid();
 	AssetPortal.listenForSaveEditUid();
 	AssetPortal.listenForSaveMapping();
 	AssetPortal.listenForSaveOrderItStatus();
 	AssetPortal.listenForAssetRequestApprove();
 	AssetPortal.listenForAssetRequestReject();
 	AssetPortal.listenForAssetRequestApproveRejectToggle();
 	AssetPortal.listenForAssetRequestApproveRejectConfirm();
});

</script>