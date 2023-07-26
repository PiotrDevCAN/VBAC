<?php

use vbac\allTables;
use vbac\skillSetTable;

set_time_limit(0);
ob_start();

$table = allTables::$STATIC_SKILLSETS;

$skillSetTable = new skillSetTable($table);
$data = $skillSetTable->returnAsArray();

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