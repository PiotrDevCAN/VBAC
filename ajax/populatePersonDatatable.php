<?php

use vbac\personTable;
use vbac\allTables;

set_time_limit(0);
ob_start();

// session_start();

$personTable = new personTable(allTables::$PERSON);
$data = $personTable->returnAsArray();

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();

if($dataJsonAble) {
     $response = array("data"=>$data,'messages'=>$messages);
 } else {     
    $personTable->findDirtyData();
    $dirtyDetails = ob_get_clean();      
    echo $dirtyDetails;
    exit();
 }
ob_clean();
echo json_encode($response);