<?php

use itdq\Loader;
use itdq\WorkerAPI;
use itdq\AuditTable;
use itdq\BlueMail;
use itdq\DbTable;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use vbac\emails\pesTeamOfLeaversEmail;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

AuditTable::audit("Potential Leavers re-check invoked.",AuditTable::RECORD_TYPE_REVALIDATION);

set_time_limit(0);
ini_set('memory_limit','3072M');

$personTable = new personTable(allTables::$PERSON);
$loader = new Loader();
$workerAPI = new WorkerAPI();

$timeMeasurements = array();
$start =  microtime(true);

/*
*
* CNUM and Worker ID section
*
*/

// get number of employees with potential status - BY CNUM
$startPhase1 = microtime(true);
$allLeavers = array();
$predicate = " ( REVALIDATION_STATUS LIKE '" . personRecord::REVALIDATED_POTENTIAL_BEGINNING . "%')";
// $predicate .= " AND " . personTable::availableCNUMPredicate();
// $predicate .= " AND " . personTable::normalWorkerIDPredicate();
$sql = " SELECT DISTINCT P.CNUM, P.WORKER_ID, P.EMAIL_ADDRESS, P.KYN_EMAIL_ADDRESS ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
$sql.= " WHERE 1=1 AND " . $predicate;
$rs = sqlsrv_query($GLOBALS['conn'], $sql);
if($rs){
    $allPotentialLeaversCounter = 0;
    $allPotentialLeaversFoundCounter = 0;
    $allPotentialLeaversNotFoundCounter = 0;
    while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        
        $employeeFound = true;
        $allPotentialLeaversCounter++;

        $cnum = $row['CNUM'];
        $workerId = $row['WORKER_ID'];
        $email = $row['EMAIL_ADDRESS'];
        $kynEmail = $row['KYN_EMAIL_ADDRESS'];

        // first attempt by SEARCH 
        $data = $workerAPI->typeaheadSearch($kynEmail);
        if (
            ! $workerAPI->validateData($data)
        ) {
            // second attempt by Email Address
            $data = $workerAPI->getworkerByEmail($kynEmail);
            if (
                ! $workerAPI->validateData($data)
            ) {
                // third attempt by CNUM
                $data = $workerAPI->getworkerByCNUM($cnum);
                if (
                    ! $workerAPI->validateData($data)
                ) {
                    // fourth attempt by WORKER_ID
                    $data = $workerAPI->getworkerByWorkerId($workerId);
                    if (
                        ! $workerAPI->validateData($data)
                    ) {
                        $employeeFound = false;
                    }
                }
            }
        }

        if ($employeeFound == true) {
            $employeeData = $data['results'][0];
            $notesid = personRecord::NO_LONGER_AVAILABLE;
            $mail = $employeeData['email'];
            if (array_key_exists('cnum', $employeeData)) {
                $serial = $employeeData['cnum'];
            } else {
                $serial = personRecord::NO_LONGER_AVAILABLE;
            }
            $personTable->confirmRevalidation($notesid, $mail, $serial, $workerId);
            $allPotentialLeaversFoundCounter++;
        } else {
            $allLeavers[$cnum] = $row;
            $allPotentialLeaversNotFoundCounter++;
        }
    }
    /* Free the statement resources. */
    sqlsrv_free_stmt($rs);
} else {
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
    exit();
}
$endPhase1 = microtime(true);
$timeMeasurements['phase_1'] = (float)($endPhase1-$startPhase1);

AuditTable::audit("Potential Leavers re-check will re-check " . $allPotentialLeaversCounter . " potential leavers.", AuditTable::RECORD_TYPE_REVALIDATION);

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP TWICE and so is now a leaver and needs to be flagged as such.
AuditTable::audit("Potential Leavers re-check found " . $allPotentialLeaversNotFoundCounter . "  leavers.", AuditTable::RECORD_TYPE_REVALIDATION);

// sets leaver status - BY CNUM
$startPhase2 = microtime(true);
$allLeaversCounter = count($allLeavers);
foreach ($allLeavers as $cnum => $row){
    set_time_limit(10);
    $personTable->flagLeaver($row['CNUM'], $row['WORKER_ID']);
}
$endPhase2 = microtime(true);
$timeMeasurements['phase_2'] = (float)($endPhase2-$startPhase2);

// send out notification with list of leavers
$startPhase3 = microtime(true);
// $person = new personRecord();
// $email = new pesTeamOfLeaversEmail();
// $email->send($person, $allPotentialLeavers);
$endPhase3 = microtime(true);
$timeMeasurements['phase_3'] = (float)($endPhase3-$startPhase3);

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

$subject = 'PES Recheck Potential Leavers timings - NEW version';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/>Summary';
$message .= '<BR/>All potential leavers recheck ' . $allPotentialLeaversCounter;
$message .= '<BR/>All potential leavers FOUND in Worker API ' . $allPotentialLeaversNotFoundCounter;
$message .= '<BR/>All potential leavers NOT FOUND in Worker API ' . $allPotentialLeaversFoundCounter;

$message .= '<HR>';

// $message .= '<BR/>Time of obtaining a number of employees with POTENTIAL revalidation status: ' . $timeMeasurements['phase_1'];
$message .= '<BR/>Time of revalidating statuses of all none leaving employees: ' . $timeMeasurements['phase_1'];
$message .= '<BR/>Time of setting a LEAVER revalidation status: ' . $timeMeasurements['phase_2'];
$message .= '<BR/>Time of sending notifications: ' . $timeMeasurements['phase_3'];

$message .= '<HR>';

$message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);