<?php

use itdq\AuditTable;
use itdq\BlueMail;
use itdq\DbTable;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

AuditTable::audit("Check for Leavers invoked.",AuditTable::RECORD_TYPE_REVALIDATION);

set_time_limit(0);
ini_set('memory_limit','3072M');

$personTable = new personTable(allTables::$PERSON);

$predicate = " ( REVALIDATION_STATUS like '" . personRecord::REVALIDATED_OFFBOARDED . "%' AND REVALIDATION_STATUS NOT LIKE '%" . personRecord::REVALIDATED_LEAVER . "%' ) ";
$predicate.= " AND PES_STATUS IN ('" . personRecord::PES_STATUS_CLEARED . "'";
$predicate.= ",'" . personRecord::PES_STATUS_CLEARED_PERSONAL . "'";
$predicate.= ",'" . personRecord::PES_STATUS_CLEARED_AMBER . "'";
$predicate.= ",'" . personRecord::PES_STATUS_EXCEPTION. "'";
$predicate.= ",'" . personRecord::PES_STATUS_PROVISIONAL. "'";
$predicate.= ",'" . personRecord::PES_STATUS_REQUESTED. "'";
$predicate.= ",'" . personRecord::PES_STATUS_INITIATED. "'";
$predicate.= ",'" . personRecord::PES_STATUS_RECHECK_PROGRESSING. "'";
$predicate.= ",'" . personRecord::PES_STATUS_RESTART. "'";
$predicate.= ",'" . personRecord::PES_STATUS_RECHECK_REQ. "'";
$predicate.= ",'" . personRecord::PES_STATUS_MOVER. "'";
$predicate.= ")";

$sql = " SELECT DISTINCT P.CNUM, P.WORKER_ID, P.EMAIL_ADDRESS, P.KYN_EMAIL_ADDRESS ";
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
$sql.= " WHERE 1=1 AND " . $predicate;
$rs = sqlsrv_query($GLOBALS['conn'], $sql);
if($rs){
    $allCounter = 0;
    $allFoundCounter = 0;
    $allNotFoundCounter = 0;
    while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        
        $employeeFound = true;
        $allCounter++;

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
                        $allFoundCounter++;
                    } else {

                    }
                }
            }
        }

        if ($employeeFound == true) {
            $allFoundCounter++;
        } else {
            $allPotentialLeavers[$cnum] = $row;
            $allNotFoundCounter++;
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

AuditTable::audit("Check for Leavers will check " . $allCounter . " Offboarded & PES Cleared.", AuditTable::RECORD_TYPE_REVALIDATION);

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP TWICE and so is now a leaver and needs to be flagged as such.
AuditTable::audit("Check for Leavers found " . $allNotFoundCounter . "  leavers.", AuditTable::RECORD_TYPE_REVALIDATION);

foreach ($allPotentialLeavers as $cnum => $row){
    set_time_limit(10);
    $personTable->flagPotentialLeaver($row['CNUM'], $row['WORKER_ID']);
}

AuditTable::audit("Check for Leavers completed.", AuditTable::RECORD_TYPE_REVALIDATION);

/*
*
* sending notification section
*
*/

$to = array($_ENV['devemailid']);
$cc = array();
if (strstr($_ENV['environment'], 'vbac')) {
    $cc[] = 'Anthony.Stark@kyndryl.com';
    $cc[] = 'philip.bibby@kyndryl.com';
}

$subject = 'Cleared PES For Leavers check timings - NEW version';

$message = 'Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];

$message .= '<HR>';

$message .= '<BR/>Summary';
$message .= '<BR/>All PES Cleared records in scope ' . $allCounter;
$message .= '<BR/>All PES Cleared records FOUND in Worker API ' . $allFoundCounter;
$message .= '<BR/>All PES Cleared records NOT FOUND in Worker API ' . $allNotFoundCounter;

$message .= '<HR>';

$replyto = $_ENV['noreplyemailid'];
$resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);