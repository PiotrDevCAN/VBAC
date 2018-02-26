<?php
use vbac\assetRequestRecord;

ob_start();
$assetRequest = new assetRequestRecord();
$headerCells = $assetRequest->htmlHeaderCells();

ob_clean();
?>
<table id='assetPortal' class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead>
<tr><?=$headerCells;?></tr></thead>
<tbody>
</tbody>
<tfoot><tr><?=$headerCells;?></tr></tfoot></table>