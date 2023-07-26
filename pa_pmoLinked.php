<?php

use vbac\allTables;
use vbac\personRecord;
use vbac\personTable;

// $personRecord = new personRecord();
// $headerCells = $personRecord->htmlHeaderCells();

$personRecord = new personRecord();
$personTable = new personTable(allTables::$PERSON);
$headerCells = $personTable->headerRowForFullDatatable();

?>

<div class='container'>
<h1 id='portalTitle'>Linked Portal</h1>
<p>Records of Pre-Boarders and the Regulars they've been linked too</p>
</div>

<div class='container-fluid'>
<h3>Person Database</h3>

<button id='reportAction' 		class='btn btn-primary btn-sm '>Action Mode</button>
<button id='reportPes'    		class='btn btn-primary btn-sm '>PES Report</button>
<button id='reportAll'  		class='btn btn-primary btn-sm '>Show all Columns</button>
&nbsp;
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
<div id='personDatabaseDiv' class='portalDiv'>
<table id='personTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
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
$person->amendPesStatusModal();
$person->amendPesLevelModal();
$person->editAgileSquadModal();
include_once 'includes/modalEditPerson.html';
include_once 'includes/modalEditEmailAddress.html';
?>

<script type="text/javascript">

document.tableType = '<?=personTable::PORTAL_PRE_BOARDER_WITH_LINKED ?>';

</script>