<?php

use vbac\personTable;
use vbac\allTables;
use itdq\Loader;
use itdq\DbTable;
use itdq\AuditTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</pre>",AuditTable::RECORD_TYPE_DETAILS);

if($_REQUEST['token']!= $token){
    return;
}

// validate parameters will fit into the columns, without hard coding the column length here.

$personTable = new personTable(allTables::$PERSON);
$loader = new Loader();

$cnumValidated = true;
$validSecEdLength = false; // default.
$secEdLength = (int)$personTable->getColumnLength('SECURITY_EDUCATION');


if(isset($_REQUEST['CNUM'])){
    $activePeople = $personTable->activePersonPredicate();
    $cnumArray = $loader->load('CNUM',allTables::$PERSON, $activePeople . " AND (CNUM='" . db2_escape_string(trim($_REQUEST['CNUM'])) . "') ");
    $cnumValidated = isset($cnumArray[trim($_REQUEST['CNUM'])]);
} else {
    $cnumValidated = false;
}

if(isset($_REQUEST['SECURITY_EDUCATION'])){    
    $validSecEdLength = (strlen(trim($_REQUEST['SECURITY_EDUCATION'])) <= $secEdLength);
    $securityEducationSupplied = true;
} else {
    $securityEducationSupplied = false;
}

if( !$securityEducationSupplied or !$validSecEdLength or  !$cnumValidated ){
    ob_clean();
    $response = array();
    $response['success'] = 'false';
    $response['messages'] = 'Invalid Parameters provided. Details follow:';
    if(!$securityEducationSupplied){
        $response['messages'].= !isset($_REQUEST['SECURITY_EDUCATION']) ? " Security Education value not supplied" : null;
     }
     if(!$validSecEdLength){
        $response['messages'].= !$validSecEdLength ? " Security Education value supplied is >= to $secEdLength bytes long": null;
    }
    if(!$cnumValidated){
        $response['messages'].= !isset($_REQUEST['CNUM']) ? " No CNUM parameter passed" : null;
        if(isset($_REQUEST['CNUM'])){
            $response['messages'].= !isset($cnumArray[$_REQUEST['CNUM']]) ? " CNUM not found as an 'active' person in the PERSON table " : null;
        }
    }
    
    $response['parameters'] = print_r($_REQUEST,true);
    error_log('Invalid Parameters provided :' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(422);
    echo json_encode($response);
    return;
}

// Save here.

$sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON ;
$sql.= " SET ";
$sql.= " SECURITY_EDUCATION='" . db2_escape_string(trim($_REQUEST['SECURITY_EDUCATION'])) . "' " ;
$sql.= " WHERE CNUM='" . db2_escape_string($_REQUEST['CNUM']) . "' ";

$rs = db2_exec($GLOBALS['conn'], $sql);

if(!$rs){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
    DbTable::displayErrorMessage($rs, '', '', $sql);
}

$messages = ob_get_clean();
$success = empty($messages);
$response['success'] = $success;
$response['messages'] = $messages;

if(!$success){
    ob_clean();
    error_log('SaveSecurityEducationError:' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);