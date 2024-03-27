<?php

/**
 * Script intended for populating an CFIRST_ID in records in the PERSON table
 */

use itdq\BlueMail;
use itdq\cFIRST;
use vbac\personTable;
use vbac\allTables;

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

    $personTable = new personTable(allTables::$PERSON);
    $cFirst = new cFIRST();
    
    $timeMeasurements = array();
    $start =  microtime(true);
    $startPhase1 = microtime(true);
    
    $stepCounter = 0;
    $totalCounter = 0;
    
    $i = 1;
    do {
        $data = $cFirst->getBackgroundCheckRequestList(null, null, $i);
        list(
            'BGVErrors' => $errors,
            'BGVListResponse' => $list
        ) = $data;
        
        if (is_countable($list)) {
            foreach($list as $key => $entry) {
                list(
                    // "APIReferenceCode" => $refCode,
                    // "UniqueReferenceNo" => $uniqueReferenceNo,
                    // "ProfileId" => $profileId,
                    "CandidateId" => $candidateId,
                    // "CurrentStatus" => $status,
                    "Email" => $emailAddress,
                    // "FirstName" => $firstName,
                    // "MiddleName" => $middleName,
                    // "LastName" => $lastName,
                    // "Phone" => $phone,
                    // "AddedOn" => $addedDate,
                    // "InfoReceivedOn" => $infoReceivedDate,
                    // "InfoRequestedOn" => $infoRequestedDate,
                    // "InvitedOn" => $invitedDate,
                    // "SubmittedOn" => $submittedDate,
                    // "CompletedOn" => $completedDate,
                ) = $entry;
                if (!empty($emailAddress)) {
                    $personTable->setcFIRSTDataByEmail($emailAddress, $candidateId);
                }
            }
            $stepCounter = count($list);
        } else {
            $subject = 'Error in: cFIRST call - cFIRST API fields';
            $message = serialize($errors);

            $to = array($_ENV['devemailid']);
            $replyto = $_ENV['noreplyemailid'];
            
            $resonse = BlueMail::send_mail($to, $subject, $message, $replyto);
            // trigger_error($subject . " - ". $message, E_USER_ERROR);
        }

        $i++;
        $totalCounter += $stepCounter;
    } while ($stepCounter > 0);
    
    $endPhase1 = microtime(true);
    $timeMeasurements['phase_1'] = (float)($endPhase1-$startPhase1);
    
    $end = microtime(true);
    $timeMeasurements['overallTime'] = (float)($end-$start);
    
    $subject = 'Update cFIRST API fields timings';
    $message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];
    $message .= '<HR>';
    $message .= '<BR/>Amount of records read from cFIRST API ' . $totalCounter;
    $message .= '<HR>';
    $message .= '<BR/>Time of updating vBAC records: ' . $timeMeasurements['phase_1'];
    $message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];
    $message .= '<HR>';
    
    $replyto = $_ENV['noreplyemailid'];
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);

} catch (Exception $e) {
    $subject = 'Error in: Update cFIRST Fields ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array($_ENV['devemailid']);
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
