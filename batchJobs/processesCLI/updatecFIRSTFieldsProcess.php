<?php

use itdq\BlueMail;
use itdq\cFIRST;
use vbac\personTable;
use vbac\allTables;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit','3072M');

$notFound = 'not found';

$personTable = new personTable(allTables::$PERSON);
$cFirst = new cFIRST();

$timeMeasurements = array();
$start =  microtime(true);
$startPhase1 = microtime(true);
$counter = 0;

for($i = 1; $i <= 1000; $i++) {
    $data = $cFirst->getBackgroundCheckRequestList(null, null, $i);
    list(
        'BGVErrors' => $error,
        'BGVListResponse' => $list
    ) = $data;
    if (count($list) > 0) {
        $counter += count($list);
        foreach($list as $key => $record) {
			list(
				"APIReferenceCode" => $refCode,
				"AddedOn" => $addedDate,
				"CandidateId" => $candidateId,
				"CurrentStatus" => $status,
				"Email" => $emailAddress,
				"FirstName" => $firstName,
				"LastName" => $lastName,
				"MiddleName" => $middleName,
				"Phone" => $phone
			) = $record;
            if (!empty($email)) {
                $personTable->setcFIRSTDataByEmail($email, $candidateId);
            }
        }
    } else {
        break;
    }
}

$endPhase1 = microtime(true);
$timeMeasurements['phase_1'] = (float)($endPhase1-$startPhase1);

$end = microtime(true);
$timeMeasurements['overallTime'] = (float)($end-$start);

$to = array($_ENV['devemailid']);
$cc = array();
if (strstr($_ENV['environment'], 'vbac')) {
    $cc[] = 'Anthony.Stark@kyndryl.com';
    $cc[] = 'philip.bibby@kyndryl.com';
}

$subject = 'Update cFIRST API fields timings';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/>Amount of records read from cFIRST API ' . $counter;
$message .= '<HR>';

$message .= '<BR/>Time of updating vBAC records: ' . $timeMeasurements['phase_1'];

$message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);