<?php

use vbac\personTable;
use vbac\allTables;
use vbac\personWithSubPTable;

set_time_limit(0);
ob_start();

// session_start();

$personTable = new personWithSubPTable(allTables::$PERSON);
$preBoardersAction = isset($_REQUEST['preBoardersAction']) ? $_REQUEST['preBoardersAction'] : null;

$data = $personTable->returnAsArray($preBoardersAction);

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();

if($dataJsonAble) {
     $response = array("data"=>$data,'messages'=>$messages,'post'=>print_r($_POST,true));
 } else {
    $personTable->findDirtyData();
    $dirtyDetails = ob_get_clean();
    echo $dirtyDetails;
    exit();
 }
ob_clean();
echo json_encode($response);