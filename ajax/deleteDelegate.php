<?php
use vbac\delegateTable;
use itdq\AuditTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {
    $success = delegateTable::deleteDelegate(trim($_POST['cnum']),trim($_POST['delegateCnum']));
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
}

$messages = ob_get_clean();
ob_start();
$response = array('success'=>$success,'messages'=>$messages,"post"=>print_r($_POST,true));
ob_clean();
echo json_encode($response);