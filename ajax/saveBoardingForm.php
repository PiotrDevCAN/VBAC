<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ob_start();



try {
    $person = new personRecord();
    $person->setFromArray($_POST);

    $table = new personTable(allTables::$PERSON);
    $saveRecordResult = $table->saveRecord($person);
    echo "Boarding Form Record - Saved.";
    $success = true;
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
}

$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages,"saveRecord"=>$saveRecordResult);
ob_clean();
echo json_encode($response);