<?php


use itdq\Loader;
use vbac\allTables;
use vbac\personTable;

set_time_limit(0);
ob_start();


$_SESSION['ssoEmail'] = $_SESSION['ssoEmail'];

$loader = new Loader();
$personTable = new personTable(allTables::$PERSON);

$dataAndSql = $personTable->getForRfFlagReport();
list('data' => $data, 'sql' => $sql) = $dataAndSql;

$messages = ob_get_clean();
ob_start();

$response = array("data"=>$data,'messages'=>$messages,'post'=>print_r($_POST,true));

ob_clean();
echo json_encode($response);