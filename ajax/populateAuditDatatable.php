<?php

use itdq\AllItdqTables;
use itdq\AuditTable;

set_time_limit(0);
ob_start();

session_start();

$auditTable = new AuditTable(AllItdqTables::$AUDIT);
$data = $auditTable->returnAsArray();

$messages = ob_get_clean();

$response = array("data"=>$data,'messages'=>$messages);

ob_clean();
echo json_encode($response);