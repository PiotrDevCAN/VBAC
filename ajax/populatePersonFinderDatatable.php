<?php
use vbac\personTable;
use vbac\allTables;
use vbac\personRecord;

set_time_limit(0);
ob_start();

// session_start();

$personTable = new personTable(allTables::$PERSON);
$data = $personTable->returnPersonFinderArray(personTable::ACTIVE_WITH_PROVISIONAL_CLEARANCE);

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();
ob_start();

if($dataJsonAble) {
    $response = array("data"=>$data,'messages'=>$messages);
} else {
    $personTable->findDirtyData();
    $dirtyDetails = ob_get_clean();
    ob_start();
    ob_clean();
    echo $dirtyDetails;
    exit();
}
ob_clean();
echo json_encode($response);