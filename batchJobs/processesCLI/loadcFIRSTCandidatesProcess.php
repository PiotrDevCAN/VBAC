<?php

/**
 * Script intended for importing all employees' records from the cFIRST application
 */

use itdq\BlueMail;
use itdq\cFIRST;
use vbac\allTables;
use vbac\cFIRSTPersonRecord;
use vbac\cFIRSTPersonTable;

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

    $table = new cFIRSTPersonTable(allTables::$CFIRST_PERSON);
    $record = new cFIRSTPersonRecord();
    
    $table->clear(false);
    
    $cFirst = new cFIRST();
    $totalCounter = 0;
    $insertCounter = 0;
    $failedCounter = 0;
    $i = 1;
    do {
        $data = $cFirst->getBackgroundCheckRequestList(null, null, $i);
        list(
            'BGVErrors' => $error,
            'BGVListResponse' => $list
        ) = $data;
        foreach($list as $key => $entry) {
			list(
				"APIReferenceCode" => $refCode,
				"UniqueReferenceNo" => $uniqueReferenceNo,
				"ProfileId" => $profileId,
                "CandidateId" => $candidateId,
				"CurrentStatus" => $status,
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
            $additionalFields['STATUS'] = trim($status);
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

            // $table->insert($record);
            // null - default return value
            // false - update row
            $saveRecordResult = $table->saveRecord($record);
            if ($saveRecordResult) {
                $insertCounter++;
            } else {
                $failedCounter++;
            }
        }
        $i++;
        $totalCounter += count($list);
    } while (count($list) > 0);
        
    $subject = 'Load cFIRST Candidates Records';
    $message = 'Candidates records have been loaded';
    $message .= '<br> Amount of records read from cFIRST: ' . $totalCounter;
    $message .= '<br> Amount of records imported to vBAC: ' . $insertCounter;
    $message .= '<br> Amount of records failed to import to vBAC: ' . $failedCounter;

    $replyto = $_ENV['noreplyemailid'];
    $result = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);    
    // trigger_error('BlueMail::send_mail result: '.serialize($result), E_USER_WARNING);

} catch (Exception $e) {
    $subject = 'Error in: Load cFIRST Candidates ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array($_ENV['devemailid']);
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
