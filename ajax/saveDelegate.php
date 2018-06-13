<?php
use vbac\delegateTable;
use itdq\AuditTable;
use vbac\personTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$delegateEmail = personTable::getEmailFromCnum($_POST['cnum']);

try {
    $success = delegateTable::saveDelegate(trim($_POST['requestorCnum']),trim($_POST['requestorEmail']),trim($_POST['cnum']),trim($delegateEmail));
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
}

$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages,"post"=>print_r($_POST,true));
ob_clean();
echo json_encode($response);