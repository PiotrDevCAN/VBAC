<?php

use vbac\allTables;
use vbac\personTable;

set_time_limit(0);
ob_start();

$personTable = new personTable(allTables::$PERSON);
$data = $personTable->returnManualUpdateArray();

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();
ob_start();

if($dataJsonAble) {
     $response = array("data"=>$data,'messages'=>$messages,'post'=>print_r($_POST,true));
 } else {
     $response = array("data"=>null,'messages'=>'Data could not be converted to JSON format','post'=>print_r($_POST,true));
 }
ob_clean();
echo json_encode($response);