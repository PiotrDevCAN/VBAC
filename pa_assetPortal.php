<?php
use vbac\assetRequestsTable;
use vbac\allTables;
use vbac\personTable;

?>
<div class='container'>
<h1 id='portalTitle'>Asset Request Portal</h1>
</div>

<div class='container-fluid'>
<h3>Asset Requests</h3>

<button id='reportShowAll'  		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Show All Requests</button>
<button id='reportShowExportable'   class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Show Exportable Requests</button>
<button id='exportForOrderIt' 		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Export for Order IT</button>
<button id='mapVarbToOrderIt' 		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Map vARB to Order IT</button>
<button id='reportShowUid' 		    class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Show UID</button>
&nbsp;
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
<div id='assetRequestsDatatablesDiv' class='portalDiv'>
</div>
</div>


<?php

$assetTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$assetTable->exportResultsModal();
$assetTable->editUidModal();
$assetTable->mapVarbToOrderItModal();
$assetTable->approveRejectModal();

$_SESSION['isPmo'] = true;
$_SESSION['isCdi']  = false;
$_SESSION['isFm'] = false;
$_SESSION['isUser'] = false;
$_SESSION['isPes'] = false;

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
$(document).ready(function(){

	$('.accessBasedBtn')<?=$isFm?><?=$isPmo?><?=$isCdi?><?=$isUser?>.remove();
	
	var AssetPortal = new assetPortal();
	AssetPortal.initialiseAssetRequestPortal();
 	AssetPortal.listenForExportButton();
 	AssetPortal.listenForMapVarbButton();
 	AssetPortal.listenForMapVarbModalShown();
// 	AssetPortal.listenForSelectedVarbForMapping();
 	AssetPortal.listenForReportReset();
 	AssetPortal.listenForReportReload();
 	AssetPortal.listenForReportShowAll();
 	AssetPortal.listenForReportShowExportable();
 	AssetPortal.listenForReportShowUid();
 	AssetPortal.listenForEditUid();
 	AssetPortal.listenForSaveEditUid();
 	AssetPortal.listenForSaveMapping();
 	AssetPortal.listenForAssetRequestApprove();
 	AssetPortal.listenForAssetRequestReject();
 	AssetPortal.listenForAssetRequestApproveRejectToggle();
 	AssetPortal.listenForAssetRequestApproveRejectConfirm();
});

</script>