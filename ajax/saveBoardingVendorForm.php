<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$person = new personRecord();
$table = new personTable(allTables::$PERSON);

$save = $_POST['mode'] == 'Save' ? true : false;
$update = $_POST['mode'] == 'Update' ? true : false;

try {
    switch (true) {
        case $save:
    
            // save not ibmer
            $cnum = personTable::getNextVirtualCnum();
            $_POST['CNUM'] = $cnum;
            // And put their name in the NOTES_ID as that's the field we display as their identity.
            // And copy over their fields into the standard fields.
            $_POST['NOTES_ID'] = $_POST['FIRST_NAME'] . " " . $_POST['LAST_NAME'];

            if ($_POST['EMPLOYEE_TYPE']==personRecord::EMPLOYEE_TYPE_VENDOR){
                $_POST['REVALIDATION_STATUS'] = personRecord::REVALIDATED_VENDOR;
            } else {
                $_POST['REVALIDATION_STATUS'] = personRecord::REVALIDATED_PREBOARDER;
            }

            AuditTable::audit("Pre boarding:<b>" . $cnum . "</b> Type:" .  $_POST['EMPLOYEE_TYPE'],AuditTable::RECORD_TYPE_AUDIT);

            break;
        case $update:

            // update not ibmer
            $_POST['CNUM'] = $_POST['person_uid'];
            // And put their name in the NOTES_ID as that's the field we display as their identity.
            // And copy over their fields into the standard fields.
            $_POST['NOTES_ID'] = $_POST['FIRST_NAME'] . " " . $_POST['LAST_NAME'];

            if ($_POST['EMPLOYEE_TYPE']==personRecord::EMPLOYEE_TYPE_VENDOR){
                $_POST['REVALIDATION_STATUS'] = personRecord::REVALIDATED_VENDOR;
            } else {
                $_POST['REVALIDATION_STATUS'] = personRecord::REVALIDATED_PREBOARDER;
            }
            break;
        default:
            break;
    }

    if ($_POST['mode']!='Update'){
        $_POST['REVALIDATION_STATUS'] = isset($_POST['REVALIDATION_STATUS']) ? $_POST['REVALIDATION_STATUS'] : personRecord::REVALIDATED_FOUND;
    }
    $_POST['EMAIL_ADDRESS'] = filter_var($_POST['EMAIL_ADDRESS'], FILTER_SANITIZE_EMAIL);
    $_POST['KYN_EMAIL_ADDRESS'] = filter_var($_POST['KYN_EMAIL_ADDRESS'], FILTER_SANITIZE_EMAIL);

    // prepare PERSON from POST
    $person->setFromArray($_POST);

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
    $allowPESInit = true;

    switch (true) {
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
                            echo "<br/>Boarding Form Pre-Boarder / Vendor Record - Saved.";

                            // Ant Stark note - 14th June 2023
                            // vendors will need to go through PES now. Hopefully this makes the logic simpler
                            // if ($_POST['EMPLOYEE_TYPE']==personRecord::EMPLOYEE_TYPE_VENDOR){
                            //     echo "<br/>There is no need to initiate the PES Check Process due to on-boarding vendor employee";
                            // } else {
                            //     echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
                            //     $allowPESInit = true;
                            // }

                            echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
                            $allowPESInit = true;
                            
                            $cnum = $person->getValue('CNUM');
                            if (!empty($cnum)) {                    
                                $person->checkForCBC();
                            } else {
                                echo "<br/>CBC Check notification has been NOT sent due to missing person data.";
                            }
                            $success = true;
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