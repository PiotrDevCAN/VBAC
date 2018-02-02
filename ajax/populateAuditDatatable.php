<?php

use itdq\AllItdqTables;
use itdq\AuditTable;

set_time_limit(0);
ob_start();

session_start();

$type = isset($_POST['type']) ? $_POST['type'] : null;
switch ($type){
    case 'revalidation':
        $predicate =" DATA like 'Revalidation %' " ;
        break;
    default:
        $predicate = null;
}

$auditTable = new AuditTable(AllItdqTables::$AUDIT);
$data = $auditTable->returnAsArray(null,null,$predicate);

$messages = ob_get_clean();

$response = array("data"=>$data,'messages'=>$messages);

ob_clean();
echo json_encode($response);