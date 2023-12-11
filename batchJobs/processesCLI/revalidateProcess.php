<?php

use itdq\Loader;
use itdq\WorkerAPI;
use itdq\AuditTable;
use itdq\BlueMail;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

AuditTable::audit("Revalidation invoked.",AuditTable::RECORD_TYPE_REVALIDATION);

set_time_limit(0);
ini_set('memory_limit','2048M');

$personTable = new personTable(allTables::$PERSON);
$loader = new Loader();
$workerAPI = new WorkerAPI();

$timeMeasurements = array();
$start =  microtime(true);

/*
*
* Common section
*
*/

// sets preboarder status
$startPhase0 = microtime(true);
$personTable->flagPreboarders();
$endPhase0 = microtime(true);
$timeMeasurements['phase_0'] = (float)($endPhase0-$startPhase0);

// get number of employees with preboarder status
$startPhase1 = microtime(true);
$preBoardersPredicate = " ( trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_PREBOARDER . "') ";
$allPreboarders = $loader->load('CNUM',allTables::$PERSON, $preBoardersPredicate );
$allPreboardersCounter = count($allPreboarders);
$allPreboarders = null; // free up some storage
$endPhase1 = microtime(true);
$timeMeasurements['phase_1'] = (float)($endPhase1-$startPhase1);

// ignore REVALIDATION_STATUS = PREBOARDER
AuditTable::audit("Revalidation will ignore " . $allPreboardersCounter . " pre-boarders.", AuditTable::RECORD_TYPE_REVALIDATION);

// get number of employees with offboard status
$startPhase2 = microtime(true);
$offboardersPredicate = " ( REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_OFFBOARD . "%') ";
$allOffboarders = $loader->load('CNUM',allTables::$PERSON, $offboardersPredicate );
$allOffboardersCounter = count($allOffboarders);
$allOffboarders = null; // free up some storage
$endPhase2 = microtime(true);
$timeMeasurements['phase_2'] = (float)($endPhase2-$startPhase2);

// ignore REVALIDATION_STATUS = OFFBOARDING/ED
AuditTable::audit("Revalidation will ignore " . $allOffboardersCounter . " offboarding/ed.", AuditTable::RECORD_TYPE_REVALIDATION);

// get number of employees with vendor status
$startPhase3 = microtime(true);
$vendorsPredicate = " ( trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_VENDOR . "') ";
$allVendors = $loader->load('CNUM',allTables::$PERSON, $vendorsPredicate );
$allVendorsCounter = count($allVendors);
$allVendors = null; // free up some storage
$endPhase3 = microtime(true);
$timeMeasurements['phase_3'] = (float)($endPhase3-$startPhase3);

// ignore REVALIDATION_STATUS = VENDORS
AuditTable::audit("Revalidation will ignore " . $allVendorsCounter . " vendors.", AuditTable::RECORD_TYPE_REVALIDATION);

/*
*
* CNUM section
*
*/

// get number of employees with empty OR null OR found status
$startPhase4 = microtime(true);
$activeIbmErsPredicate = " ( trim(REVALIDATION_STATUS) = '' OR REVALIDATION_STATUS IS NULL OR trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_FOUND . "') ";
$activeIbmErsPredicate .= " AND " . personTable::availableCNUMPredicate();
$allNonLeaversCNUMs = $loader->load('CNUM',allTables::$PERSON, $activeIbmErsPredicate );
$allNonLeaversCNUMsCounter = count($allNonLeaversCNUMs);
$endPhase4 = microtime(true);
$timeMeasurements['phase_4'] = (float)($endPhase4-$startPhase4);

// iterate through these employees' records
AuditTable::audit("Revalidation will check " . $allNonLeaversCNUMsCounter . " people currently flagged as found.", AuditTable::RECORD_TYPE_REVALIDATION);

$startPhase5 = microtime(true);
foreach ($allNonLeaversCNUMs as $key => $CNUM) {
    $data = $workerAPI->getworkerByCNUM($CNUM);
    if (
        is_array($data) 
        && array_key_exists('count', $data) 
        && $data['count'] > 0
    ) {
        $employeeData = $data['results'][0];
        $notesid = personRecord::NO_LONGER_AVAILABLE;
        $mail = $employeeData['email'];
        $serial = $employeeData['cnum'];
        $personTable->confirmRevalidationByCNUM($notesid, $mail, $serial);
        unset($allNonLeaversCNUMs[$serial]);
    }
}
$endPhase5 = microtime(true);
$timeMeasurements['phase_5'] = (float)($endPhase5-$startPhase5);

// At this stage, anyone still in the $allNonLeaversCNUMs array - has NOT been found in BP and so is now POTENTIALLY a leaver and needs to be flagged as such.
AuditTable::audit("Revalidation found " . count($allNonLeaversCNUMs) . " potential leavers.",AuditTable::RECORD_TYPE_REVALIDATION);

// sets potentialLeaver status
$startPhase6 = microtime(true);
$allPotentialLeaverCNUMsCounter = count($allNonLeaversCNUMs);
foreach ($allNonLeaversCNUMs as $cnum){
    set_time_limit(10);
    $personTable->flagPotentialLeaverByCNUM($cnum);
}
$endPhase6 = microtime(true);
$timeMeasurements['phase_6'] = (float)($endPhase6-$startPhase6);

// sets leaver status
// $startPhase7 = microtime(true);
// $allLeaverCounterCNUMs = count($allNonLeaversCNUMs);
// foreach ($allNonLeaversCNUMs as $cnum){
//     set_time_limit(10);
//     $personTable->flagLeaverByCNUM($cnum);
// }
// $endPhase7 = microtime(true);
// $timeMeasurements['phase_7'] = (float)($endPhase7-$startPhase7);

AuditTable::audit("Revalidation completed.",AuditTable::RECORD_TYPE_REVALIDATION);

/*
*
* Worker ID section
*
*/

// get number of employees with empty OR null OR found status
$startPhase8 = microtime(true);
$activeIbmErsPredicate = " ( trim(REVALIDATION_STATUS) = '' OR REVALIDATION_STATUS IS NULL OR trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_FOUND . "') ";
$activeIbmErsPredicate .= " AND " . personTable::normalWorkerIDPredicate();
$allNonLeaversWorkerIDs = $loader->load('WORKER_ID',allTables::$PERSON, $activeIbmErsPredicate );
$allNonLeaversWorkerIDsCounter = count($allNonLeaversWorkerIDs);
$endPhase8 = microtime(true);
$timeMeasurements['phase_8'] = (float)($startPhase8-$startPhase8);

// iterate through these employees' records
AuditTable::audit("Revalidation will check " . $allNonLeaversWorkerIDsCounter . " people currently flagged as found.", AuditTable::RECORD_TYPE_REVALIDATION);

$startPhase9 = microtime(true);
foreach ($allNonLeaversWorkerIDs as $key => $WORKER_ID) {
    $data = $workerAPI->getworkerByWorkerId($WORKER_ID);
    if (
        is_array($data) 
        && array_key_exists('count', $data) 
        && $data['count'] > 0
    ) {
        $employeeData = $data['results'][0];
        $notesid = personRecord::NO_LONGER_AVAILABLE;
        $mail = $employeeData['email'];
        $personTable->confirmRevalidationByWORKER_ID($notesid, $mail, $WORKER_ID);
        unset($allNonLeaversWorkerIDs[$WORKER_ID]);
    }
}
$endPhase9 = microtime(true);
$timeMeasurements['phase_9'] = (float)($endPhase9-$startPhase9);

// At this stage, anyone still in the $allNonLeaversWorkerIDs array - has NOT been found in BP and so is now POTENTIALLY a leaver and needs to be flagged as such.
AuditTable::audit("Revalidation found " . count($allNonLeaversWorkerIDs) . " potential leavers.",AuditTable::RECORD_TYPE_REVALIDATION);

// sets potentialLeaver status
$startPhase10 = microtime(true);
$allPotentialLeaverWorkerIDsCounter = count($allNonLeaversWorkerIDs);
foreach ($allNonLeaversWorkerIDs as $WORKER_ID){
    set_time_limit(10);
    $personTable->flagPotentialLeaverByWORKER_ID($WORKER_ID);
}
$endPhase10 = microtime(true);
$timeMeasurements['phase_10'] = (float)($endPhase10-$startPhase10);

// sets leaver status
// $startPhase11 = microtime(true);
// $allLeaverCounterWorkerIDs = count($allNonLeaversWorkerIDs);
// foreach ($allNonLeaversWorkerIDs as $WORKER_ID){
//     set_time_limit(10);
//     $personTable->flagLeaverByWORKER_ID($WORKER_ID);
// }
// $endPhase11 = microtime(true);
// $timeMeasurements['phase_11'] = (float)($endPhase11-$startPhase11);

AuditTable::audit("Revalidation completed.",AuditTable::RECORD_TYPE_REVALIDATION);

/*
*
* sending notification section
*
*/

$end = microtime(true);
$timeMeasurements['overallTime'] = (float)($end-$start);

$to = array($_ENV['devemailid']);
$cc = array();
if (strstr($_ENV['environment'], 'vbac')) {
    $cc[] = 'Anthony.Stark@kyndryl.com';
    $cc[] = 'philip.bibby@kyndryl.com';
}

$subject = 'PES Revalidation timings';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/><b>Common part</b>';
$message .= '<BR/>All preBoarders ' . $allPreboardersCounter;
$message .= '<BR/>All offBoarders ' . $allOffboardersCounter;
$message .= '<BR/>All vendors ' . $allVendorsCounter;

$message .= '<HR>';

$message .= '<BR/>Time of setting a PREBOARDER revalidation status: ' . $timeMeasurements['phase_0'];
$message .= '<BR/>Time of obtaining a number of employees with PREBOARDER revalidation status: ' . $timeMeasurements['phase_1'];
$message .= '<BR/>Time of obtaining a number of employees with OFFBOARD revalidation status: ' . $timeMeasurements['phase_2'];
$message .= '<BR/>Time of obtaining a number of employees with VENDOR revalidation status: ' . $timeMeasurements['phase_3'];

$message .= '<HR>';

$message .= '<BR/>List by <b>CNUM</b> field';
$message .= '<BR/>All non leavers ' . $allNonLeaversCNUMsCounter;
$message .= '<BR/>All potential leavers ' . $allPotentialLeaverCNUMsCounter;
// $message .= '<BR/>All leavers ' . $allLeaverCounterCNUMs;

$message .= '<HR>';

$message .= '<BR/>Time of obtaining a number of employees with EMPTY or FOUND revalidation status: ' . $timeMeasurements['phase_4'];
$message .= '<BR/>Time of revalidating statuses of all none leaving employees: ' . $timeMeasurements['phase_5'];
$message .= '<BR/>Time of setting a POTENTIAL LEAVER revalidation status: ' . $timeMeasurements['phase_6'];
// $message .= '<BR/>Time of setting a LEAVER revalidation status: ' . $timeMeasurements['phase_7'];

$message .= '<HR>';

$message .= '<BR/>List by <b>Worker Id</b> field';
$message .= '<BR/>All non leavers ' . $allNonLeaversWorkerIDsCounter;
$message .= '<BR/>All potential leavers ' . $allPotentialLeaverWorkerIDsCounter;
// $message .= '<BR/>All leavers ' . $allLeaverCounterWorkerIDs;

$message .= '<HR>';

$message .= '<BR/>Time of obtaining a number of employees with EMPTY or FOUND revalidation status: ' . $timeMeasurements['phase_8'];
$message .= '<BR/>Time of revalidating statuses of all none leaving employees: ' . $timeMeasurements['phase_9'];
$message .= '<BR/>Time of setting a POTENTIAL LEAVER revalidation status: ' . $timeMeasurements['phase_10'];
// $message .= '<BR/>Time of setting a LEAVER revalidation status: ' . $timeMeasurements['phase_11'];

$message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);