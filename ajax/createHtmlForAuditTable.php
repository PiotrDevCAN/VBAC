<?php
use itdq\AuditTable;
use itdq\AuditRecord;

set_time_limit(0);
ob_start();
$auditRecord = new AuditRecord();
$headerCells = $auditRecord->htmlHeaderCells();

ob_clean();
?>
<table id='auditTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead>
<tr><?=$headerCells;?></tr></thead>
<tbody>
</tbody>
<tfoot><tr><?=$headerCells;?></tr></tfoot></table>
