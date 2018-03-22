<?php
use vbac\personTable;
use vbac\allTables;

ob_start();

$personTable = new personTable(allTables::$PERSON);

$personTable->updateSecurityEducationForCnum($_POST['securityEducation'], $_POST['cnum']);

$messages = ob_get_clean();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);
