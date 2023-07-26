<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\AuditRecord;
use itdq\BluePagesSLAPHAPI;

use vbac\cFIRST\APICaller;
use vbac\cFIRST\AddCandidateRequestRecord;
use vbac\cFIRST\AddCandidateRequestTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);



/*/
    Add candidate to cFIRST system   
/*/
$api = new APICaller();
// add candidate to cFIRST db
$candidateData = array(
    // "PackageName" => "Test package", // not required
    // "PackageId" => "111",
    "PackageName" => "Package 1",
    "PackageId" => "28000000000001",
    "BGCResponseEmailIds" => "piotr.tajanowicz@ocean.ibm.com",
    "EmployeeId" => "123456",
    "APIReferenceCode" => "123456",
    "RequesterId" => "piotr.tajanowicz@ocean.ibm.com",
    "FirstName" => "TEST",
    "MiddleName" => "C",
    "LastName" => "CANDIDATE",
    "Email" => "piotr.tajanowicz@ocean.ibm.com",
    "Phone" => "(425)+533-7745"
);
$resopnse = $api->addCandidate($candidateData);
$dataObj = $resopnse[0]->AddCandidateResult[0];
var_dump($dataObj);

$requestRecord = new AddCandidateRequestRecord();
$targetTable = new AddCandidateRequestTable(allTables::$ADD_CANDIDATE);

$additionalFields = array();
$additionalFields['API_REFERENCE_CODE'] = $dataObj->APIReferenceCode;
$additionalFields['BGV_ERRORS'] = $dataObj->BGVErrors;
$additionalFields['CANDIDATE_ID'] = $dataObj->CandidateId;
$additionalFields['SUCESS'] = $dataObj->Sucess;
$additionalFields['SUCESS_CODE'] = $dataObj->Sucesscode;
$additionalFields['SUCESS_DESCRIPTION'] = $dataObj->Sucessdescription;
$additionalFields['TIMESTAMP'] = $dataObj->Timestamp;
if (!empty($additionalFields)) {
    $requestRecord->setFromArray($additionalFields);
}

$requestRecord->iterateVisible('full');

$valid = $requestRecord->validateForTable($targetTable);
if($valid===true){
    
    // Returns TRUE if it inserted a new record, false if it updated an existing record.
    $saveResponse = $targetTable->saveRecord($requestRecord);
    if ($saveResponse === true) {
        // new record

    } elseif ($saveResponse === false) {
        // existing record
        
    } else {

    }

} else {
    $saveResponse = false;
    $messages = "Add Candidate Record has been not saved";
}

exit;



$person = new personRecord();
$table = new personTable(allTables::$PERSON);

$boardingIbmer = $_POST['boarding'] == 'true' ? true : false;

$save = $_POST['mode'] == 'Save' ? true : false;
$update = $_POST['mode'] == 'Update' ? true : false;

try {
    switch (true) {
        case $boardingIbmer:
            switch (true) {
                case $save:
            
                    break;
                case $update:
                    
                    break;
                default:
                    break;
            }
            break;
        case !$boardingIbmer:
            switch (true) {
                case $save:
            
                    // save not ibmer
                    $cnum = personTable::getNextVirtualCnum();
                    $_POST['CNUM'] = $cnum;
                    // And put their name in the NOTES_ID as that's the field we display as their identity.
                    // And copy over their fields into the standard fields.
                    $_POST['NOTES_ID'] = $_POST['resFIRST_NAME'] . " " . $_POST['resLAST_NAME'];
                    $_POST['FIRST_NAME'] = $_POST['resFIRST_NAME'];
                    $_POST['LAST_NAME'] = $_POST['resLAST_NAME'];
                    $_POST['COUNTRY'] = $_POST['resCOUNTRY'];
                    $_POST['EMAIL_ADDRESS'] = $_POST['resEMAIL_ADDRESS'];
                    $_POST['KYN_EMAIL_ADDRESS'] = $_POST['resKYN_EMAIL_ADDRESS'];
                    $_POST['EMPLOYEE_TYPE'] = $_POST['resEMPLOYEE_TYPE'];
            
                    // switch (trim($_POST['EMPLOYEE_TYPE'])) {
                    //     case personRecord::EMPLOYEE_TYPE_PREBOARDER:
                    //         // ibmer
                    //         // resource_email required
                    //         break;
                    //     case personRecord::EMPLOYEE_TYPE_VENDOR:
                    //         // if other
                    //         // resource_email required
                    //         // else 
                    //         // resource_email not required
                    //         break;
                    //     default:
                    //         break;
                    // }
            
                    if ($_POST['EMPLOYEE_TYPE']==personRecord::EMPLOYEE_TYPE_VENDOR){
                        $_POST['REVALIDATION_STATUS'] = personRecord::REVALIDATED_VENDOR;
                    } else {
                        $_POST['REVALIDATION_STATUS'] = personRecord::REVALIDATED_PREBOARDER;
                    }
            
                    switch (trim($_POST['ROLE_ON_THE_ACCOUNT'])) {
                        case personRecord::ROLE_ON_THE_ACCOUNT_WIPRO:
                        case personRecord::ROLE_ON_THE_ACCOUNT_COGNIZANT:
                        case personRecord::ROLE_ON_THE_ACCOUNT_DENSIFY:
                            $_POST['PES_STATUS'] = personRecord::PES_STATUS_CLEARED;
                            break;
                        case personRecord::ROLE_ON_THE_ACCOUNT_OTHER:
                            $_POST['PES_STATUS'] = personRecord::PES_STATUS_TBD;
                            break;
                        default:
                            break;
                    }
                    AuditTable::audit("Pre boarding:<b>" . $cnum . "</b> Type:" .  $_POST['EMPLOYEE_TYPE'],AuditTable::RECORD_TYPE_AUDIT);

                    break;
                case $update:
                    
                    break;
                default:
                    break;
            }
            break;
        default:
            break;
    }

    if ($_POST['mode']!='Update'){
        $_POST['REVALIDATION_STATUS'] = isset($_POST['REVALIDATION_STATUS']) ? $_POST['REVALIDATION_STATUS'] : personRecord::REVALIDATED_FOUND;
    }
    $_POST['PRE_BOARDED'] = !empty($_POST['person_preboarded']) ? $_POST['person_preboarded']  : null;  // Save the link to the pre-boarded person

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
                    // check if employee has ibm record - IBM CNUM
                    $data = BluePagesSLAPHAPI::getOceanDetailsFromCNUM($cnum);
                    if (empty($data)) {
                        // check if employee has ocean record
                        $data = BluePagesSLAPHAPI::getIBMDetailsFromCNUM($cnum);
                        if (empty($data)) {
                            // CNUM not indetified as IBM nor Kyndryl
                        } else {
                            if (array_key_exists('additional', $data)) {
                                $additionalData = $data['additional'];
                                $namesArr = explode(';', $additionalData);
                                $IBMCnum = $namesArr[0];
                                $IBMMail = $namesArr[1];
                                // echo ' <b>CNUM is from OCEAN</b> ';
                                // echo $IBMCnum;
                                // echo $IBMMail;
                            } else {
                                // CNUM does not have IBM details
                                $IBMCnum = '';
                                $IBMMail = '';
                            }
                        }
                    } else {
                        // echo ' <b>CNUM is from IBM</b> ';
                        // echo $data['uid'];
                        // echo $data['mail'];
                    }
                    if (!empty($data)) {
                        //valid ocean
                        $invalidPersonCnum = false;
                    } else {
                        // invalid ocean
                        $invalidPersonCnum = true;
                    }

                    // $invalidPersonCnum = true;
                    
                    if (empty($data)) {
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

    $emailRequired = true;
    $employeeType = $person->getValue('EMPLOYEE_TYPE');
    $type = $person->getValue('ROLE_ON_THE_ACCOUNT');
    switch (trim($employeeType)) {
        case personRecord::EMPLOYEE_TYPE_PREBOARDER:
            // ibmer
            // resource_email required
            $emailRequired = true;
            break;
        case personRecord::EMPLOYEE_TYPE_VENDOR:
            // if other
            // resource_email required
            // else 
            // resource_email not required
            switch ($type) {
                case personRecord::ROLE_ON_THE_ACCOUNT_OTHER:
                    $emailRequired = false;
                    break;
                default:
                    $emailRequired = true;
                    break;
            }
            break;
        default:
            $emailRequired = true;
            break;
    }

    if ($emailRequired) {
        // email address not required
        $invalidPersonEmailAddress = false;
    } else {
        $email = $person->getValue('EMAIL_ADDRESS');
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $invalidPersonEmailAddress = false;
        } else {
            $invalidPersonEmailAddress = true;
        }
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

            // $saveRecordResult
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
                            echo "<br/>Boarding Form Record - Saved.";
                
                            // Ant Stark note - 14th February 2022
                            // If pre-boarder record is filled and PES status is filled after the record is saved 
                            // then 'initiate Pes' button should be disabled.
                            if ($boardingIbmer && !empty($_POST['person_preboarded'])) {
                
                                $table = new personTable(allTables::$PERSON);
                                $personData = $table->getWithPredicate(" EMAIL_ADDRESS='" . db2_escape_string(trim($_POST['person_preboarded'])) . "' ");
                                
                                if (!empty($personData)) {
                                    echo '<br/>Preboarded person data read from EMAIL_ADDRESS.';
                                } else {
                                    $personData = $table->getWithPredicate(" CNUM='" . db2_escape_string(trim($_POST['person_preboarded'])) . "' ");
                                    if (!empty($personData)) {
                                        echo '<br/>Preboarded person data read from CNUM.';
                                    } else {
                                        echo '<br/>Unable to read preboarded person data neither from EMAIL_ADDRESS nor CNUM.';
                                    }
                                }
        
                                $person = new personRecord();
                                $person->setFromArray($personData);
                                
                                $pesStatus = $person->getValue('PES_STATUS');
                                
                                if ($pesStatus!=personRecord::PES_STATUS_NOT_REQUESTED) {
                                    echo "<br/>The PES Check Process has been already initialized for preboarded person";
                                } else {
                                    echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
                                }
                            } else {
                                if ($_POST['EMPLOYEE_TYPE']==personRecord::EMPLOYEE_TYPE_VENDOR){
                                    echo "<br/>There is no need to initiate the PES Check Process due to on-boarding vendor employee";
                                } else {
                                    echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
                                }
                            }
                            
                            $cnum = $person->getValue('CNUM');
                            if (!empty($cnum)) {                    
                                $person->checkForCBC();
                            } else {
                                echo "<br/>CBC Check notification has been NOT sent due to missing person data.";
                            }
                            $success = true;
                            // Do we need to update a PRE-BOARDING record ?
                            if (!empty($_POST['person_preboarded'])){
                                try {
                                    $table->linkPreBoarderToIbmer($_POST['person_preboarded'], $_POST['CNUM']);
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
    'boarding'=>$_POST['boarding'],
    'boardingIbmer'=>$boardingIbmer,
    'employeetype'=>$_POST['EMPLOYEE_TYPE'],
    'pesstatus'=>$_POST['PES_STATUS'],
    'success'=>$success,
    'messages'=>$messages,
    'saveRecord'=>$saveRecordResult,
    'cnum'=>$_POST['CNUM'],
    'post'=>print_r($_POST,true),
    'sendWarning'=>print_r($timeToWarnPmo,true)
);
ob_clean();
echo json_encode($response);