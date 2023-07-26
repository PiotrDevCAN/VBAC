<?php
use vbac\requestableAssetListRecord;
use itdq\FormClass;

?>

<div class='container greyablePage'>
<h1>Requestable Assets</h1>
</div>

<div class='container' >
<h3>Requestable Asset Form</h3>
<div id='requestableAssetFormDiv'>
<?php
$requestableAssetListRecord = new requestableAssetListRecord();
$requestableAssetListRecord->displayForm(FormClass::$modeDEFINE);

$headerCells = $requestableAssetListRecord->htmlHeaderCells();

?>
</div>
</div>

<div class='container-fluid'>
<h3>Requestable Asset List</h3>
<div id='requestableAssetListDiv'>
<table id='requestableAssetTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<tr><?=$headerCells;?></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><?=$headerCells;?></tr>
</tfoot>
</table>
</div>

</div>