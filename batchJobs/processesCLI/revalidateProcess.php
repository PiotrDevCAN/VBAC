<?php

use itdq\Loader;
use itdq\WorkerAPI;
use itdq\AuditTable;
use itdq\BlueMail;
use itdq\slack;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $slack = new slack();

AuditTable::audit("Revalidation invoked.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation invoked.");

set_time_limit(0);
ini_set('memory_limit','6144M');

$personTable = new personTable(allTables::$PERSON);

$loader = new Loader();
$timeMeasurements = array();
$start =  microtime(true);

// sets preboarder status
$startPhase0 = microtime(true);
$personTable->flagPreboarders();
$endPhase0 = microtime(true);
$timeMeasurements['phase_0'] = (float)($endPhase0-$startPhase0);

// get number of employees with offboard status
$startPhase1 = microtime(true);
$offboardersPredicate = " ( REVALIDATION_STATUS like '" . personRecord::REVALIDATED_OFFBOARD . "%') ";
$allOffboarders = $loader->load('CNUM',allTables::$PERSON, $offboardersPredicate ); //
$allOffboardersCounter = count($allOffboarders);
AuditTable::audit("Revalidation will ignore " . $allOffboardersCounter . " offboarding/ed.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation will ignore " .$allOffboardersCounter . " offboarding/ed.");
$allOffboarders = null; // free up some storage
$endPhase1 = microtime(true);
$timeMeasurements['phase_1'] = (float)($endPhase1-$startPhase1);

// get number of employees with preboarder status
$startPhase2 = microtime(true);
$preBoardersPredicate = " ( trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_PREBOARDER . "') ";
$allPreboarders = $loader->load('CNUM',allTables::$PERSON, $preBoardersPredicate );
$allPreboardersCounter = count($allPreboarders);
AuditTable::audit("Revalidation will ignore " . $allPreboardersCounter . " pre-boarders.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation will ignore " . $allPreboardersCounter . " pre-boarders.");
$allPreboarders = null; // free up some storage
$endPhase2 = microtime(true);
$timeMeasurements['phase_2'] = (float)($endPhase2-$startPhase2);

// get number of employees with vendor status
$startPhase3 = microtime(true);
$vendorsPredicate = " ( trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_VENDOR . "') ";
$allVendors = $loader->load('CNUM',allTables::$PERSON, $vendorsPredicate ); //
$allVendorsCounter = count($allVendors);
AuditTable::audit("Revalidation will ignore " . $allVendorsCounter . " vendors.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation will ignore " . $allVendorsCounter . " vendors.");
$allVendors = null; // free up some storage
$endPhase3 = microtime(true);
$timeMeasurements['phase_3'] = (float)($endPhase3-$startPhase3);

// get number of employees with empty OR null OR found status
$startPhase4 = microtime(true);
$activeIbmErsPredicate = " ( trim(REVALIDATION_STATUS) = '' or REVALIDATION_STATUS is null or trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_FOUND . "') ";
$allNonLeavers = $loader->load('CNUM',allTables::$PERSON, $activeIbmErsPredicate );
$allNonLeaversCounter = count($allNonLeavers);
AuditTable::audit("Revalidation will check " . $allNonLeaversCounter . " people currently flagged as found.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation will check " . $allNonLeaversCounter . " people currently flagged as found.");
$endPhase4 = microtime(true);
$timeMeasurements['phase_4'] = (float)($endPhase4-$startPhase4);

$startPhase5 = microtime(true);
$workerAPI = new WorkerAPI();
foreach ($allNonLeavers as $key => $CNUM) {
    $data = $workerAPI->getworkerByCNUM($CNUM);
    if (array_key_exists('count', $data) && $data['count'] > 0) {
        $employeeData = $data['results'][0];
        $notesid = 'No longer available';
        $mail = $employeeData['email'];
        $serial = $employeeData['cnum'];
        $personTable->confirmRevalidation($notesid,$mail,$serial);
        unset($allNonLeavers[$serial]);
    }
}
$endPhase5 = microtime(true);
$timeMeasurements['phase_5'] = (float)($endPhase5-$startPhase5);

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP and so is now POTENTIALLY a leaver and needs to be flagged as such.
AuditTable::audit("Revalidation found " . count($allNonLeavers) . " potential leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation found " . count($allNonLeavers) . " potential leavers.");

// sets potentialLeaver status
$startPhase6 = microtime(true);
$allPotentialLeaverCounter = count($allNonLeavers);
foreach ($allNonLeavers as $cnum){
    set_time_limit(10);
    $personTable->flagPotentialLeaver($cnum);
}
$endPhase6 = microtime(true);
$timeMeasurements['phase_6'] = (float)($endPhase6-$startPhase6);

// sets leaver status
// $startPhase7 = microtime(true);
// $allLeaverCounter = count($allNonLeavers);
// foreach ($allNonLeavers as $cnum){
//     set_time_limit(10);
//     $personTable->flagLeaver($cnum);
// }
// $endPhase7 = microtime(true);
// $timeMeasurements['phase_7'] = (float)($endPhase7-$startPhase7);

AuditTable::audit("Revalidation completed.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation completed.");

$end = microtime(true);
$timeMeasurements['overallTime'] = (float)($end-$start);

$to = array($_ENV['devemailid']);
$cc = array();
if (strstr($_ENV['environment'], 'vbac')) {
    $cc[] = 'Anthony.Stark@kyndryl.com';
}

$subject = 'PES Revalidation timings';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/>Time of setting a PREBOARDER revalidation status: ' . $timeMeasurements['phase_0'];

$message .= '<BR/>Time of obtaining a number of employees with OFFBOARD revalidation status: ' . $timeMeasurements['phase_1'];
$message .= '<BR/>All offBoarders ' . $allOffboardersCounter;

$message .= '<BR/>Time of obtaining a number of employees with PREBOARDER revalidation status: ' . $timeMeasurements['phase_2'];
$message .= '<BR/>All preBoarders ' . $allPreboardersCounter;

$message .= '<BR/>Time of obtaining a number of employees with VENDOR revalidation status: ' . $timeMeasurements['phase_3'];
$message .= '<BR/>All vendors ' . $allVendorsCounter;

$message .= '<BR/>Time of obtaining a number of employees with EMPTY or FOUND revalidation status: ' . $timeMeasurements['phase_4'];
$message .= '<BR/>All non leavers ' . $allNonLeaversCounter;

$message .= '<BR/>Time of revalidating statuses of all none leaving employees: ' . $timeMeasurements['phase_5'];

$message .= '<BR/>Time of setting a POTENTIAL LEAVER revalidation status: ' . $timeMeasurements['phase_6'];
$message .= '<BR/>All potential leavers ' . $allPotentialLeaverCounter;

// $message .= '<BR/>Time of setting a LEAVER revalidation status: ' . $timeMeasurements['phase_7'];
// $message .= '<BR/>All leavers ' . $allLeaverCounter;

$message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);