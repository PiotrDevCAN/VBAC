<?php

use vbac\AgileTribeRecord;
use itdq\FormClass;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<h2>Manage Tribe Records</h2>
<form id='tribeVersion' class="form-horizontal" method='post'>
    <div class="form-group">
    <div class="col-sm-offset-2 col-sm-4">
    <input  data-toggle="toggle" type="checkbox" class='toggle' data-width='100%' data-on="Current Tribes" data-off="Old Tribes" id='version' name='version' value='Original' data-onstyle='success' data-offstyle='warning' checked disabled>
    </div>
    </div>
</form>

<?php
$tribeRecord = new AgileTribeRecord();
$tribeRecord->displayForm(FormClass::$modeDEFINE, 'Original');
?>
</div>

<div class='container-fluid'>
<table id='tribeTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<tr><th>Tribe Number</th><th>Tribe Name</th><th>Tribe Leader</th><th>Iteration Mgr</th><th>Organisation</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Tribe Number</th><th>Tribe Name</th><th>Tribe Leader</th><th>Iteration Mgr</th><th>Organisation</th></tr>
</tfoot>
</table>
</div>

<?php
$tribeRecord->confirmDeleteTribeModal();
// include_once 'includes/modalConfirmDeleteTribe.html';
?>