<?php
?>
<div class='container'>
<h1>Revalidation Audit<small>(last 31 days)</small></h1>
</div>


<div class='container-fluid'>


<div id='revalidationAuditDiv'>
</div>
</div>


<script>
$(document).ready(function(){
	var audit = new auditRecord();
	audit.initialiseRevalidationAuditTable();

});
</script>