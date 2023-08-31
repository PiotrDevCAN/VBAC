<?php

use vbac\allTables;
use vbac\personPortalReport;
use vbac\personTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

set_time_limit(0);
// ob_start();

$draw = isset($_REQUEST['draw']) ? $_REQUEST['draw'] * 1 : 1;
$start = isset($_REQUEST['start']) ? $_REQUEST['start'] * 1 : 1;
$length = isset($_REQUEST['length']) ? $_REQUEST['length'] : 50;

$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : array();
$columns = isset($_REQUEST['columns']) ? $_REQUEST['columns'] : array();
$searchValue = isset($_REQUEST['search']) && isset($_REQUEST['search']['value']) ? htmlspecialchars(trim($_REQUEST['search']['value'])) : '';

$columnsFromQuery = array(
    'CNUM',
    'OPEN_SEAT_NUMBER',
    'FIRST_NAME',
    'LAST_NAME',
    'EMAIL_ADDRESS',
    'KYN_EMAIL_ADDRESS',
    'NOTES_ID',
    'LBG_EMAIL',
    'EMPLOYEE_TYPE',
    'FM_CNUM',
    'FM_MANAGER_FLAG',
    'CTB_RTB',
    'LOB',
    'ROLE_TECHNOLOGY',
    'START_DATE',
    'PROJECTED_END_DATE',
    'COUNTRY',
    'IBM_BASE_LOCATION',
    'LBG_LOCATION',
    'OFFBOARDED_DATE',
    'PES_DATE_REQUESTED',
    'PES_REQUESTOR',
    'PES_DATE_RESPONDED',
    'PES_STATUS_DETAILS',
    'PES_STATUS',
    'REVALIDATION_DATE_FIELD',
    'REVALIDATION_STATUS',
    'PROPOSED_LEAVING_DATE',
    'CBN_DATE_FIELD',
    'CBN_STATUS',
    'CT_ID_REQUIRED',
    'CT_ID',
    'CIO_ALIGNMENT',
    'PRE_BOARDED',
    'SECURITY_EDUCATION',
    'PMO_STATUS',
    'PES_DATE_EVIDENCE',
    'RSA_TOKEN',
    'CALLSIGN_ID',
    'PES_LEVEL',
    'PES_RECHECK_DATE',
    'PES_CLEARED_DATE',
    'PROCESSING_STATUS',
    'PROCESSING_STATUS_CHANGED',
    'SKILLSET',
    'SQUAD_NUMBER',
    'SQUAD_NAME',
    'SQUAD_LEADER',
    'TRIBE_NUMBER',
    'TRIBE_NAME',
    'TRIBE_LEADER',
    'ORGANISATION',
    'ITERATION_MGR',
    // 'HAS_DELEGATES',
);

$personTable = new personTable(allTables::$PERSON);
$personPortalReport = new personPortalReport(allTables::$PERSON);

$preBoardersAction = isset($_REQUEST['preBoardersAction']) ? $_REQUEST['preBoardersAction'] : null;

// main predicate
$preBoardersPredicate = $personPortalReport->buildPreBoardersPredicate($preBoardersAction);

// search predicate
$searchPredicate = $personPortalReport->buildGlobalSearchPredicate($searchValue, $columnsFromQuery);
$searchPredicate .= $personPortalReport->buildSearchPredicate($columns);

// sorting predicate
$sortingPredicate = $personPortalReport->buildSortingPredicate($order, $columns);

$dataAndSql = $personTable->returnAsArray($start, $length, $preBoardersPredicate, $searchPredicate, $sortingPredicate);
list('data' => $data, 'sql' => $sql) = $dataAndSql;

$total = personTable::totalRows($preBoardersPredicate);
$filtered = personTable::recordsFiltered($preBoardersPredicate, $searchPredicate);

$dataJsonAble = json_encode($data);

$messages = ob_get_clean();
ob_start();

if ($dataJsonAble) {
    $response = array(
        'draw' => $draw,
        "data" => $data,
        'recordsTotal' => $total,
        'recordsFiltered' => $filtered,
        "error" => $messages,
        "sql" => $sql,
    );
} else {
    $personTable->findDirtyData();
    $dirtyDetails = ob_get_clean();
    ob_start();
    echo $dirtyDetails;
    exit();
}
ob_clean();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING'])) {
    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start("ob_html_compress");
    }
} else {
    ob_start("ob_html_compress");
}

echo json_encode($response);
