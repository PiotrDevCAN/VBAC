<?php
use vbac\requestableAssetListTable;
use itdq\AuditTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$success = requestableAssetListTable::flagAsDeleted($_POST['ASSET_TITLE'], $_POST['DELETED_BY']);
$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);