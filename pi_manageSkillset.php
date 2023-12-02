<?php

use itdq\FormClass;
use vbac\skillSetRecord;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<h2>Manage Skillset Records</h2>
<?php
$setRecord = new skillSetRecord();
$setRecord->displayForm(FormClass::$modeDEFINE);
?>
</div>

<div class='container'>
<table id='skillSetTable' class='table table-striped table-bordered compact'  style='width:100%'>
<thead>
<tr><th>Id</th><th>Skillset</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Id</th><th>Skillset</th></tr>
</tfoot>
</table>
</div>

<?php
$setRecord->confirmDeleteSkillsetModal();
// include_once 'includes/modalConfirmDeleteSkillset.html';
?>