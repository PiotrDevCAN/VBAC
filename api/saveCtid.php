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

$ctidTokenSupplied = false; // If they've supplied CT ID we will validate and set this BOOL as appropriate. This covers us if it's NOT supplied.
$cnumValidated = false;
$duplicateCtid = false;
$validCtidLength = false;
$ctidLength = 0;

if(isset($_REQUEST['CNUM'])){
    $activePeople = $personTable->activePersonPredicate();
    $cnumArray = $loader->load('CNUM',allTables::$PERSON, $activePeople . " AND (CNUM='" . db2_escape_string(trim($_REQUEST['CNUM'])) . "') ");
    $cnumValidated = isset($cnumArray[trim($_REQUEST['CNUM'])]);
} else {
    $cnumValidated = false;
}

$ctidLength = (int)$personTable->getColumnLength('CT_ID');
if(isset($_REQUEST['CT_ID'])){  
    $ctidTokenSupplied = true;
    $validCtidLength = !empty($_REQUEST['CT_ID']) ? (strlen(trim($_REQUEST['CT_ID']))<=$ctidLength) : false;    
    $allCtidToken = $loader->load('CT_ID',allTables::$PERSON);
    $duplicateCtid = isset($allCtidToken[trim($_REQUEST['CT_ID'])]);
    
    $ctidValidated = ($validCtidLength && !$duplicateCtid);
}

if( !$ctidValidated or !$cnumValidated ){
    ob_clean();
    $response = array();
    $response['success'] = 'false';
    $response['messages'] = 'Invalid Parameters provided. Details follow:';
    
    if(!$ctidTokenSupplied){
        $response['messages'].= " CT_ID not supplied";
    }    
    if($duplicateCtid){
        $response['messages'].=" CT ID is already allocated";
    }
    if($ctidTokenSupplied && !$validCtidLength){
        $response['messages'].=" CT ID supplied is the wrong length";
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
$sql.= isset($_REQUEST['CT_ID']) ? " CT_ID='" . db2_escape_string(trim($_REQUEST['CT_ID'])) . "' " : 'invalid sql no ctid' ;
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
    error_log('SaveCtIdError:' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);