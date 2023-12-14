<?php

use itdq\AuditTable;
use vbac\cbnEmail;

AuditTable::audit('CBN Initiated',AuditTable::RECORD_TYPE_AUDIT);

$timeMeasurements = array();
$start = microtime(true);

$cbn = new cbnEmail();
$cbn->sendCbnEmail();

$end = microtime(true);
$timeMeasurements['phase_0'] = (float)($end-$start);

AuditTable::audit('CBN Completed.Check #sm_cdi_audit for details.',AuditTable::RECORD_TYPE_AUDIT);
?>

<div class='container'>
<p>CBN Email request sent to managers.Check email log for details</p>

</div>