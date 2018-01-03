<?php


use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ob_start();

try {
    $cnum = $_POST['cnum'];
    $table = new personTable(allTables::$PERSON);
    $success = $table->setPesRequested($cnum);
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
}

$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);