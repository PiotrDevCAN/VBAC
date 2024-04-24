<?php
use itdq\AuditTable;
use vbac\allTables;
use vbac\emails\pesStatusChangedConfirmationEmail;
use vbac\personRecord;
use vbac\pesTrackerTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);
$response = array();
$cnum = trim($_POST['cnum']);
$workerId = trim($_POST['workerid']);
$firstName = trim($_POST['firstname']);
$lastName = trim($_POST['lastname']);
$emailAddress = trim($_POST['emailaddress']);
$flm = !empty(trim($_POST['flm']))  ? trim($_POST['flm']) : null;

try {

    $pesEmailObj = new pesStatusChangedConfirmationEmail();

    $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
    $pesTracker->setPesProcessStatus($_POST['cnum'],$_POST['workerid'],$_POST['processStatus']);

    $comment = $pesTracker->savePesComment($_POST['cnum'],$_POST['workerid'],"Process Status set to " . $_POST['processStatus']);

    $messages  = ob_get_clean();
    ob_start();
    $success   = empty($messages);

    $basicPersonDetails = array(
        'CNUM'=>$cnum,
        'WORKER_ID'=>$workerId
    );

    $person = new personRecord();
    $person->setFromArray(
        $basicPersonDetails
    );
    $person->setFromArray(
        array(
            'FIRST_NAME'=>$firstName, 
            'LAST_NAME'=>$lastName, 
            'EMAIL_ADDRESS'=>$emailAddress
        )
    );

    if($success){
        // Some Status Changes - we notify the subject, so they know what's happening.
        switch (trim($_POST['processStatus'])) {
            case 'CRC':
            case 'PES':
                $emailResponse = $pesEmailObj->send($person, trim($_POST['processStatus']), $flm);
                $response['emailResponse'] = $emailResponse;
            break;
            case 'User':
                $emailResponse = $pesEmailObj->send($person, trim($_POST['processStatus']), $flm);
                $response['emailResponse'] = $emailResponse;
            default:
                ;
            break;
        }
    }

    $now = new DateTime();
    $row = array(
        'CNUM'=>$_POST['cnum'],
        'WORKER_ID'=>$_POST['workerid'],
        'PROCESSING_STATUS'=>$_POST['processStatus'],
        'PROCESSING_STATUS_CHANGED'=>$now->format('Y-m-d H:i:s')
    );

    ob_start();
    pesTrackerTable::formatProcessingStatusCell($row);
    $formattedStatusField = ob_get_clean();
    ob_start();

} catch (Exception $e){
    $success = false;
    $messages = $pesTracker->lastDb2StmtErrorMsg;

}
$response['success']=$success;
$response['messages']=$messages;
$response['formattedStatusField']=$formattedStatusField;
$response['comment']=$comment;
echo json_encode($response);