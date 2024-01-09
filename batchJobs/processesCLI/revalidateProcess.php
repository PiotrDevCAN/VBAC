<?php

use itdq\Loader;
use itdq\WorkerAPI;
use itdq\AuditTable;
use itdq\BlueMail;
use itdq\DbTable;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

AuditTable::audit("Revalidation invoked.",AuditTable::RECORD_TYPE_REVALIDATION);

set_time_limit(0);
ini_set('memory_limit','3072M');

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
* CNUM and Worker ID section
*
*/

$startPhase4 = microtime(true);
$allPotentialLeavers = array();
$predicate = " ( trim(REVALIDATION_STATUS) = '' OR REVALIDATION_STATUS IS NULL OR trim(REVALIDATION_STATUS) = '" . personRecord::REVALIDATED_FOUND . "') ";
// $predicate .= " AND " . personTable::availableCNUMPredicate();
// $predicate .= " AND " . personTable::normalWorkerIDPredicate();
$sql = " SELECT DISTINCT P.CNUM, P.WORKER_ID, P.EMAIL_ADDRESS, P.KYN_EMAIL_ADDRESS ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
$sql.= " WHERE 1=1 AND " . $predicate;
$rs = sqlsrv_query($GLOBALS['conn'], $sql);
if($rs){
    $allNonLeaversCounter = 0;
    $allNonLeaversFoundCounter = 0;
    $allNonLeaversNotFoundCounter = 0;
    while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        
        $employeeFound = true;
        $allNonLeaversCounter++;

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
            $allNonLeaversFoundCounter++;
        } else {
            $allPotentialLeavers[$cnum] = $row;
            $allNonLeaversNotFoundCounter++;
        }
    }
} else {
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
    exit();
}
$endPhase4 = microtime(true);
$timeMeasurements['phase_4'] = (float)($endPhase4-$startPhase4);

// iterate through these employees' records
AuditTable::audit("Revalidation will check " . $allNonLeaversCounter . " people currently flagged as found.", AuditTable::RECORD_TYPE_REVALIDATION);

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP and so is now POTENTIALLY a leaver and needs to be flagged as such.
AuditTable::audit("Revalidation found " . $allNonLeaversFoundCounter . " potential leavers.", AuditTable::RECORD_TYPE_REVALIDATION);

// sets potentialLeaver status
$startPhase5 = microtime(true);
$allPotentialLeaversCounter = count($allPotentialLeavers);
foreach ($allPotentialLeavers as $cnum => $row){
    set_time_limit(10);
    $personTable->flagPotentialLeaver($row['CNUM'], $row['WORKER_ID']);
}
$endPhase5 = microtime(true);
$timeMeasurements['phase_5'] = (float)($endPhase5-$startPhase5);

    // sets leaver status
    // $startPhase6 = microtime(true);
    // $allLeaverCounter = count($allNonLeavers);
    // foreach ($allNonLeavers as $cnum => $row){
    //     set_time_limit(10);
    //     $personTable->flagLeaver($row['CNUM'], $row['WORKER_ID']);
    // }
    // $endPhase6 = microtime(true);
    // $timeMeasurements['phase_6'] = (float)($endPhase6-$startPhase6);

AuditTable::audit("Revalidation completed.", AuditTable::RECORD_TYPE_REVALIDATION);

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

$subject = 'PES Revalidation timings - NEW version';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/><b>Summary</b>';

$message .= '<HR>';

$message .= '<BR/>All preBoarders ' . $allPreboardersCounter;
$message .= '<BR/>All offBoarders ' . $allOffboardersCounter;
$message .= '<BR/>All vendors ' . $allVendorsCounter;

$message .= '<HR>';

$message .= '<BR/>All non leavers ' . $allNonLeaversCounter;
$message .= '<BR/>All non leavers FOUND in Worker API ' . $allNonLeaversFoundCounter;
$message .= '<BR/>All non leavers NOT FOUND in Worker API ' . $allNonLeaversNotFoundCounter;

$message .= '<BR/>All potential leavers ' . $allPotentialLeaversCounter;
// $message .= '<BR/>All leavers ' . $allLeaverCounter;

$message .= '<HR>';

$message .= '<BR/><b>Timing summary</b>';

$message .= '<HR>';

$message .= '<BR/>Time of setting a PREBOARDER revalidation status: ' . $timeMeasurements['phase_0'];
$message .= '<BR/>Time of obtaining a number of employees with PREBOARDER revalidation status: ' . $timeMeasurements['phase_1'];
$message .= '<BR/>Time of obtaining a number of employees with OFFBOARD revalidation status: ' . $timeMeasurements['phase_2'];
$message .= '<BR/>Time of obtaining a number of employees with VENDOR revalidation status: ' . $timeMeasurements['phase_3'];

$message .= '<HR>';

// $message .= '<BR/>Time of obtaining a number of employees with EMPTY or FOUND revalidation status: ' . $timeMeasurements['phase_4'];
$message .= '<BR/>Time of revalidating statuses of all none leaving employees: ' . $timeMeasurements['phase_4'];
$message .= '<BR/>Time of setting a POTENTIAL LEAVER revalidation status: ' . $timeMeasurements['phase_5'];
// $message .= '<BR/>Time of setting a LEAVER revalidation status: ' . $timeMeasurements['phase_7'];

$message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);