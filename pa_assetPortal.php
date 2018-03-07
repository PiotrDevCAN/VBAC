<?php
?>
<div class='container'>
<h1 id='portalTitle'>Asset Request Portal</h1>
</div>

<div class='container-fluid'>
<h3>Asset Requests</h3>

<button id='exportForOrderIt' 		class='btn btn-primary btn-sm '>Export for Order IT</button>
<button id='updateWithOrderIt' 		class='btn btn-primary btn-sm '>Update with Order IT</button>
&nbsp;
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
<div id='assetRequestsDatatablesDiv' class='portalDiv'>
</div>
</div>


<?php



?>

<script>
$(document).ready(function(){

	var person = new personRecord();
	person.initialisePersonTable();

	
	var AssetPortal = new assetPortal();
	AssetPortal.initialiseAssetRequestPortal();
// 	AssetPortal.listenForExportButton();
// 	AssetPortal.listenForReportReset();
// 	AssetPortal.listenForReportReload();
});

</script>