<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ob_start();

$success = true;

try {
    $person = new personRecord();
    $person->setFromArray($_POST);

    $table = new personTable(allTables::$PERSON);
    $saveRecordResult = $table->saveRecord($person);
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
}
$messages = ob_get_clean();


$response = array("newRecord"=>$saveRecordResult,'messages'=>$messages,'success'=>$success);

ob_clean();
echo json_encode($response);