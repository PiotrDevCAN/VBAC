<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

try {

    $person = new personRecord();
    $person->setFromArray(array('CNUM'=>$_POST['psm_cnum'],'PES_STATUS'=>$_POST['psm_status'],'PES_STATUS_DETAILS'=>$_POST['psm_detail'],'PES_DATE_RESPONDED'=>$_POST['PES_DATE_RESPONDED']));

    $table = new personTable(allTables::$PERSON);
    $updateRecordResult = $table->update($person,false,false);

    $personData = $table->getRecord($person);
    $person->setFromArray($personData);

    AuditTable::audit("Saved Person <pre>" . print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);

    if(!$updateRecordResult){
        echo db2_stmt_error();
        echo db2_stmt_errormsg();
        AuditTable::audit("Db2 Error in " . __FILE__ . " Code:<b>" . db2_stmt_error() . "</b> Msg:<b>" . db2_stmt_errormsg() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
        $success = false;
    } else {
        echo "<br/>PES Status set to : " . $_POST['psm_status'];
        echo "<br/>Detail : " . $_POST['psm_detail'];

        AuditTable::audit("PES Status set for:" . $_POST['psm_cnum'] ." To : " . $_POST['psm_status'] . " Detail :" . $_POST['psm_detail'] . "Date : " . $_POST['PES_DATE_RESPONDED'],AuditTable::RECORD_TYPE_AUDIT);
       
        switch ($_POST['psm_status']) {
            case personRecord::PES_STATUS_REMOVED:
            case personRecord::PES_STATUS_DECLINED:
            case personRecord::PES_STATUS_FAILED:
            case personRecord::PES_STATUS_INITIATED:
            case personRecord::PES_STATUS_REQUESTED:
            case personRecord::PES_STATUS_EXCEPTION:
                $notificationStatus = 'Email not applicable';
                 break;  
            case personRecord::PES_STATUS_CLEARED:
            case personRecord::PES_STATUS_CLEARED_PERSONAL:
                $emailResponse = $person->sendPesStatusChangedEmail();   
                $notificationStatus = $emailResponse ? 'Email sent' : 'No email sent';
                break;
            default:
                $notificationStatus = 'Email not applicable(other)';               
            break;
        }
        
        AuditTable::audit("PES Status Email:" . $notificationStatus ,AuditTable::RECORD_TYPE_DETAILS);
        
        $success = true;
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
}

$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages,"updateRecord"=>$updateRecordResult, "emailResponse"=>$notificationStatus);
ob_clean();
echo json_encode($response);