<?php

use itdq\FormClass;
use vbac\allTables;
use vbac\personRecord;
use vbac\personSquadRecord;
use vbac\personSquadTable;

$table = new personSquadTable(allTables::$EMPLOYEE_AGILE_MAPPING);
$headerCells = $table->headerRowForDatatable();

?>
<div class='container'>
<h1 id='portalTitle'>Agile Tribes/Squads assignment management</h1>

<h4>An individual can have only one assignment to Primary Tribe/Squad.</h4>
<h4>Several assignments to Secondary Tribes/Squads are allowed.</h4>

<?php
$setRecord = new personSquadRecord();
$setRecord->displayForm(FormClass::$modeDEFINE);
?>
</div>

<div class='container-fluid'>
<h3>Person Database</h3>

<div id='personDatabaseDiv' class='portalDiv'>
<table id='personTable'     class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
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

<?php
$setRecord->confirmDeleteSquadAssignmentModal();
// include_once 'includes/modalConfirmDeleteSquadAssignmentt.html';
?>

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