<?php

use vbac\allTables;
use vbac\cFIRSTPersonTable;

$table = new cFIRSTPersonTable(allTables::$CFIRST_PERSON);
$headerCells = $table->headerRowForDatatable();

?>
<div class='container'>
<h1 id='portalTitle'>Person Portal - cFIRST data</h1>
</div>

<div class='container-fluid'>
<h3>cFIRST Entries Database</h3>

<h4>Statuses: Completed, Cancelled, In Progress are reflected precisely</h4>
<h4>Other fFIRST profile statuses are shown as blank</h4>

<div id='personDatabaseDiv'   class='portalDiv'>
<table id='personTable'       class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<?=$headerCells;?>
</thead>
<tbody>
</tbody>
<tfoot>
<?=$headerCells;?>
</tfoot>
</table>
</div>
</div>

<script type="text/javascript">

document.isCdi = <?= $_SESSION['isCdi'] ? "'yes'" : "'no'";?>;
document.isFm = <?= $_SESSION['isFm'] ? "'yes'" : "'no'";?>;

</script>
<style>
  .toolTipDetails {
    width:  600px;
    max-width: 600px;
    overflow:auto;
  } 
  .toolTipDetails p {
      margin: 0;
      font-weight: bold;
  }
</style>