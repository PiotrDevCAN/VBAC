<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\cFIRST;
use itdq\OKTAGroups;
use itdq\OKTAUsers;
use itdq\WorkerAPI;
use vbac\emails\cbcEmail;
use vbac\emails\offboardingWarningEmail;
use vbac\knownValues\knownCNUMs;
use vbac\knownValues\knownKyndrylEmails;
use vbac\knownValues\knownWorkerIDs;

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
        $_POST['REVALIDATION_STATUS'] = isset($_POST['REVALIDATION_STATUS']) ? $_POST['REVALIDATION_STATUS'] : personRecord::REVALIDATED_FOUND;
    }
    $_POST['EMAIL_ADDRESS'] = filter_var($_POST['EMAIL_ADDRESS'], FILTER_SANITIZE_EMAIL);
    $_POST['KYN_EMAIL_ADDRESS'] = filter_var($_POST['KYN_EMAIL_ADDRESS'], FILTER_SANITIZE_EMAIL);

    // prepare PERSON from POST
    $person->setFromArray($_POST);

    $cnum = $person->getValue('CNUM');
    $workerId = $person->getValue('WORKER_ID');

    switch (true) {
        case $save:
            if (strlen($cnum) == 9) {
                if (endsWith($cnum, 'XXX') || endsWith($cnum, 'xxx') || endsWith($cnum, '999')) {
                    // skip CNUM
                    $invalidPersonId = false;
                } else {

                    $workerAPI = new WorkerAPI();
                    $data = $workerAPI->getworkerByCNUM($cnum);
                    if (
                        is_array($data)
                        && array_key_exists('count', $data)
                        && $data['count'] > 0
                    ) {
                        //valid Kyndryl
                        $invalidPersonId = false;
                    } else {
                        // invalid Kyndryl
                        $invalidPersonId = true;

                        $messages = 'Employee data not found in the Worker API.';
                        echo $messages;
                    }
                }
            } else {
                if ($cnum == personRecord::NO_LONGER_AVAILABLE) {
                    if (!empty($workerId)) {

                        $workerAPI = new WorkerAPI();
                        $data = $workerAPI->getworkerByWorkerId($workerId);
                        if (
                            is_array($data)
                            && array_key_exists('count', $data)
                            && $data['count'] > 0
                        ) {
                            //valid Kyndryl
                            $invalidPersonId = false;
                        } else {
                            // invalid Kyndryl
                            $invalidPersonId = true;

                            $messages = 'Employee data not found in the Worker API.';
                            echo $messages;
                        }
                    } else {
                        // invalid CNUM and Worker Id
                        $invalidPersonId = true;

                        $messages = 'Employee does not have either CNUM and Worker Id.';
                        echo $messages;
                    }
                } else {
                    $invalidPersonId = true;
                }
            }
            break;
        case $update:
            $invalidPersonId = false;
            break;
        default:
            $invalidPersonId = false;
            break;
    }

    $email = $person->getValue('EMAIL_ADDRESS');
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $invalidPersonEmailAddress = false;
    } else {
        $invalidPersonEmailAddress = true;
    }

    $kynEmail = $person->getValue('KYN_EMAIL_ADDRESS');
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $invalidPersonKynEmailAddress = false;
    } else {
        $invalidPersonKynEmailAddress = true;
    }

    $agileTribeId = isset($_POST['TRIBE_NUMBER']) ? $_POST['TRIBE_NUMBER'] : null;
    if (empty($agileTribeId)) {
        $invaliedAgileTribe = false;
    } else {
        $invaliedAgileTribe = true;
    }

    $agileSquadId = isset($_POST['SQUAD_NUMBER']) ? $_POST['SQUAD_NUMBER'] : null;
    if (empty($agileSquadId)) {
        $invaliedAgileSquad = false;
    } else {
        $invaliedAgileSquad = true;
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
        case $invalidPersonId:
            // required parameters protection
            $messages = 'Provided Employee Serial Number or Worker Id is invalid';
            echo $messages;
            $success = false;
            break;
        case $invalidPersonEmailAddress:
            // required parameters protection
            $messages = 'Provided Email Address is invalid';
            echo $messages;
            $success = false;
            break;
        case $invalidPersonKynEmailAddress:
            // required parameters protection
            $messages = 'Provided Kyndryl Email Address is invalid';
            echo $messages;
            $success = false;
            break;
        case $invaliedAgileTribe:
            // required parameters protection
            $messages = 'Provided Agile Tribe Name is invalid';
            echo $messages;
            $success = false;
            break;
        case $invaliedAgileSquad:
            // required parameters protection
            $messages = 'Provided Agile Squad Name is invalid';
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

            // null - default return value
            // false - update row
            if ($saveRecordResult === null) {
                $saveRecordResult = true;
            }
            
            switch (true) {
                case $saveRecordResult:
                    switch (true) {
                        case $save:

                            AuditTable::audit("Saved Boarding Record:<B>" . $cnum .":" . $workerId .  "</b>Mode:<b>" . $mode, AuditTable::RECORD_TYPE_AUDIT);
                            AuditTable::audit("Saved Record:<pre>". print_r($person,true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);
                            AuditTable::audit("PROJECTED_END_DATE:<pre>". print_r($_POST['PROJECTED_END_DATE'],true) . "</pre>", AuditTable::RECORD_TYPE_DETAILS);

                            /*
                            * clear CIO alignment
                            */
                            if ($_POST['CTB_RTB'] != personRecord::CIO_ALIGNMENT_TYPE_CTB){
                                $personTable->clearCioAlignment($cnum, $workerId);
                            }
                            
                            /*
                            * send notification
                            */
                            $timeToWarnPmo = $person->checkIfTimeToWarnPmo();
                            $offboardingWarning = new offboardingWarningEmail();
                            $timeToWarnPmo ? $offboardingWarning->send($person) : null;

                            /*
                            * assign employee to requested groups
                            */
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

                            /*
                            * add record to cFIRST
                            */
                            $employeeId = personRecord::UNKNOWN;
                            if ($cnum == personRecord::NO_LONGER_AVAILABLE) {
                                if (!empty($workerId)) {
                                    $employeeId = $workerId;
                                }
                            } else {
                                $employeeId = $cnum;
                            }

                            // PES_LEVEL
                            // LBG_LOCATIOn
                            
                            // $candidateData = array(
                            //     /*
                            //     * a further discussion with Divya is required
                            //     */
                            //     "PackageName" => "Sample Pacakge", // not required
                            //     "PackageId" => "28000000000006",
                            //     "BGCResponseEmailIds" => $person->getValue('KYN_EMAIL_ADDRESS'),
                            //     "EmployeeId" => $employeeId,
                            //     "APIReferenceCode" => $employeeId, // CNUM or WORKER_ID in vBAC
                            //     "RequesterId" => $person->getValue('PES_REQUESTOR'),
                            //     "FirstName" => $person->getValue('FIRST_NAME'),
                            //     "MiddleName" => "",
                            //     "LastName" => $person->getValue('LAST_NAME'),
                            //     "Email" => $person->getValue('KYN_EMAIL_ADDRESS'),
                            //     "Phone" => ""
                            // );
                            // $cFirst = new cFIRST();
                            // $cFIRSTSaveRecordResult = $cFirst->addCandidate($candidateData);
                            // echo '<pre>';
                            // var_dump($cFIRSTSaveRecordResult);
                            // echo '</pre>';

                            echo "<br/>Boarding Form Regular Record - Saved.";
                
                            // Ant Stark note - 14th February 2022
                            // If pre-boarder record is filled and PES status is filled after the record is saved 
                            // then 'initiate Pes' button should be disabled.
                            // Do we need to update a PRE-BOARDING record ?
                            if (!empty($_POST['PRE_BOARDED'])) {
                                
                                $preboardedPersonData = $personTable->getWithPredicate(" EMAIL_ADDRESS='" . htmlspecialchars(trim($_POST['PRE_BOARDED'])) . "' ");
                                
                                if (!empty($preboardedPersonData)) {
                                    echo '<br/>Preboarded person data read from EMAIL_ADDRESS.';
                                } else {
                                    $preboardedPersonData = $personTable->getWithPredicate(" CNUM='" . htmlspecialchars(trim($_POST['PRE_BOARDED'])) . "' ");
                                    if (!empty($preboardedPersonData)) {
                                        echo '<br/>Preboarded person data read from CNUM.';
                                    } else {
                                        echo '<br/>Unable to read preboarded person data neither from EMAIL_ADDRESS nor CNUM.';
                                    }
                                }

                                if (!empty($preboardedPersonData)) {

                                    $preboardedPerson = new personRecord();
                                    $preboardedPerson->setFromArray($preboardedPersonData);
                                    
                                    $preboarderCnum = $preboardedPerson->getValue('CNUM');
                                    $preboarderWorkerId = $preboardedPerson->getValue('WORKER_ID');
                                    
                                    $pesStatus = $preboardedPerson->getValue('PES_STATUS');
        
                                    switch($pesStatus) {
                                        case personRecord::PES_STATUS_NOT_REQUESTED:
                                            echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
                                            $allowPESInit = true;
                                            break;
                                        default:
                                            echo "<br/>The PES Check Process has been already initialized for preboarded person";
                                            break;
                                    }

                                    try {
                                        $personTable->linkPreBoarderToRegular($preboarderCnum, $preboarderWorkerId, $cnum, $workerId);
                                    } catch (Exception $e) {
                                        echo "Link PreBoarder To Kyndryl Id: " . $e->getMessage();
                                    }
                                } 
                            } else {
                                echo "<br/>Click 'Initiate PES' button to initiate the PES Check Process";
                                $allowPESInit = true;
                            }
                            
                            // check regular record
                            if (!empty($cnum) && !empty($workerId)) {
                                $cbc = new cbcEmail();
                                $cbc->send($person);
                            } else {
                                echo "<br/>CBC Check notification has been NOT sent due to missing person data.";
                            }

                            // refresh list of know values
                            $kyndrylEmails = new knownKyndrylEmails();
                            $kyndrylEmails->reloadCache();

                            $knownCNUMS = new knownCNUMs();
                            $knownCNUMS->reloadCache();

                            $knownWorkerIDS = new knownWorkerIDs();
                            $knownWorkerIDS->reloadCache();
                            
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