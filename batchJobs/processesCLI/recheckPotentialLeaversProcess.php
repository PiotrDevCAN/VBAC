<?php

use itdq\Loader;
use itdq\WorkerAPI;
use itdq\AuditTable;
use itdq\BlueMail;
use itdq\slack;
use vbac\pesEmail;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $slack = new slack();

AuditTable::audit("Potential Leavers re-check invoked.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_SM_CDI_AUDIT,$_ENV['environment'] . ':Potential Leavers re-check invoked.');

set_time_limit(0);
ini_set('memory_limit','2048M');

$personTable = new personTable(allTables::$PERSON);
$loader = new Loader();

$timeMeasurements = array();
$start =  microtime(true);

// get number of employees with potential status
$startPhase1 = microtime(true);
$potentialLeaversPredicate = " ( REVALIDATION_STATUS like '" . personRecord::REVALIDATED_POTENTIAL_BEGINNING . "%') ";
$allPotentialLeavers = $loader->load('CNUM',allTables::$PERSON, $potentialLeaversPredicate );
$allPotentialLeaversCounterStart = count($allPotentialLeavers);
// $allPotentialLeavers = null; // free up some storage
$endPhase1 = microtime(true);
$timeMeasurements['phase_1'] = (float)($endPhase1-$startPhase1);

AuditTable::audit("Potential Leavers re-check will re-check " . $allPotentialLeaversCounterStart . " potential leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Potential Leavers re-check will re-check " . $allPotentialLeaversCounterStart . " potential leavers.", slack::CHANNEL_SM_CDI_AUDIT);

// check if employee has a record in BluePages
$startPhase2 = microtime(true);
$workerAPI = new WorkerAPI();
foreach ($allPotentialLeavers as $key => $CNUM) {
    $data = $workerAPI->getworkerByCNUM($CNUM);
    if (
        is_array($data) 
        && array_key_exists('count', $data) 
        && $data['count'] > 0
    ) {
        $employeeData = $data['results'][0];
        $notesid = 'No longer available';
        $mail = $employeeData['email'];
        $serial = $employeeData['cnum'];
        $personTable->confirmRevalidation($notesid, $mail, $serial);
        unset($allPotentialLeavers[$serial]);
    }
}
$endPhase2 = microtime(true);
$timeMeasurements['phase_2'] = (float)($endPhase2-$startPhase2);

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP TWICE and so is now a leaver and needs to be flagged as such.
AuditTable::audit("Potential Leavers re-check found " . count($allPotentialLeavers) . "  leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_SM_CDI_AUDIT,$_ENV['environment'] . ":Potential Leavers re-check found " . count($allPotentialLeavers) . "  leavers.");

// sets leaver status
$startPhase3 = microtime(true);
$allPotentialLeaversCounterEnd = count($allPotentialLeavers);
foreach ($allPotentialLeavers as $cnum){
    set_time_limit(10);
    $personTable->flagLeaver($cnum);
}
$endPhase3 = microtime(true);
$timeMeasurements['phase_3'] = (float)($endPhase3-$startPhase3);

// send out notification with list of leavers
$startPhase4 = microtime(true);
// pesEmail::notifyPesTeamOfLeavers($allPotentialLeavers);
$endPhase4 = microtime(true);
$timeMeasurements['phase_4'] = (float)($endPhase4-$startPhase4);

AuditTable::audit("Potential Leavers re-check completed.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_SM_CDI_AUDIT,$_ENV['environment'] . ":Potential Leavers re-check completed.", slack::CHANNEL_SM_CDI_AUDIT);

$end = microtime(true);
$timeMeasurements['overallTime'] = (float)($end-$start);

$to = array($_ENV['devemailid']);
$cc = array();
if (strstr($_ENV['environment'], 'vbac')) {
    // $cc[] = 'Anthony.Stark@kyndryl.com';
    $cc[] = 'Piotr.Tajanowicz@kyndryl.com';
}

$subject = 'PES Recheck Potential Leavers timings';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/>All potential leavers before recheck ' . $allPotentialLeaversCounterStart;
$message .= '<BR/>All potential leavers left upon recheck ' . $allPotentialLeaversCounterEnd;

$message .= '<HR>';

$message .= '<BR/>Time of obtaining a number of employees with POTENTIAL revalidation status: ' . $timeMeasurements['phase_1'];
$message .= '<BR/>Time of revalidating statuses of all none leaving employees: ' . $timeMeasurements['phase_2'];
$message .= '<BR/>Time of setting a LEAVER revalidation status: ' . $timeMeasurements['phase_3'];
$message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);