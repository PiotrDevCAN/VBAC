<?phpuse vbac\personTable;
use vbac\allTables;use vbac\personRecord;
set_time_limit(0);
ob_start();
$personRecord = new personRecord();
$headerCells = $personRecord->htmlHeaderCells();
ob_clean();?>
<table id='personTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead>
<tr><?=$headerCells;?></tr></thead>
<tbody>
</tbody>
<tfoot><tr><?=$headerCells;?></tr></tfoot></table>