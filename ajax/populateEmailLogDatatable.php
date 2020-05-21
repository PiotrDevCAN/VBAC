<?php
use vbac\allTables;
use itdq\DbTable;
use itdq\AllItdqTables;
use itdq\EmailLogTable;

set_time_limit(0);
ob_start();
$emailLogTable = new EmailLogTable(AllItdqTables::$EMAIL_LOG);

$startDate = !empty($_POST['startDate']) ? $_POST['startDate'] : null;
$endDate = !empty($_POST['endDate']) ? $_POST['endDate'] : null;

$data = $emailLogTable->returnAsArray($startDate,$endDate);

$messages = ob_get_clean();
ob_start();

$response = array("data"=>$data,'messages'=>$messages);

ob_clean();
echo json_encode($response);