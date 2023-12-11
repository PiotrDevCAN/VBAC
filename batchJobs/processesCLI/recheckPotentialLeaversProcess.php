<?php

use itdq\Loader;
use itdq\WorkerAPI;
use itdq\AuditTable;
use itdq\BlueMail;
use vbac\pesEmail;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

AuditTable::audit("Potential Leavers re-check invoked.",AuditTable::RECORD_TYPE_REVALIDATION);

set_time_limit(0);
ini_set('memory_limit','2048M');

$personTable = new personTable(allTables::$PERSON);
$loader = new Loader();
$workerAPI = new WorkerAPI();

$timeMeasurements = array();
$start =  microtime(true);

/*
*
* CNUM section
*
*/

// get number of employees with potential status - BY CNUM
$startPhase1 = microtime(true);
$potentialLeaversPredicate = " ( REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_POTENTIAL_BEGINNING . "%')";
$potentialLeaversPredicate .= " AND " . personTable::availableCNUMPredicate();
$allPotentialLeaversCNUMs = $loader->load('CNUM',allTables::$PERSON, $potentialLeaversPredicate );
$allPotentialLeaversCNUMsCounterStart = count($allPotentialLeaversCNUMs);
$endPhase1 = microtime(true);
$timeMeasurements['phase_1'] = (float)($endPhase1-$startPhase1);

AuditTable::audit("Potential Leavers re-check will re-check " . $allPotentialLeaversCNUMsCounterStart . " potential leavers.",AuditTable::RECORD_TYPE_REVALIDATION);

// check if employee has a record in Worker API - BY CNUM
$startPhase2 = microtime(true);
foreach ($allPotentialLeaversCNUMs as $key => $CNUM) {
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
        unset($allPotentialLeaversCNUMs[$serial]);
    }
}
$endPhase2 = microtime(true);
$timeMeasurements['phase_2'] = (float)($endPhase2-$startPhase2);

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP TWICE and so is now a leaver and needs to be flagged as such.
AuditTable::audit("Potential Leavers re-check found " . count($allPotentialLeaversCNUMs) . "  leavers.",AuditTable::RECORD_TYPE_REVALIDATION);

// sets leaver status - BY CNUM
$startPhase3 = microtime(true);
$allPotentialLeaversCNUMsCounterEnd = count($allPotentialLeaversCNUMs);
foreach ($allPotentialLeaversCNUMs as $cnum){
    set_time_limit(10);
    $personTable->flagLeaverByCNUM($cnum);
}
$endPhase3 = microtime(true);
$timeMeasurements['phase_3'] = (float)($endPhase3-$startPhase3);

// send out notification with list of leavers
$startPhase4 = microtime(true);
// pesEmail::notifyPesTeamOfLeavers($allPotentialLeaversCNUMs);
$endPhase4 = microtime(true);
$timeMeasurements['phase_4'] = (float)($endPhase4-$startPhase4);

AuditTable::audit("Potential Leavers re-check completed.",AuditTable::RECORD_TYPE_REVALIDATION);

/*
*
* Worker ID section
*
*/

// get number of employees with potential status - BY WORKER_ID
$startPhase5 = microtime(true);
$potentialLeaversPredicate = " ( REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_POTENTIAL_BEGINNING . "%')";
$potentialLeaversPredicate .= " AND " . personTable::normalWorkerIDPredicate();
$allPotentialLeaversWorkerIDs = $loader->load('WORKER_ID',allTables::$PERSON, $potentialLeaversPredicate );
$allPotentialLeaversWorkerIDsCounterStart = count($allPotentialLeaversWorkerIDs);
$endPhase5 = microtime(true);
$timeMeasurements['phase_5'] = (float)($endPhase5-$startPhase5);

AuditTable::audit("Potential Leavers re-check will re-check " . $allPotentialLeaversWorkerIDsCounterStart . " potential leavers.",AuditTable::RECORD_TYPE_REVALIDATION);

// check if employee has a record in Worker API - BY WORKER_ID
$startPhase6 = microtime(true);
foreach ($allPotentialLeaversWorkerIDs as $key => $WORKER_ID) {
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
        unset($allPotentialLeaversWorkerIDs[$WORKER_ID]);
    }
}
$endPhase6 = microtime(true);
$timeMeasurements['phase_6'] = (float)($endPhase6-$startPhase6);

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP TWICE and so is now a leaver and needs to be flagged as such.
AuditTable::audit("Potential Leavers re-check found " . count($allPotentialLeaversWorkerIDs) . "  leavers.",AuditTable::RECORD_TYPE_REVALIDATION);

// sets leaver status - BY WORKER_ID
$startPhase7 = microtime(true);
$allPotentialLeaversWorkerIDsCounterEnd = count($allPotentialLeaversWorkerIDs);
foreach ($allPotentialLeaversWorkerIDs as $WORKER_ID){
    set_time_limit(10);
    $personTable->flagLeaverByWORKER_ID($WORKER_ID);
}
$endPhase7 = microtime(true);
$timeMeasurements['phase_7'] = (float)($endPhase7-$startPhase7);

// send out notification with list of leavers
$startPhase8 = microtime(true);
// pesEmail::notifyPesTeamOfLeavers($allPotentialLeaversWorkerIDs);
$endPhase8 = microtime(true);
$timeMeasurements['phase_8'] = (float)($endPhase8-$startPhase8);

AuditTable::audit("Potential Leavers re-check completed.",AuditTable::RECORD_TYPE_REVALIDATION);

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

$subject = 'PES Recheck Potential Leavers timings';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/>List by <b>CNUM</b> field';
$message .= '<BR/>All potential leavers before recheck ' . $allPotentialLeaversCNUMsCounterStart;
$message .= '<BR/>All potential leavers left upon recheck ' . $allPotentialLeaversCNUMsCounterEnd;

$message .= '<HR>';

$message .= '<BR/>Time of obtaining a number of employees with POTENTIAL revalidation status: ' . $timeMeasurements['phase_1'];
$message .= '<BR/>Time of revalidating statuses of all none leaving employees: ' . $timeMeasurements['phase_2'];
$message .= '<BR/>Time of setting a LEAVER revalidation status: ' . $timeMeasurements['phase_3'];

$message .= '<HR>';

$message .= '<BR/>List by <b>Worker Id</b> field';
$message .= '<BR/>All potential leavers before recheck ' . $allPotentialLeaversWorkerIDsCounterStart;
$message .= '<BR/>All potential leavers left upon recheck ' . $allPotentialLeaversWorkerIDsCounterEnd;

$message .= '<HR>';

$message .= '<BR/>Time of obtaining a number of employees with POTENTIAL revalidation status: ' . $timeMeasurements['phase_5'];
$message .= '<BR/>Time of revalidating statuses of all none leaving employees: ' . $timeMeasurements['phase_6'];
$message .= '<BR/>Time of setting a LEAVER revalidation status: ' . $timeMeasurements['phase_7'];

$message .= '<HR>';

$message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);