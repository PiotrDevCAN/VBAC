<?php

use itdq\AuditTable;
use itdq\DbTable;

?>
<div class='container'>
<h1>Squad-A-Log</h1>
</div>

<div class='container-fluid'>

<h3>Squad-a-log</h3>

<table id='squadalog' class='table table-stripped table-responseive'>
<thead>
<tr><th>CNUM</th><th>Notes Id</th><th>JRSS</th><th>Squad Type</th><th>Tribe</th><th>Shift</th><th>Squad Leader</th><th>FLL</th><th>SLL</th><th>Squad Number</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>CNUM</th><th>Notes Id</th><th>JRSS</th><th>Squad Type</th><th>Tribe</th><th>Shift</th><th>Squad Leader</th><th>FLL</th><th>SLL</th><th>Squad Number</th></tr>
</tfoot>
</table>
</div>
<script>
$(document).ready(function(){
	var person = new personRecord();
	person.initialiseSquadALog();

});

</script>