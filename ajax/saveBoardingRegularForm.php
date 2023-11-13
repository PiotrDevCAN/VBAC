<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\OKTAGroups;
use itdq\OKTAUsers;
use itdq\WorkerAPI;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$person = new personRecord();
$table = new personTable(allTables::$PERSON);

$save = $_POST['mode'] == 'Save' ? true : false;
$update = $_POST['mode'] == 'Update' ? true : false;

try {
    if ($_POST['mode']!='Update'){
        $_POST['REVALIDATION_STATUS'] = isset($_POST['REVALIDATION_STATUS']) ? $_POST['REVALIDATION_STATUS'] : personRecord::REVALIDATED_FOUND;
    }
    $_POST['EMAIL_ADDRESS'] = filter_var($_POST['EMAIL_ADDRESS'], FILTER_SANITIZE_EMAIL);
    $_POST['KYN_EMAIL_ADDRESS'] = filter_var($_POST['KYN_EMAIL_ADDRESS'], FILTER_SANITIZE_EMAIL);

    // prepare PERSON from POST
    $person->setFromArray($_POST);

    switch (true) {
        case $save:
            $cnum = $person->getValue('CNUM');
            if (strlen($cnum) == 9) {
                if (endsWith($cnum, 'XXX') || endsWith($cnum, 'xxx') || endsWith($cnum, '999')) {
                    // skip CNUM
                    $invalidPersonCnum = false;
                } else {

                    $workerAPI = new WorkerAPI();
                    $data = $workerAPI->getworkerByCNUM($cnum);
                    if (
                        is_array($data)
                        && array_key_exists('count', $data)
                        && $data['count'] > 0
                    ) {
                        //valid ocean
                        $invalidPersonCnum = false;
                    } else {
                        // invalid ocean
                        $invalidPersonCnum = true;

                        $messages = 'Employee data not found in the BluePages.';
                        echo $messages;
                    }
                }
            } else {
                $invalidPersonCnum = false;
            }
            break;
        case $update:
            $invalidPersonCnum = false;
            break;
        default:
            $invalidPersonCnum = false;
            break;
    }

    $email = $person->getValue('EMAIL_ADDRESS');
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $invalidPersonEmailAddress = false;
    } else {
        $invalidPersonEmailAddress = true;
    }

    if (empty($person->getValue('CNUM'))
        || empty($person->getValue('NOTES_ID'))
        || empty($person->getValue('FIRST_NAME'))
        || empty($person->getValue('LAST_NAME'))
        || empty($person->getValue('COUNTRY'))
        || empty($person->getValue('EMPLOYEE_TYPE'))
    ) {
        $invalidOtherParameters = true;
    } else {
        $invalidOtherParameters = false;
    }

    $saveRecordResult = false;
    $timeToWarnPmo = false;
    $allowPESInit = false;

    switch (true) {
        case $invalidPersonCnum:
            // required parameters protection
            $messages = 'Provided Employee Serial Number is invalid';
            echo $messages;
            $success = false;
            break;
        case $invalidPersonEmailAddress:
            // required parameters protection
            $messages = 'Provided Email Address is invalid';
            echo $messages;
            $success = false;
            break;
        case $invalidOtherParameters:
            // required parameters protection
            $messages = 'Significant parameters from form are missing.';
            echo $messages;
            $success = false;
            break;
        default:
            $person->convertCountryCodeToName();
            $saveRecordResult = $table->saveRecord($person, false, false);

            if ($_POST['CTB_RTB'] != personRecord::CIO_ALIGNMENT_TYPE_CTB){
                $table->clearCioAlignment($_POST['CNUM']);
            }
            
            AuditTable::audit("Saved Boarding Record:<B>" . $_POST['CNUM'] .":" . $_POST['NOTES_ID'] .  "</b>Mode:<b>" . $_POST['mode'],AuditTable::RECORD_TYPE_AUDIT);
            AuditTable::audit("Saved Record:<pre>". print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);
            AuditTable::audit("PROJECTED_END_DATE:<pre>". print_r($_POST['PROJECTED_END_DATE'],true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);
        
            $timeToWarnPmo = $person->checkIfTimeToWarnPmo();
            $timeToWarnPmo ? $person->sendOffboardingWarning() : null;

            if (isset($_POST['OktaRoles'])) {
                $OKTAGroups = new OKTAGroups();
                $OKTAUsers = new OKTAUsers();
                foreach($_POST['OktaRoles'] as $key => $groupName) {
                    // add on-boarded employee to OKTA groups
                    $groupId = $OKTAGroups->getGroupId($groupName);
                    $userId = $OKTAUsers->getUserID($_POST['EMAIL_ADDRESS']);
                    $result = $OKTAGroups->addMember($groupId, $userId);
                    $OKTAGroups->clearGroupMembersCache($groupName);
                }
            }

            if ($saveRecordResult) {
                $saveRecordResult = true;
            }
            // null - default
            // false - update
            // rs - insert

            switch (true) {
                case $saveRecordResult:
                    switch (true) {
                        case $save:
                            echo "<br/>Boarding Form Regular Record - Saved.";
                
                            // Ant Stark note - 14th February 2022
                            // If pre-boarder record is filled and PES status is filled after the record is saved 
                            // then 'initiate Pes' button should be disabled.
                            if (!empty($_POST['PRE_BOARDED'])) {
                
                                $table = new personTable(allTables::$PERSON);
                                $personData = $table->getWithPredicate(" EMAIL_ADDRESS='" . htmlspecialchars(trim($_POST['PRE_BOARDED'])) . "' ");
                                
                                if (!empty($personData)) {
                                    echo '<br/>Preboarded person data read from EMAIL_ADDRESS.';
                                } else {
                                    $personData = $table->getWithPredicate(" CNUM='" . htmlspecialchars(trim($_POST['PRE_BOARDED'])) . "' ");
                                    if (!empty($personData)) {
                                        echo '<br/>Preboarded person data read from CNUM.';
                                    } else {
                                        echo '<br/>Unable to read preboarded person data neither from EMAIL_ADDRESS nor CNUM.';
                                    }
                                }
        
                                $person = new personRecord();
                                $person->setFromArray($personData);
                                
                                $pesStatus = $person->getValue('PES_STATUS');
                                
                                switch($pesStatus) {
                                    case personRecord::PES_STATUS_NOT_REQUESTED:
                                        echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
                                        $allowPESInit = true;
                                        break;
                                    default:
                                        echo "<br/>The PES Check Process has been already initialized for preboarded person";
                                        break;
                                }
                            } else {
                                echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
                                $allowPESInit = true;
                            }
                            
                            $cnum = $person->getValue('CNUM');
                            if (!empty($cnum)) {                    
                                $person->checkForCBC();
                            } else {
                                echo "<br/>CBC Check notification has been NOT sent due to missing person data.";
                            }
                            $success = true;
                            // Do we need to update a PRE-BOARDING record ?
                            if (!empty($_POST['PRE_BOARDED'])){
                                try {
                                    $table->linkPreBoarderToIbmer($_POST['PRE_BOARDED'], $_POST['CNUM']);
                                } catch (Exception $e) {
                                    echo "Link PreBoarder To Ocean Id: " . $e->getMessage();
                                }
                            }
                            break;
                        case $update:
                            // incorrect option
                            echo "Expecting to update an existing record, we ended up INSERTING a new one";
                            $success = false;
                            break;
                        default:
                            AuditTable::audit("Db2 Error in " . __FILE__ . " POST:" . print_r($_POST,true) , AuditTable::RECORD_TYPE_DETAILS);
                            break;
                    }
                    break;
                case !$saveRecordResult:
                    switch (true) {
                        case $save:
                            // incorrect option
                            echo "Expecting to create a new record, we ended up UPDATING a record that already existed.";
                            $success = false;
                            break;
                        case $update:
                            echo "<br/>Boarding Form Record - Updated.";
                            $success = true;
                            break;
                        default:
                            AuditTable::audit("Db2 Error in " . __FILE__ . " POST:" . print_r($_POST,true) , AuditTable::RECORD_TYPE_DETAILS);
                            break;
                    }
                    break;
                default:
                    AuditTable::audit("Db2 Error in " . __FILE__ . " POST:" . print_r($_POST,true) , AuditTable::RECORD_TYPE_DETAILS);
                    break;
            }
            break;
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
}

$messages = ob_get_clean();
ob_start();

$response = array(
    'cnum'=>$_POST['CNUM'],
    'employeetype'=>$_POST['EMPLOYEE_TYPE'],
    'pesstatus'=>$_POST['PES_STATUS'],
    'success'=>$success,
    'messages'=>$messages,
    'allowPESInitalise'=>$allowPESInit,
    'saveRecord'=>$saveRecordResult,
    'post'=>print_r($_POST, true),
    'sendWarning'=>print_r($timeToWarnPmo, true)
);
ob_clean();
echo json_encode($response);