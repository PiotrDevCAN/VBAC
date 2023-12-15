<?php

use itdq\Trace;
use vbac\personRecord;

Trace::pageOpening($_SERVER['PHP_SELF']);

?>
<div class="container">
<div class='row'>
<div class='col-sm-6'>
<h1 id='portalTitle'>Manual Status Override</h1>
<p><b>Not Requested</b> and <b>Initiated</b> colleagues shown only!</p>
<p>New PES Status options limited to <b>PES Progressing</b> only!</p>
</div>
</div>

<?php
$mode = personRecord::$modeDEFINE;
$person = new personRecord();
$person->displayStatusUpdateForm($mode);
?>
</div>

<hr/>

<div class='container-fluid'>
<h3>Person Database</h3>
<button id='reportPesUpdate' class='btn btn-primary btn-sm '>Update PES Status</button>
&nbsp;
<button id='reportReload' class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset' class='btn btn-warning btn-sm '>Reset</button>
<div id='personDatabaseDiv' class='portalDiv'>
<table id='updateStatusTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<tr><th>CNUM</th><th>Worker ID</th><th>First Name</th><th>Last Name</th><th>Email Address</th><th>Kyn Email Address</th><th>PES Status</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>CNUM</th><th>Worker ID</th><th>First Name</th><th>Last Name</th><th>Email Address</th><th>Kyn Email Address</th><th>PES Status</th></tr>
</tfoot>
</table>
</div>
</div>

<?php
include_once 'includes/modalShowUpdateResult.html';
Trace::pageLoadComplete($_SERVER['PHP_SELF']);