<?php
use vbac\requestableAssetListRecord;
use itdq\FormClass;

?>

<div class='container'>
<h1>Requestable Assets</h1>
</div>

<div class='container' >
<h3>Requestable Asset Form</h3>
<div id='requestableAssetFormDiv'>
<?php
$requestableAssetListRecord = new requestableAssetListRecord();
$requestableAssetListRecord->displayForm(FormClass::$modeDEFINE);
?>
</div>
</div>


<div class='container-fluid'>
<h3>Requestable Asset List</h3>
<div id='requestableAssetListDiv'>
</div>

</div>


<script>
$( document ).ready(function() {
	var RequestableAsset = new requestableAsset();
	    RequestableAsset.initialiseTable();
	    RequestableAsset.initialiseSelect2();
	    RequestableAsset.listenForSaveRequestableAsset();
	    RequestableAsset.listenForEditButton();
	    RequestableAsset.listenForDeleteButton();
	    RequestableAsset.listenForJustificationButton();
});
</script>
