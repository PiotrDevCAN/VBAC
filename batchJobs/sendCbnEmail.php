<?php
use vbac\personRecord;
use itdq\AuditRecord;
use itdq\AuditTable;

AuditTable::audit('CBN Initiated',AuditTable::RECORD_TYPE_AUDIT);

$personRecord = new personRecord();
$personRecord->sendCbnEmail();

AuditTable::audit('CBN Completed.Check #sm_cdi_audit for details.',AuditTable::RECORD_TYPE_AUDIT);
?>

<div class='container'>
<p>CBN Email request sent to managers.Check email log for details</p>

</div>



