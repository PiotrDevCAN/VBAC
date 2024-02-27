<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use vbac\emails\cbcEmail;
use vbac\emails\offboardingWarningEmail;
use vbac\knownValues\knownExternalEmails;
use vbac\knownValues\knownIBMEmails;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personTable = new personTable(allTables::$PERSON);
$person = new personRecord();

$mode = !empty($_POST['mode']) ? trim($_POST['mode']) : '';
$save = $mode == 'Save' ? true : false;
$update = $mode == 'Update' ? true : false;

try {
    // set initial value of REVALIDATION_STATUS
    if ($save == true){
        $_POST['REVALIDATION_STATUS'] = $_POST['EMPLOYEE_TYPE']==personRecord::EMPLOYEE_TYPE_VENDOR ? personRecord::REVALIDATED_VENDOR : personRecord::REVALIDATED_PREBOARDER;
    }
    $_POST['EMAIL_ADDRESS'] = filter_var($_POST['EMAIL_ADDRESS'], FILTER_SANITIZE_EMAIL);
    $_POST['KYN_EMAIL_ADDRESS'] = filter_var($_POST['KYN_EMAIL_ADDRESS'], FILTER_SANITIZE_EMAIL);

    // And put their name in the NOTES_ID as that's the field we display as their identity.
    // And copy over their fields into the standard fields.
    $_POST['NOTES_ID'] = $_POST['FIRST_NAME'] . " " . $_POST['LAST_NAME'];
    
    // set initial value of CNUM
    switch (true) {
        case $save:
            // save not ibmer
            $cnum = personTable::getNextVirtualCnum();
            $_POST['CNUM'] = $cnum;
            $_POST['WORKER_ID'] = personRecord::NOT_FOUND;
            AuditTable::audit("Pre boarding:<b>" . $cnum . "</b> Type:" .  $_POST['EMPLOYEE_TYPE'], AuditTable::RECORD_TYPE_AUDIT);
            break;
        case $update:
            // update not ibmer
            $cnum = $_POST['resource_uid'];
            $_POST['CNUM'] = $cnum;
            $_POST['WORKER_ID'] = isset($_POST['WORKER_ID']) ? $_POST['WORKER_ID'] : personRecord::NOT_FOUND;        
            AuditTable::audit("Pre boarded update:<b>" . $cnum . "</b> Type:" .  $_POST['EMPLOYEE_TYPE'], AuditTable::RECORD_TYPE_AUDIT);
            break;
        default:
            break;
    }

    // prepare PERSON from POST
    $person->setFromArray($_POST);

    $email = $person->getValue('EMAIL_ADDRESS');
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $invalidPersonEmailAddress = false;
    } else {
        $invalidPersonEmailAddress = true;
    }

    if (empty($person->getValue('CNUM'))
        || empty($person->getValue('WORKER_ID'))
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

    $success = false;

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
            $saveRecordResult = $personTable->saveRecord($person, false, false);

            switch (true) {
                case $saveRecordResult === true:
                    switch (true) {
                        case $save:

                            AuditTable::audit("Saved Boarding Record:<B>" . $_POST['CNUM'] .":" . $_POST['WORKER_ID'] .  "</b>Mode:<b>" . $mode, AuditTable::RECORD_TYPE_AUDIT);
                            AuditTable::audit("Saved Record:<pre>". print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);
                            AuditTable::audit("PROJECTED_END_DATE:<pre>". print_r($_POST['PROJECTED_END_DATE'],true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);

                            /*
                            * clear CIO alignment
                            */
                            if ($_POST['CTB_RTB'] != personRecord::CIO_ALIGNMENT_TYPE_CTB){
                                $personTable->clearCioAlignment($_POST['CNUM'], $_POST['WORKER_ID']);
                            }
                            
                            /*
                            * send notification
                            */
                            $timeToWarnPmo = $person->checkIfTimeToWarnPmo();
                            $offboardingWarning = new offboardingWarningEmail();
                            $timeToWarnPmo ? $offboardingWarning->send($person) : null;

                            echo "<br/>Boarding Form Pre-Boarder / Vendor Record - Saved.";
                            echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
                            $allowPESInit = true;
                            
                            $cnum = $person->getValue('CNUM');
                            $workerId = $person->getValue('WORKER_ID');
                            if (!empty($cnum) && !empty($workerId)) {                    
                                $cbc = new cbcEmail();
                                $cbc->send($person);
                            } else {
                                echo "<br/>CBC Check notification has been NOT sent due to missing person data.";
                            }

                            // refresh list of know values
                            $extenalEmails = new knownExternalEmails();
                            $extenalEmails->reloadCache();

                            $IBMEmails = new knownIBMEmails();
                            $IBMEmails->reloadCache();
                            
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
                case $saveRecordResult === false:
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
                    echo "<br/>Boarding Form Record - Failed, likely an error has occurred.";
                    $success = false;
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
    'workerId'=>$_POST['WORKER_ID'],
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