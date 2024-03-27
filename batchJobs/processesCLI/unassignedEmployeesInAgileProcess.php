<?php

use itdq\BlueMail;
use vbac\personTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
ini_set('memory_limit','3072M');

$_ENV['email'] = 'on';

// require_once __DIR__ . '/../../src/Bootstrap.php';
// $helper = new Sample();
// if ($helper->isCli()) {
//     $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
//     return;
// }

$noreplemailid = $_ENV['noreplykyndrylemailid'];
$emailAddress = array(
    'divya.a.s@kyndryl.com',
    'philip.bibby@kyndryl.com',
    $_ENV['automationemailid']
);
$emailAddressCC = array();
$emailAddressBCC = array();

try {
    
    $withProvClear = true;
    $onlyPrimarySquadBool = false;

    // default fields
    $additionalSelect = " P.CNUM, P.WORKER_ID, P.NOTES_ID, P.EMAIL_ADDRESS, P.KYN_EMAIL_ADDRESS, P.FIRST_NAME, P.LAST_NAME, ";
    $additionalSelect .= personTable::FULLNAME_SELECT . ", ";
    $additionalSelect .= " AS1.SQUAD_NUMBER, AS1.SQUAD_NAME, AT.TRIBE_NUMBER, AT.TRIBE_NAME, ";
    $additionalSelect .= personTable::getStatusSelect($withProvClear, 'P');

    $sql = " SELECT DISTINCT ";
    $sql.= $additionalSelect;
    $sql.= personTable::getTablesForQuery($onlyPrimarySquadBool);
    $sql.= " WHERE 1=1 AND trim(P.KYN_EMAIL_ADDRESS) != '' ";
    $sql.= " AND " . personTable::activePersonPredicate($withProvClear, 'P');
    $sql.= " AND EA.TYPE IS NULL ";
    $sql.= " ORDER BY P.KYN_EMAIL_ADDRESS ";

    $totalRecords = 0;

    $rs = sqlsrv_query( $GLOBALS['conn'], $sql );
    if (! $rs) {
        error_log("<BR>" . json_encode(sqlsrv_errors()));
        error_log("<BR>" . json_encode(sqlsrv_errors()) . "<BR>");
        exit ( "Error in: " . __METHOD__ . " running: COMMIT " );
    }

    $totalResponse = '';
    while($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        $totalRecords++;
        $cnum = trim($row['CNUM']);
        $workerId = trim($row['WORKER_ID']);
        $email = trim($row['EMAIL_ADDRESS']);
        $fullName = trim($row['FULL_NAME']);
        $totalResponse .= "<br>(" . $cnum . ") (" . $workerId . ") (" . $email . ") (" . $fullName . ") - Squad Name is null";
    }
    
    $subject = 'Validate Employees to Agile Squad / Tribe assignment';
    $message = 'Validate Employees to Agile Squad / Tribe assignment script has completed.';
    $message .= '<br>Amount of records not assigned to any Agile Squad / Tribe (Squad Number is null): ' . $totalRecords;
    $message .= $totalResponse;
    $result = BlueMail::send_mail($emailAddress, $subject, $message, $noreplemailid, $emailAddressCC, $emailAddressBCC);
    
} catch (Exception $e) {
    $subject = 'Error in: Validate Employees to Agile Squad / Tribe assignment ';
    $message = $e->getMessage() . ' ' . $e->getLine() . ' ' . $e->getFile();

    $to = array($_ENV['automationemailid']);
    $cc = array();
    $replyto = $_ENV['noreplyemailid'];
    
    $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
    trigger_error($subject . " - ". $message, E_USER_ERROR);
}
