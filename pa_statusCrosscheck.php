<?php

use vbac\allTables;
use vbac\personStatusCrosscheckReport;

$table = new personStatusCrosscheckReport(allTables::$PERSON);
$headerCells = $table->headerRowForDatatable();

?>
<div class='container'>
<h1 id='portalTitle'>vBAC and cFIRST statuses crosscheck</h1>
</div>

<div class='container-fluid'>
<h3>Entries Database</h3>

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