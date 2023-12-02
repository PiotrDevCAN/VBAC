<?php

use itdq\FormClass;
use vbac\bandMappingRecord;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<h2>Manage Business Title to Band Assignment</h2>
<?php
$setRecord = new bandMappingRecord();
$setRecord->displayForm(FormClass::$modeDEFINE);
?>
</div>

<div class='container'>
<table id='bandMappingTable' class='table table-striped table-bordered compact'  style='width:100%'>
<thead>
<tr><th>Business Title</th><th>Band</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Business Title</th><th>Band</th></tr>
</tfoot>
</table>
</div>

<?php
$setRecord->confirmDeleteBandMappingModal();
// include_once 'includes/modalConfirmDeleteBandMapping.html';
?>