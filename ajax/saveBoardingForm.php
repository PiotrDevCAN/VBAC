<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\AuditRecord;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$person = new personRecord();
$table = new personTable(allTables::$PERSON);


try {
    if(empty($_POST['CNUM']) && $_POST['EMPLOYEE_TYPE']=='Pre-Hire'){
        //echo "Need to create virtual cnum";
        $cnum = personTable::getNextVirtualCnum();
        $_POST['CNUM']= $cnum;
        AuditTable::audit("Pre boarding:<b>" . $cnum . "</b>",AuditTable::RECORD_TYPE_AUDIT);
    }
    $person->setFromArray($_POST);
    $person->convertCountryCodeToName();
    $saveRecordResult = $table->saveRecord($person);
    AuditTable::audit("Saved Boarding Record:<B>" . $_POST['CNUM'] . "</b>Mode:<b>" . $_POST['mode'],AuditTable::RECORD_TYPE_AUDIT);
    AuditTable::audit("Saved Record:<pre>". print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);

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
            AuditTable::audit("Db2 Error in " . __FILE__ . " Code:<b>" . db2_stmt_error() . "</b> Msg:<b>" . db2_stmt_errormsg() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
        }
        $success = false;
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
}

$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages,"saveRecord"=>$saveRecordResult,'cnum'=>$_POST['CNUM']);
ob_clean();
echo json_encode($response);