<?php

?>
<div class='container'>
<h1>vBAC Audit Trail</h1>
</div>

<div class='container-fluid'>
<h3>Audit Table</h3>

<div id='auditDatabaseDiv'>
</div>
</div>


<script>
$(document).ready(function(){
	var audit = new auditRecord();
	audit.initialiseAuditTable();

});
</script>