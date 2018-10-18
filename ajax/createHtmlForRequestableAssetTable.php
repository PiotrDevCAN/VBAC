<?php
use itdq\AuditTable;
use itdq\AuditRecord;
use vbac\requestableAssetListRecord;

set_time_limit(0);
ob_start();
$requestableAsset = new requestableAssetListRecord();
$headerCells = $requestableAsset->htmlHeaderCells();

ob_clean();
?>
<table id='requestableAssetTable' class='table table-striped table-bordered compact'   style='width:100%'>
<thead>
<tr><?=$headerCells;?></tr></thead>
<tbody>
</tbody>
<tfoot><tr><?=$headerCells;?></tr></tfoot></table>