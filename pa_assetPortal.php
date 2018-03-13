<?php
use vbac\assetRequestsTable;
use vbac\allTables;

?>
<div class='container'>
<h1 id='portalTitle'>Asset Request Portal</h1>
</div>

<div class='container-fluid'>
<h3>Asset Requests</h3>

<button id='exportForOrderIt' 		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Export for Order IT</button>
<button id='mapVarbToOrderIt' 		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Map vARB to Order IT</button>
&nbsp;
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
<div id='assetRequestsDatatablesDiv' class='portalDiv'>
</div>
</div>


<?php

$assetTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$assetTable->exportResultsModal();
$assetTable->mapVarbToOrderIt();

$isFm   = $_SESSION['isFm']   ? ".not('.accessFm')"   : null;
$isCdi  = $_SESSION['isCdi']  ? ".not('.accessCdi')"  : null;
$isPmo  = $_SESSION['isPmo']  ? ".not('.accessPmo')"  : null;
$isPes  = $_SESSION['isPes']  ? ".not('.accessPes')"  : null;
$isUser = $_SESSION['isUser'] ? ".not('.accessUser')" : null;
?>

<script>
$(document).ready(function(){

	$('.accessBasedBtn')<?=$isFm?><?=$isPmo?><?=$isCdi?><?=$isUser?>.remove();
	
	var AssetPortal = new assetPortal();
	AssetPortal.initialiseAssetRequestPortal();
 	AssetPortal.listenForExportButton();
 	AssetPortal.listenForMapVarbButton();
 	AssetPortal.listenForReportReset();
 	AssetPortal.listenForReportReload();
});

</script>