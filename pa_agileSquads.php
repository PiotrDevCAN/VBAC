<?php

use vbac\AgileSquadRecord;
use itdq\FormClass;
use vbac\AgileSquadTable;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<h2>Manage Squad Records (Current)</h2>
<form id='tribeVersion' class="form-horizontal" method='post'>
    <div class="form-group">
    <div class="col-sm-offset-2 col-sm-4">
    <input disabled data-toggle="toggle" type="checkbox" class='toggle' data-width='100%' data-on="Current Squads" data-off="Old Squads" id='version' name='version' value='Original' data-onstyle='success' data-offstyle='warning' checked>
    </div>
    </div>
</form>
<div id='squadDisplayForm'>
<?php
$tribeRecord = new AgileSquadRecord();
$tribeRecord->setTribeOrganisation('Original');
$tribeRecord->displayForm(FormClass::$modeDEFINE);
?>
</div>
</div>

<div class='container-fluid'>
<table id='squadTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<tr><th>Squad Number</th><th>Squad Name</th><th>Squad Leader</th><th>Tribe Number</th><th>Tribe Name</th><th>Organisation</th><th>Shift</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Squad Number</th><th>Squad Name</th><th>Squad Leader</th><th>Tribe Number</th><th>Tribe Name</th><th>Organisation</th><th>Shift</th></tr>
</tfoot>
</table>
</div>

<?=AgileSquadTable::buildTribeSelects_NEW();?>