<?php
use itdq\AuditTable;
use vbac\dlpTable;
use vbac\allTables;
use vbac\dlpRecord;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$dlp = new dlpTable(allTables::$DLP);
$trimmedParms = array_map('trim', $_POST);

$approveReject = $trimmedParms['approveReject'] == dlpRecord::STATUS_APPROVED ? dlpRecord::STATUS_APPROVED : dlpRecord::STATUS_REJECTED;
$dlp->approveReject($trimmedParms['cnum'], $trimmedParms['hostname'], $approveReject);

$messages = ob_get_clean();
ob_start();
$success = (trim($messages) == "");
$response = array('success'=>$success,'messages'=>$messages,"post"=>print_r($_POST,true));
ob_clean();
echo json_encode($response);
