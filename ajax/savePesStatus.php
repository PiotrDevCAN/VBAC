<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ob_start();

try {

    $now = new DateTime();

    $person = new personRecord();
    $person->setFromArray(array('CNUM'=>$_POST['psm_cnum'],'PES_STATUS'=>$_POST['psm_status'],'PES_STATUS_DETAILS'=>$_POST['psm_detail'],'PES_DATE_RESPONDED'=>$now->format('Y-m-d')));

    $table = new personTable(allTables::$PERSON);
    $updateRecordResult = $table->update($person,false,false);

    if(!$updateRecordResult){
        echo db2_stmt_error();
        echo db2_stmt_errormsg();
        $success = false;
    } else {
        echo "<br/>PES Status set to : " . $_POST['psm_status'];
        echo "<br/>Detail : " . $_POST['psm_detail'];
        $success = true;
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
}

$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages,"updateRecord"=>$updateRecordResult);
ob_clean();
echo json_encode($response);