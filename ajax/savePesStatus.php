<?php
use itdq\AuditTable;
use vbac\allTables;
use vbac\personRecord;
use vbac\personTable;
use vbac\pesStatus;
use vbac\pesStatusChangeNotification;
use vbac\pesTrackerRecord;
use vbac\pesTrackerTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$formattedEmailField = null;
$formattedPesStatusField = null;

try {

    $requestor = $_SESSION['ssoEmail'];
    $cnum = $_POST['psm_cnum'];
    $workerId = $_POST['psm_worker_id'];
    $status = $_POST['psm_status'];
    $pesDetail = $_POST['psm_detail'];
    $pesDateResponded = $_POST['PES_DATE_RESPONDED'];
    $revalidationStatus = $_POST['psm_revalidationstatus'];
    $firstName = isset($_POST['psm_passportFirst']) ? $_POST['psm_passportFirst'] : null;
    $lastName = isset($_POST['psm_passportSurname']) ? $_POST['psm_passportSurname'] : null;

    $personTable = new personTable(allTables::$PERSON);
    $person = new personRecord();
    $person->setFromArray(
        array(
            'CNUM'=>$cnum,
            'WORKER_ID'=>$workerId
        )
    );
    $pesStatus = new pesStatus();
    $success = $pesStatus->change($personTable, $person, $status, $requestor, $pesDetail, $pesDateResponded);

    if ($success) {
        $notification = new pesStatusChangeNotification();
        $notificationStatus = $notification->save($person, $status, $revalidationStatus);
        AuditTable::audit("PES Status Email: " . $notificationStatus, AuditTable::RECORD_TYPE_DETAILS);
    
        /*
        *
        */
        // get person from DB
        $person = new personRecord();
        $person->setFromArray(
            array(
                'CNUM'=>$cnum,
                'WORKER_ID'=>$workerId
            )
        );
        $personData = $personTable->getRecord($person);
        $person->setFromArray($personData);
        
        $formattedPesStatusField = personTable::getPesStatusWithButtons($personData);

        if (!$firstName && !$lastName) {
            // We've been called from the PES TRACKER Screen;
            $pesTrackeRecord = new pesTrackerRecord();
            $pesTrackeRecord->setFromArray(
                array(
                    'CNUM'=>$cnum,
                    'WORKER_ID'=>$workerId
                )
            );

            $pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);
            $pesTracker->setPesPassportNames($cnum, $workerId, $firstName, $lastName);

            $pesTrackerData = $pesTracker->getRecord($pesTrackeRecord);

            $row = $pesTrackerData;
            $row['EMAIL_ADDRESS'] = $personData['EMAIL_ADDRESS'];
            $row['FIRST_NAME']    = $personData['FIRST_NAME'];
            $row['LAST_NAME']     = $personData['LAST_NAME'];
            $formattedEmailField  = pesTrackerTable::formatEmailFieldOnTracker($row);
        }
        /*
        *
        */
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
    'formattedEmailField' => $formattedEmailField,
    'formattedPesStatusField' => $formattedPesStatusField
);
$jse = json_encode($response);
echo $jse ? $jse : json_encode(array('success'=>false,'messages'=>'Failed to json_encode : ' . json_last_error() . json_last_error_msg()));