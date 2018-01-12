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

     if(($saveRecordResult && $_POST['mode']=='Save') || (!$saveRecordResult && $_POST['mode']=='Update')){
        if($_POST['mode']=='Save'){
            echo "<br/>Boarding Form Record - Saved.";
            echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
        }
        if($_POST['mode']=='Update'){
            echo "<br/>Boarding Form Record - Updated.";
        }
        $success = true;
    } else {
        $errorCode = db2_stmt_error();
        if(empty($errorCode)){
            echo "<br/>Record already existed";
        } else {
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
        }
        $success = false;
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
}

$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages,"saveRecord"=>$saveRecordResult);
ob_clean();
echo json_encode($response);