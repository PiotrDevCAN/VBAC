<?php

use itdq\DbTable;
use vbac\allTables;
use vbac\AgileSquadRecord;
use vbac\AgileTribeRecord;
use vbac\personRecord;
use vbac\personTable;
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
// default fields
$additionalSelect = " P.NOTES_ID, P.EMAIL_ADDRESS, P.KYN_EMAIL_ADDRESS, P.FIRST_NAME, P.LAST_NAME ";
$additionalSelect .= ", " . personTable::FULLNAME_SELECT;
$additionalSelect .= ", AS1.SQUAD_NUMBER, AS1.SQUAD_NAME, AT.TRIBE_NUMBER, AT.TRIBE_NAME, ";
$additionalSelect .= personTable::getStatusSelect($withProvClear, 'P');
$employees = array();

if (!is_null($additionalFields)) {

    $personRecord = new personRecord();
    $availablePersonColumns = $personRecord->getColumns();
    $personTableAliases = array('P.', 'F.', 'U.');

    $agileSquadRecord = new AgileSquadRecord();
    $availableAgileSquadColumns = $agileSquadRecord->getColumns();
    $agileSquadTableAliases = array('AS1.');

    $agileTribeRecord = new AgileTribeRecord();
    $availableAgileTribeColumns = $agileTribeRecord->getColumns();
    $agileTribeTableAliases = array('AT.');

    $skillsetRecord = new staticDataSkillsetsRecord();
    $skillsetRecordColumns = $skillsetRecord->getColumns();
    $skillsetTableAliases = array('SS.');

    foreach ($additionalFields as $field) {

        $field = trim($field);

        // an additional mapping
        switch($field) {
            case 'ORGANISATION':
                $fieldExpression = personTable::ORGANISATION_SELECT;
                $additionalSelect .= ", " . htmlspecialchars($fieldExpression);
                continue 2;
                break;
            case 'FM':
                $fieldExpression = personTable::FLM_SELECT;
                $additionalSelect .= ", " . htmlspecialchars($fieldExpression);
                continue 2;
                break;
            case 'SM':
                $fieldExpression = personTable::SLM_SELECT;
                $additionalSelect .= ", " . htmlspecialchars($fieldExpression);
                continue 2;
                break;
            case 'EMPLOYEE_TYPE':
                $fieldExpression = personTable::EMPLOYEE_TYPE_SELECT;
                $additionalSelect .= ", " . htmlspecialchars($fieldExpression);
                continue 2;
                break;
            case 'JRSS':
                $additionalSelect .= ", SS.SKILLSET AS JRSS";
                continue 2;
                break;
            case 'FLL_CNUM':
                $additionalSelect .= ", F.CNUM as FLL_CNUM";
                continue 2;
                break;
            case 'FLL_NOTES_ID':
                $additionalSelect .= ", F.NOTES_ID as FLL_NOTES_ID";
                continue 2;
                break;
            case 'FLL_EMAIL':
                $additionalSelect .= ", F.KYN_EMAIL_ADDRESS AS FLL_EMAIL";
                continue 2;
                break;
            case 'FLM_EMAIL_ADDRESS':
                $additionalSelect .= ", F.KYN_EMAIL_ADDRESS AS FLM_EMAIL_ADDRESS";
                continue 2;
                break;
            case 'SLL_CNUM':
                $additionalSelect .= ", U.CNUM as SLL_CNUM";
                continue 2;
                break;
            case 'SLL_NOTES_ID':
                $additionalSelect .= ", U.NOTES_ID as SLL_NOTES_ID";
                continue 2;
                break;
            case 'SLL_EMAIL':
                $additionalSelect .= ", U.KYN_EMAIL_ADDRESS AS SLL_EMAIL";
                continue 2;
                break;
            case 'SLM_EMAIL_ADDRESS':
                $additionalSelect .= ", U.KYN_EMAIL_ADDRESS AS SLM_EMAIL_ADDRESS";
                continue 2;
                break;
            case 'ITERATION_MGR':
                $additionalSelect .= ", AT.ITERATION_MGR AS ITERATION_MGR";
                continue 2;
                break;
            default:
                break;
        }

        // validate field against PERSON table
        $tableField = str_replace($personTableAliases, '', $field);

        if (array_key_exists($tableField, $availablePersonColumns)) {
            $additionalSelect .= ", " . htmlspecialchars("P.".$tableField);
            continue;
        }

        // validate field against AGILE_SQUAD table
        $tableField = str_replace($agileSquadTableAliases, '', $field);

        if (array_key_exists($tableField, $availableAgileSquadColumns)) {
            $additionalSelect .= ", " . htmlspecialchars("AS1.".$tableField);
            continue;
        }

        // validate field against AGILE_TRIBE table
        $tableField = str_replace($agileTribeTableAliases, '', $field);

        if (array_key_exists($tableField, $availableAgileTribeColumns)) {
            $additionalSelect .= ", " . htmlspecialchars("AT.".$tableField);
            continue;
        }

        // validate field against STATIC_SKILLSET table
        $tableField = str_replace($skillsetTableAliases, '', $field);

        if (array_key_exists($tableField, $skillsetRecordColumns)) {
            $additionalSelect .= ", " . htmlspecialchars("SS.".$tableField);
            continue;
        }
    }
}

$sql = " SELECT DISTINCT ";
$sql.= $additionalSelect;
$sql.= personTable::getTablesForQuery();
$sql.= " WHERE 1=1 AND trim(P.KYN_EMAIL_ADDRESS) != '' ";
$sql.= $onlyActiveBool ? " AND " . personTable::activePersonPredicate($withProvClear, 'P') : null;
$sql.= $onlyActiveInTimeBool ? " AND (" . personTable::activePersonPredicate($withProvClear, 'P') . " OR P.OFFBOARDED_DATE > '" . $offboardedDate->format('Y-m-d') . "')" : null;
$sql.= !empty($emailID) ? " AND (lower(P.EMAIL_ADDRESS) = '" . htmlspecialchars(strtolower($emailID)) . "' OR lower(P.KYN_EMAIL_ADDRESS) = '" . htmlspecialchars(strtolower($emailID)) . "') " : null;
$sql.= !empty($notesId) ? " AND lower(P.NOTES_ID) = '" . htmlspecialchars(strtolower($notesId)) . "'  " : null;
$sql.= !empty($cnum) ? " AND lower(P.CNUM) = '" . htmlspecialchars(strtolower($cnum)) . "'  " : null;
$sql.= " ORDER BY P.KYN_EMAIL_ADDRESS ";

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
    exit();
}

$employees = count($employees)==1 ? $employees[0] : $employees;

ob_clean();
header('Content-Type: application/json');
echo json_encode($employees);
