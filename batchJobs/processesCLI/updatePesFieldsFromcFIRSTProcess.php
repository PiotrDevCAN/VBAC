<?php

/**
 * Script intended for populating selected fields in records in the PERSON table
 */

use itdq\AuditTable;
use itdq\BlueMail;
use itdq\cFIRST;
use vbac\allTables;
use vbac\personRecord;
use vbac\cFIRSTPersonRecord;
use vbac\cFIRSTPersonTable;
use vbac\personTable;
use vbac\pesStatus;
use vbac\pesStatusChangeNotification;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit','3072M');

// require_once __DIR__ . '/../../src/Bootstrap.php';
// $helper = new Sample();
// if ($helper->isCli()) {
//     $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
//     return;
// }

$to = array($_ENV['devemailid']);
$cc = array();
if (strstr($_ENV['environment'], 'vbac')) {
    $cc[] = 'Anthony.Stark@kyndryl.com';
    $cc[] = 'philip.bibby@kyndryl.com';
}

try {
    
    // PES_STATUS : Completed => Cleared
    // PES_CLEARED_DATE : Completed_On_Date => $completedDate
    // PES request number : should be match with C First => 
    // PES_STATUS_DETAILS = First name + last name + [candidate_id] => $firstName . ' ' . $lastName . ' [' . $candidateId . ' ]';
    
    $table = new cFIRSTPersonTable(allTables::$CFIRST_PERSON);
    $record = new cFIRSTPersonRecord();
    
    $personTable = new personTable(allTables::$PERSON);
    $person = new personRecord();
    
    $pesStatus = new pesStatus();
    $notification = new pesStatusChangeNotification();

    $cFirst = new cFIRST();

    $stepCounter = 0;
    $totalCounter = 0;

    $foundInVbac = 0;
    $notFoundInVbac = 0;
    $completedInVbac = 0;

    $insertCounter = 0;
    $failedCounter = 0;
    $i = 1;
    
    $txt = '';

    do {
        $data = $cFirst->getBackgroundCheckRequestList(null, null, $i);
        list(
            'BGVErrors' => $errors,
            'BGVListResponse' => $list
        ) = $data;
        
        if (is_countable($list)) {
            foreach($list as $key => $entry) {
                list(
                    "APIReferenceCode" => $refCode,
                    "UniqueReferenceNo" => $uniqueReferenceNo,
                    "ProfileId" => $profileId,
                    "CandidateId" => $candidateId,
                    "CurrentStatus" => $cFirstStatus,
                    "Email" => $emailAddress,
                    "FirstName" => $firstName,
                    "MiddleName" => $middleName,
                    "LastName" => $lastName,
                    "Phone" => $phone,
                    "AddedOn" => $addedDate,
                    "InfoReceivedOn" => $infoReceivedDate,
                    "InfoRequestedOn" => $infoRequestedDate,
                    "InvitedOn" => $invitedDate,
                    "SubmittedOn" => $submittedDate,
                    "CompletedOn" => $completedDate,
                ) = $entry;
    
                $additionalFields = array();
                $additionalFields['API_REFERENCE_CODE'] = trim($refCode);
                $additionalFields['UNIQUE_REFERENCE_NO'] = trim($uniqueReferenceNo);
                $additionalFields['PROFILE_ID'] = trim($profileId);
                $additionalFields['CANDIDATE_ID'] = trim($candidateId);
                $additionalFields['STATUS'] = trim($cFirstStatus);
                $additionalFields['EMAIL_ADDRESS'] = trim($emailAddress);
                $additionalFields['FIRST_NAME'] = trim($firstName);
                $additionalFields['MIDDLE_NAME'] = trim($middleName);
                $additionalFields['LAST_NAME'] = trim($lastName);
                $additionalFields['PHONE'] = trim($phone);
                $additionalFields['ADDED_ON_DATE'] = trim($addedDate);
                $additionalFields['INFO_RECEIVED_ON_DATE'] = trim($infoReceivedDate);
                $additionalFields['INFO_REQUESTED_ON_DATE'] = trim($infoRequestedDate);
                $additionalFields['INVITED_ON_DATE'] = trim($invitedDate);
                $additionalFields['SUBMITTED_ON_DATE'] = trim($submittedDate);
                $additionalFields['COMPLETED_ON_DATE'] = trim($completedDate);
                
                $record->setFromArray($additionalFields);
    
                switch($cFirstStatus) {
                    case cFIRSTPersonRecord::PROFILE_STATUS_COMPLETED:
    
                        // try EMAIL_ADDRESS
                        $personData = $personTable->getWithPredicate(" EMAIL_ADDRESS='" . trim($emailAddress) . "' ");
                        if (empty($personData)) {
                            // try KYN_EMAIL_ADDRESS
                            $personData = $personTable->getWithPredicate(" KYN_EMAIL_ADDRESS='" . trim($emailAddress) . "' ");
                        }
                        
                        if (!empty($personData)) {
    
                            $vBACpesStatus = $personData['PES_STATUS'];
                            $vBACrevalidationStatus = $personData['REVALIDATION_STATUS'];
                            switch ($vBACpesStatus) {
                                case personRecord::PES_STATUS_CLEARED:
                                case personRecord::PES_STATUS_CLEARED_PERSONAL:
                                case personRecord::PES_STATUS_CLEARED_AMBER:
                                case personRecord::PES_STATUS_CANCEL_REQ:
                                case personRecord::PES_STATUS_PROVISIONAL: // For Covid
                                    $completedInVbac++;
                                    break;
                                default:
                                    $person->setFromArray($personData);
                                
                                    // PES Status to set
                                    $pesStatusCleared = personRecord::PES_STATUS_CLEARED;
                
                                    $requestor = $_SESSION['ssoEmail'];
                                    $pesDetail = $firstName . ' ' . $lastName . ' ['. $candidateId .']';
    
                                    // prepare date
                                    $dateToUseObj = \DateTime::createFromFormat('d-M-Y', $completedDate);
                                    $pesDateResponded = $dateToUseObj->format('Y-m-d');
                                    
                                    $txt .= '<li>';
                                    $txt .= $pesDetail;
                                    $txt .= ' === ';
                                    $txt .= $pesDateResponded;
                                    $txt .= ' === ';
                                    $txt .= $requestor;
                                    $txt .= '</li>';
    
                                    $success = $pesStatus->change($personTable, $person, $pesStatusCleared, $requestor, $pesDetail, $pesDateResponded);
                                    if ($success) {
    
                                        $notificationStatus = $notification->save($person, $pesStatusCleared, $vBACrevalidationStatus);
                                        AuditTable::audit("PES Status Email: " . $notificationStatus, AuditTable::RECORD_TYPE_DETAILS);
                                        
                                        $insertCounter++;
                                    } else {
                                        $failedCounter++;
                                    }
                                    break;
                            }
                            $foundInVbac++;
                        } else {
                            // person not found
                            $notFoundInVbac++;
                        }
                        break;
                    default:
                        break;
                }
            }
            $stepCounter = count($list);      
        } else {
            $subject = 'Error in: cFIRST call - PES Fields From cFIRST';
            $message = serialize($errors);

            $to = array($_ENV['devemailid']);
            $replyto = $_ENV['noreplyemailid'];
            
            $resonse = BlueMail::send_mail($to, $subject, $message, $replyto);
            // trigger_error($subject . " - ". $message, E_USER_ERROR);
        }

        $i++;
        $totalCounter += $stepCounter;
    } while ($stepCounter > 0);
        
    $subject = 'Update PES Fields From cFIRST';
    $message = 'Candidates records have been updated';
    $message .= '<br> Amount of records read from cFIRST: ' . $totalCounter;
    $message .= '<br>';
    $message .= '<br> Amount of records found in vBAC: ' . $foundInVbac;
    $message .= '<br> Amount of records not found in vBAC: ' . $notFoundInVbac;
    $message .= '<br>';
    $message .= '<br> Amount of records already completed in vBAC: ' . $completedInVbac;
    $message .= '<br>';
    $message .= '<br> List of employees set to Completed in vBAC';
    $message .= '<ul>';
    $message .= $txt;
    $message .= '</ul>';
    $message .= '<br> Amount of records updated to Complete in vBAC: ' . $insertCounter;
    $message .= '<br> Amount of records failed to update to Complete in vBAC: ' . $failedCounter;
    
    $replyto = $_ENV['noreplyemailid'];
    $result = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);    
    // trigger_error('BlueMail::send_mail result: '.serialize($result), E_USER_WARNING);

} catch (Exception $e) {
    $subject = 'Error in: Update PES Fields From cFIRST ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array($_ENV['devemailid']);
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
