<?php

use vbac\personTable;
use vbac\allTables;

set_time_limit(0);
ob_start();

session_start();

$personTable = new personTable(allTables::$PERSON);
$data = $personTable->returnAsArray();

var_dump($_SESSION);


$messages = ob_get_clean();

$response = array("data"=>$data,'messages'=>$messages);

ob_clean();
echo json_encode($response);