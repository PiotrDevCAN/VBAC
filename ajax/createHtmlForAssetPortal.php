<?php
use vbac\assetRequestRecord;
use vbac\assetRequestsTable;

ob_start();
$headerCells = assetRequestsTable::portalHeaderCells();

ob_clean();
?>
<table id='assetPortalTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<tr><?=$headerCells;?></tr></thead>
<tbody>
</tbody>
<tfoot><tr><?=$headerCells;?></tr></tfoot></table>