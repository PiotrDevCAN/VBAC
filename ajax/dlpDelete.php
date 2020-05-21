<?php
use itdq\AuditTable;
use vbac\dlpTable;
use vbac\allTables;


ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$dlp = new dlpTable(allTables::$DLP);

$trimmedParms = array_map('trim', $_POST);
$dlp->delete($trimmedParms['cnum'], $trimmedParms['hostname'], $trimmedParms['transferred']);

$messages = ob_get_clean();
ob_start();
$success = (trim($messages) == "");
$response = array('success'=>$success,'cnum'=>$trimmedParms['cnum'],'hostname'=>$trimmedParms['hostname'],'messages'=>$messages,"post"=>print_r($_POST,true));
ob_clean();
echo json_encode($response);
