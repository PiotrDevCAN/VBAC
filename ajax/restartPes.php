<?php
use itdq\AuditTable;
use vbac\allTables;
use vbac\personRecord;
use vbac\personTable;
use vbac\pesStatus;
use vbac\pesStatusChangeNotification;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$formattedEmailField = null;

try {

    $requestor = $_SESSION['ssoEmail'];
    $cnum = $_POST['psm_cnum'];
    $workerId = $_POST['psm_worker_id'];
    $status = $_POST['psm_status'];
    $pesDetail = $_POST['psm_detail'];
    $pesDateResponded = $_POST['PES_DATE_RESPONDED'];
    
    $basicPersonDetails = array(
        'CNUM'=>$cnum,
        'WORKER_ID'=>$workerId
    );

    $personTable = new personTable(allTables::$PERSON);
    $person = new personRecord();
    $person->setFromArray(
        $basicPersonDetails
    );
    $pesStatus = new pesStatus();
    $success = $pesStatus->change($personTable, $person, $status, $requestor, $pesDetail, $pesDateResponded);
    
    if ($success) {
        // get person from DB
        $personData = $personTable->getRecord($person);
        $person->setFromArray(
            $personData
        );
        $notification = new pesStatusChangeNotification();
        $notificationStatus = $notification->restart($person, $status);
        AuditTable::audit("PES Status Email: " . $notificationStatus, AuditTable::RECORD_TYPE_DETAILS);
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
    $success = false;
    $notificationStatus = pesStatusChangeNotification::EMAIL_NOT_APPLICABLE_ERROR;
}

$messages = ob_get_clean();
ob_start();
$success = $success && empty($messages);
$response = array(
    'success' => $success,
    'messages' => $messages, 
    'emailResponse' => $notificationStatus,
    'cnum' => $_POST['psm_cnum'],
    'workerId' => $_POST['psm_worker_id'],
    'formattedEmailField' => $formattedEmailField
);
$jse = json_encode($response);
echo $jse ? $jse : json_encode(array('success'=>false,'messages'=>'Failed to json_encode : ' . json_last_error() . json_last_error_msg()));