<?php
use vbac\assetRequestsTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$comment = !empty($_POST['comment']) ? trim($_POST['comment']) : null;
$dateReturned = null;
$orderItStatus = !empty($_POST['orderitstatus']) ? trim($_POST['orderitstatus']) : null;
assetRequestsTable::setStatus($_POST['reference'], $_POST['status'],$comment,$dateReturned,$orderItStatus);

$messages = ob_get_clean();

$success = empty($messages);

$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);