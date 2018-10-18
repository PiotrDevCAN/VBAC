<?php
use vbac\assetRequestRecord;
use vbac\assetRequestsTable;

ob_start();
$headerCells = assetRequestsTable::portalHeaderCells();

ob_clean();
?>
<table id='assetPortalTable' class='table table-striped table-bordered compact'  style='width:100%'>
<thead>
<tr><?=$headerCells;?></tr></thead>
<tbody>
</tbody>
<tfoot><tr><?=$headerCells;?></tr></tfoot></table>