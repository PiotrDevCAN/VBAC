<?php

use itdq\AuditTable;
use itdq\DbTable;

?>
<div class='container'>
<h1>vBAC Audit Trail<small>(last 31 days)</small></h1>
</div>

<div class='container-fluid'>

<div id='auditTableProgress'></div>


<h3>Audit Table</h3>

<?php
AuditTable::removeExpired();
DbTable::db2ErrorModal();
?>



<div id='auditDatabaseDiv'>
</div>
</div>


<script>
$(document).ready(function(){
	var audit = new auditRecord();
	audit.initialiseAuditTable();

});

</script>