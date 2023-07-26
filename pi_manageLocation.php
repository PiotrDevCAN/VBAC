<?php

use itdq\FormClass;
use vbac\workLocationRecord;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<h2>Manage Work Location Records</h2>
<?php
$locationRecord = new workLocationRecord();
$locationRecord->displayForm(FormClass::$modeDEFINE);
?>
</div>

<div class='container'>
<table id='workLocationTable' class='table table-striped table-bordered compact'  style='width:100%'>
<thead>
<tr><th>Country</th><th>City</th><th>Address</th><th>On Shore</th><th>CBC In Place</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Country</th><th>City</th><th>Address</th><th>On Shore</th><th>CBC In Place</th></tr>
</tfoot>
</table>
</div>