<?php
use vbac\pesTrackerTable;
use vbac\allTables;

set_time_limit(0);
ob_start();

$pesTrackerTable = new pesTrackerTable(allTables::$PES_TRACKER);
$table = $pesTrackerTable->buildTable($_POST['records']);

$dataJsonAble = json_encode($table);

$messages = ob_get_clean();
$success = empty($messages);

if($dataJsonAble) {
    $response = array("records"=>$_POST['records'],"sucess"=>$success,'messages'=>$messages,'table'=>$table);
} else {
    exit();
}
ob_clean();
echo json_encode($response);