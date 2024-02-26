<?php

use vbac\allTables;
use vbac\personRecord;
use vbac\personSquadCrosscheckReport;

$table = new personSquadCrosscheckReport(allTables::$PERSON);
$headerCells = $table->headerRowForDatatable();

?>
<div class='container'>
<h1 id='portalTitle'>Agile Tribes/Squads assignment crosscheck</h1>
</div>

<div class='container-fluid'>
<h3>Person Database</h3>

<button id='reportAction' 		    class='btn btn-primary btn-sm '>Action Mode</button>
<button id='reportAll'  		      class='btn btn-primary btn-sm '>Show all Columns</button>
&nbsp;
<button id='reportRemoveAssigned' class='btn btn-warning btn-sm '>Hide Assigned</button>
<button id='reportReload'  		    class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		    class='btn btn-warning btn-sm '>Reset</button>
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
$person = new personRecord();
$person->editAgileSquadModal();
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