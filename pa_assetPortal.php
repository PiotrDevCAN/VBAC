<?php


?>
<div class='container'>
<h1>Asset Request Portal</h1>
</div>

<div class='container-fluid'>
<h3>Asset Requests</h3>

<div id='assetRequestsDatatablesDiv'>
</div>

</div>

<?php



?>

<script>
$(document).ready(function(){
	var AssetPortal = new assetPortal();
	AssetPortal.initialiseAssetRequestPortal();
});

</script>