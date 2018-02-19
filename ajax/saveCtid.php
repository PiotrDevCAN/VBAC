<?php
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$cnum = !empty($_POST['cnum']) ? $_POST['cnum'] : personTable::getCnumFromNotesid($_POST['notesid']);

try {

    $table = new personTable(allTables::$PERSON);
    $updateRecordResult = $table->saveCtid($cnum,$_POST['ctid']);

    if(!$updateRecordResult){
        echo db2_stmt_error();
        echo db2_stmt_errormsg();
        AuditTable::audit("Db2 Error in " . __FILE__ . " Code:<b>" . db2_stmt_error() . "</b> Msg:<b>" . db2_stmt_errormsg() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
        $success = false;
    } else {
        echo "<br/>CTID set to : " . $_POST['ctid'] . " for " . $cnum;
        $success = true;
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
}

$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages,"updateRecord"=>$updateRecordResult);
ob_clean();
echo json_encode($response);