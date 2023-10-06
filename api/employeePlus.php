<?php

use vbac\allTables;
use vbac\personTable;
use itdq\DbTable;
use vbac\personRecord;
use vbac\staticDataSkillsetsRecord;

ob_start();

if($_REQUEST['token']!= $token){
    return;
}

$emailID = !empty($_GET['emailid']) ? $_GET['emailid'] : null;
$notesId = !empty($_GET['notesid']) ? $_GET['notesid'] : null;
$cnum    = !empty($_GET['cnum']) ? $_GET['cnum'] : null;

$onlyActiveStr = !empty($_GET['onlyactive']) ? $_GET['onlyactive'] : 'true';
$onlyActiveBool = $onlyActiveStr=='true';

// parameter for query - 6 or 12 months
$onlyActiveInTimeBool = false;

$onlyActiveIn6MonthsStr = !empty($_GET['onlyactive6mths']) ? $_GET['onlyactive6mths'] : 'false';
$onlyActiveIn6MonthsBool = $onlyActiveIn6MonthsStr=='true';

$onlyActiveIn12MonthsStr = !empty($_GET['onlyactive12mths']) ? $_GET['onlyactive12mths'] : 'false';
$onlyActiveIn12MonthsBool = $onlyActiveIn12MonthsStr=='true';

if ($onlyActiveIn6MonthsBool || $onlyActiveIn12MonthsBool) {

    // override the onlyactive parameter
    $onlyActiveBool = false;
    
    // parameter for query - 6 or 12 months
    $onlyActiveInTimeBool = true;

    if ($onlyActiveIn6MonthsBool) {
        $months = 6;
    }

    if ($onlyActiveIn12MonthsBool) {
        $months = 12;
    }
    // $today = new \DateTime();
    $offboardedDate = new \DateTime();
    $xMonths = new \DateInterval('P'.$months.'M');
    $offboardedDate = $offboardedDate->sub($xMonths);
    $days = $offboardedDate->format("d");
    $subxDays = new \DateInterval('P'.$days.'D');
    $offboardedDate->sub($subxDays);
}

$withProvClear = !empty($_GET['withProvClear']) ? $_GET['withProvClear'] : null;

if (isset($_REQUEST['plus'])) {
    strtoupper($_REQUEST['plus']);
}

$additionalFields = !empty($_REQUEST['plus']) ? explode(",", $_REQUEST['plus']) : null;
$additionalSelect = null;
$employees = array();

if (!is_null($additionalFields)) {

    $personRecord = new personRecord();
    $availableColumns = $personRecord->getColumns();
    $personTableAliases = array('P.');

    $skillsetRecord = new staticDataSkillsetsRecord();
    $skillsetRecordColumns = $skillsetRecord->getColumns();
    $skillseTableAliases = array('SS.');

    foreach ($additionalFields as $field) {

        // validate field against PERSON table
        $tableField = str_replace($personTableAliases, '', $field);

        if (array_key_exists($tableField, $availableColumns)) {
            $additionalSelect .= ", " . htmlspecialchars("P.".$tableField);
            continue;
        }

        // validate field against STATIC_SKILLSET table
        $tableField = str_replace($skillseTableAliases, '', $field);

        if (array_key_exists($tableField, $skillsetRecordColumns)) {
            $additionalSelect .= ", " . htmlspecialchars("SS.".$tableField);
            continue;
        }
    }
}

$sql = " SELECT DISTINCT P.NOTES_ID, ";
$sql.=" CASE WHEN " . personTable::activePersonPredicate($withProvClear, 'P') . " THEN 'active' ELSE 'inactive' END AS INT_STATUS ";
$sql.= $additionalSelect;
$sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " AS P ";
$sql.= " LEFT JOIN " .  $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_SKILLSETS . " as SS ";
$sql.= " ON P.SKILLSET_ID = SS.SKILLSET_ID ";
$sql.= " WHERE 1=1 AND trim(NOTES_ID) != '' ";
$sql.= $onlyActiveBool ? " AND " . personTable::activePersonPredicate($withProvClear, 'P') : null;
$sql.= $onlyActiveInTimeBool ? " AND (" . personTable::activePersonPredicate($withProvClear, 'P') . " OR P.OFFBOARDED_DATE > '" . $offboardedDate->format('Y-m-d') . "')" : null;
$sql.= !empty($emailID) ? " AND (lower(P.EMAIL_ADDRESS) = '" . htmlspecialchars(strtolower($emailID)) . "' OR lower(P.KYN_EMAIL_ADDRESS) = '" . htmlspecialchars(strtolower($emailID)) . "') " : null;
$sql.= !empty($notesId) ? " AND lower(P.NOTES_ID) = '" . htmlspecialchars(strtolower($notesId)) . "'; " : null;
$sql.= !empty($cnum) ? " AND lower(P.CNUM) = '" . htmlspecialchars(strtolower($cnum)) . "'; " : null;
$sql.= " ORDER BY P.NOTES_ID ";

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

if($rs){
    while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
        $employees[] = array_map('trim', $row);
    }
} else {
    ob_clean();
    ob_start();
    DbTable::displayErrorMessage($rs, 'class', 'method', $sql);
    $errorMessage = ob_get_clean();
    echo json_encode($errorMessage);
}

$employees = count($employees)==1 ? $employees[0] : $employees;

ob_clean();
header('Content-Type: application/json');
echo json_encode($employees);
