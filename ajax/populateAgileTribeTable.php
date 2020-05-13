<?php

use vbac\personTable;
use vbac\allTables;
use vbac\AgileTribeTable;

set_time_limit(0);
ob_start();

$table = $_POST['version']=='Original' ? allTables::$AGILE_TRIBE : allTables::$AGILE_TRIBE_OLD;

$agileTribeTable = new AgileTribeTable($table);
$data = $agileTribeTable->returnAsArray();

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();

if($dataJsonAble) {
     $response = array("data"=>$data,'messages'=>$messages,'post'=>print_r($_POST,true));
 } else {
     $response = array("data"=>null,'messages'=>'Data could not be converted to JSON format','post'=>print_r($_POST,true));
 }
ob_clean();
echo json_encode($response);