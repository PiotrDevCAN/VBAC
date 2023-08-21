<?php
use itdq\Loader;
use vbac\personRecord;
use itdq\BluePages;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\BlueMail;
use itdq\DbTable;
use itdq\slack;
use vbac\pesEmail;

$slack = new slack();

AuditTable::audit("Potential Leavers re-check invoked.",AuditTable::RECORD_TYPE_REVALIDATION);
$response = $slack->slackApiPostMessage(slack::CHANNEL_SM_CDI_AUDIT,$_ENV['environment'] . ':Potential Leavers re-check invoked.');
error_log($response);

set_time_limit(60);

$personTable = new personTable(allTables::$PERSON);
$loader = new Loader();

$timeMeasurements = array();
$start =  microtime(true);

// get number of employees with potential status
$startPhase1 = microtime(true);
$potentialLeaversPredicate = " ( REVALIDATION_STATUS like '" . personRecord::REVALIDATED_POTENTIAL_BEGINNING . "%') ";
$allPotentialLeavers = $loader->load('CNUM',allTables::$PERSON, $potentialLeaversPredicate ); //
$allPotentialLeaversCounter = count($allPotentialLeavers);
AuditTable::audit("Potential Leavers re-check will re-check " . $allPotentialLeaversCounter . " potential leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
$response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Potential Leavers re-check will re-check " . $allPotentialLeaversCounter . " potential leavers.", slack::CHANNEL_SM_CDI_AUDIT);
error_log($response);
// $allPotentialLeavers = null; // free up some storage
$endPhase1 = microtime(true);
$timeMeasurements['phase_1'] = (float)($endPhase1-$startPhase1);

$chunkedCnum = array_chunk($allPotentialLeavers, 400);
$detailsFromBp = "&notesid&mail";
$bpEntries = array();

$startPhase2 = microtime(true);
foreach ($chunkedCnum as $key => $cnumList){
    $bpEntries[$key] = BluePages::getDetailsFromCnumSlapMulti($cnumList, $detailsFromBp);
    foreach ($bpEntries[$key]->search->entry as $bpEntry){
        set_time_limit(20);
        $serial = substr($bpEntry->dn,4,9);
        $mail        = ''; // Clear out previous value
        $notesid     = ''; // Clear out previous value
        foreach ($bpEntry->attribute as $details){
            $name = trim($details->name);
            $$name = trim($details->value[0]);
        }
        $notesid = str_replace(array('CN=','OU=','O='),array('','',''),$notesid);
        $personTable->confirmRevalidation($notesid,$mail,$serial);
        unset($allPotentialLeavers[$serial]);
    }
}
$endPhase2 = microtime(true);
$timeMeasurements['phase_2'] = (float)($endPhase2-$startPhase2);

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP TWICE and so is now a leaver and needs to be flagged as such.
AuditTable::audit("Potential Leavers re-check found " . count($allPotentialLeavers) . "  leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
$response = $slack->slackApiPostMessage(slack::CHANNEL_SM_CDI_AUDIT,$_ENV['environment'] . ":Potential Leavers re-check found " . count($allPotentialLeavers) . "  leavers.");
error_log($response);

// sets leaver status
$startPhase3 = microtime(true);
$allPotentialLeaversCounter = count($allPotentialLeavers);
foreach ($allPotentialLeavers as $cnum){
    set_time_limit(10);
    $personTable->flagLeaver($cnum);
}
$endPhase3 = microtime(true);
$timeMeasurements['phase_3'] = (float)($endPhase3-$startPhase3);

$startPhase4 = microtime(true);
pesEmail::notifyPesTeamOfLeavers($allPotentialLeavers);
$endPhase4 = microtime(true);
$timeMeasurements['phase_4'] = (float)($endPhase4-$startPhase4);

AuditTable::audit("Potential Leavers re-check completed.",AuditTable::RECORD_TYPE_REVALIDATION);
$response = $slack->slackApiPostMessage(slack::CHANNEL_SM_CDI_AUDIT,$_ENV['environment'] . ":Potential Leavers re-check completed.", slack::CHANNEL_SM_CDI_AUDIT);
error_log($response);

sqlsrv_commit($GLOBALS['conn']);

$end = microtime(true);
$timeMeasurements['overallTime'] = (float)($end-$start);

$to = array($_ENV['devemailid']);
$cc = array();
if (strstr($_ENV['environment'], 'vbac')) {
    $cc[] = 'Anthony.Stark@kyndryl.com';
}

$subject = 'PES Recheck Potential Leavers timings';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/>Time of obtaining a number of employees with POTENTIAL revalidation status: ' . $timeMeasurements['phase_1'];
$message .= '<BR/>All potential leavers ' . $allPotentialLeaversCounter;

$message .= '<BR/>Time of revalidating statuses of all none leaving employees: ' . $timeMeasurements['phase_2'];

$message .= '<BR/>Time of setting a LEAVER revalidation status: ' . $timeMeasurements['phase_3'];

$message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);