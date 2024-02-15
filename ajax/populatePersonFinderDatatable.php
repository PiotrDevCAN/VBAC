<?php
use vbac\personTable;
use vbac\allTables;
use vbac\personRecord;

set_time_limit(0);
ob_start();

$personTable = new personTable(allTables::$PERSON, null, true, true);
$dataAndSql = $personTable->returnPersonFinderArray(personTable::ACTIVE_WITH_PROVISIONAL_CLEARANCE);
list('data' => $data, 'sql' => $sql) = $dataAndSql;

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();
ob_start();

if($dataJsonAble) {
    $response = array("data"=>$data,'messages'=>$messages,'sql'=>$sql);
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