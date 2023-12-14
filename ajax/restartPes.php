<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use vbac\emails\pesStatusChangeEmail;
use vbac\pesEmail;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$formattedEmailField= null;

try {
    $personTable= new personTable(allTables::$PERSON);
    $personTable->setPesStatus($_POST['psm_cnum'],$_POST['psm_workerid'],$_POST['psm_status'],$_SESSION['ssoEmail']);

    $person = new personRecord();
    $person->setFromArray(array('CNUM'=>$_POST['psm_cnum'],'WORKER_ID'=>$_POST['psm_workerid'],'PES_STATUS_DETAILS'=>$_POST['psm_detail'],'PES_DATE_RESPONDED'=>$_POST['PES_DATE_RESPONDED']));
    $updateRecordResult = $personTable->update($person,false,false);

    $personData = $personTable->getRecord($person);
    $person->setFromArray($personData);

    $pesStatusChangeEmail = new pesStatusChangeEmail();
    
    AuditTable::audit("Saved Person <pre>" . print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);

    if(!$updateRecordResult){
        echo json_encode(sqlsrv_errors());
        echo json_encode(sqlsrv_errors());
        AuditTable::audit("Db2 Error in " . __FILE__ . " Code:<b>" . json_encode(sqlsrv_errors()) . "</b> Msg:<b>" . json_encode(sqlsrv_errors()) . "</b>", AuditTable::RECORD_TYPE_DETAILS);
        $success = false;
    } else {
        switch ($_POST['psm_status']) {
            case personRecord::PES_STATUS_REMOVED:
            case personRecord::PES_STATUS_DECLINED:
            case personRecord::PES_STATUS_FAILED:
            case personRecord::PES_STATUS_INITIATED:
            case personRecord::PES_STATUS_REQUESTED:
            case personRecord::PES_STATUS_EXCEPTION:
            case personRecord::PES_STATUS_PROVISIONAL;
            case personRecord::PES_STATUS_RECHECK_REQ;
            case personRecord::PES_STATUS_RECHECK_PROGRESSING;
            case personRecord::PES_STATUS_MOVER;
            case personRecord::PES_STATUS_LEFT_IBM;
            case personRecord::PES_STATUS_REVOKED;
                $notificationStatus = 'Email not applicable';
                 break;
            case personRecord::PES_STATUS_CLEARED:
            case personRecord::PES_STATUS_CLEARED_PERSONAL:
            case personRecord::PES_STATUS_CLEARED_AMBER:
            case personRecord::PES_STATUS_CANCEL_REQ:
            case personRecord::PES_STATUS_RESTART:
                $emailResponseData = $pesStatusChangeEmail->sendPesStatusChangedEmail($person, pesEmail::EMAIL_NOT_PES_SUPRESSABLE);
                list(
                    'response' => $emailResponse,
                    'to' => $to,
                    'message' => $message,
                    'pesTaskId' => $pesTaskId
                ) = $emailResponseData;
                $notificationStatus = $emailResponse ? 'Email sent' : 'No email sent';
                break;
            default:
                $notificationStatus = 'Email not applicable(other)';
            break;
        }

        AuditTable::audit("PES Status Email: " . $notificationStatus ,AuditTable::RECORD_TYPE_DETAILS);

        $success = true;

    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
    $notificationStatus = "Email not applicable(error)";
}

$messages = ob_get_clean();
ob_start();
$success = $success && empty($messages);
$response = array(
    'success' => $success,
    'messages' => $messages, 
    'emailResponse' => $notificationStatus,
    'cnum' => $_POST['psm_cnum'], 
    'workerId' => $_POST['psm_workerid'], 
    'formattedEmailField' => $formattedEmailField
);
$jse = json_encode($response);
echo $jse ? $jse : json_encode(array('success'=>false,'messages'=>'Failed to json_encode : ' . json_last_error() . json_last_error_msg()));