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

$boardingIbmer = $_POST['boarding']== 'true' ? true:false;

try {
    if(!$boardingIbmer && $_POST['mode']!='Update'){
        //echo "Need to create virtual cnum";
        $cnum = personTable::getNextVirtualCnum();
        $_POST['CNUM']= $cnum;
        // And put their name in the NOTES_ID as that's the field we display as their identity.
        // And copy over their fields into the standard fields.
        $_POST['NOTES_ID'] = $_POST['resFIRST_NAME'] . " " . $_POST['resLAST_NAME'];
        $_POST['FIRST_NAME'] = $_POST['resFIRST_NAME'];
        $_POST['LAST_NAME'] = $_POST['resLAST_NAME'];
        $_POST['COUNTRY'] = $_POST['resCOUNTRY'];
        $_POST['EMAIL_ADDRESS'] = $_POST['resEMAIL_ADDRESS'];
        $_POST['EMPLOYEE_TYPE'] = $_POST['resEMPLOYEE_TYPE'];
        $_POST['PES_STATUS'] = $_POST['resPES_STATUS'];
        AuditTable::audit("Pre boarding:<b>" . $cnum . "</b>",AuditTable::RECORD_TYPE_AUDIT);
    }
    $person->setFromArray($_POST);
    $person->convertCountryCodeToName();
    $saveRecordResult = $table->saveRecord($person);
    AuditTable::audit("Saved Boarding Record:<B>" . $_POST['CNUM'] .":" . $_POST['NOTES_ID'] .  "</b>Mode:<b>" . $_POST['mode'],AuditTable::RECORD_TYPE_AUDIT);
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
        // Do we need to update a PRE-BOARDING record ?
        if(!empty($_POST['person_preboarded'])){
            $preBoarder = new personRecord();
            $preBoarder->setFromArray(array('CNUM'=>$_POST['person_preboarded']));
            $preBoarderData = $table->getFromDb($preBoarder);
            $pesStatus = $preBoarderData['PES_STATUS_DETAILS'];
            $preBoarderData['PES_STATUS_DETAILS'] = 'Boarded as ' . $_POST['CNUM'] . ":" . $_POST['NOTES_ID'] . " Status was:" . $pesStatus;
            $preBoarder->setFromArray($preBoarderData);
            $table->saveRecord($preBoarder);
        }
    } else {
        $errorCode = db2_stmt_error();
        if(empty($errorCode)){
            echo "<br/>Error Trying to save record, no DB2 Stmt Error";
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
$response = array('boarding'=>$_POST['boarding'], 'boardingIbmer'=>$boardingIbmer, 'success'=>$success,'messages'=>$messages,"saveRecord"=>$saveRecordResult,'cnum'=>$_POST['CNUM'], 'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);
