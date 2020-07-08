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

// validate parameters.

$rsaTokenSupplied   = isset($_REQUEST['RSA_TOKEN']);
$callSignIdSupplied = isset($_REQUEST['CALLSIGN_ID']);
$cnumSupplied       = isset($_REQUEST['CNUM']);

if( (!$rsaTokenSupplied and !$callSignIdSupplied) or !$cnumSupplied ){
    ob_clean();
    $response = array();
    $response['success'] = 'false';
    $response['messages'] = 'Parameters provided were not valid. Details follow:';

    if(!$cnumSupplied and !$rsaTokenSupplied){
        $response['messages'].= " Parameters don't indicate which token to clear";
    }

    if(!$cnumSupplied){
        $response['messages'].= " CNUM not provided" ;
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
$sql.= isset($_REQUEST['RSA_TOKEN']) ? " RSA_TOKEN = null " : null;
$sql.= isset($_REQUEST['RSA_TOKEN']) && isset($_REQUEST['CALLSIGN_ID']) ? " , " : null;
$sql.= isset($_REQUEST['CALLSIGN_ID']) ? " CALLSIGN_ID = null " : null;
$sql.= " WHERE CNUM='" . db2_escape_string($_REQUEST['CNUM']) . "' ";

$rs = db2_exec($_SESSION['conn'], $sql);

if(!$rs){
    echo db2_stmt_error();
    echo db2_stmt_errormsg();
    DbTable::displayErrorMessage($rs, '', '', $sql);
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response['success'] = $success;
$response['messages'] = $messages;

if(!$success){
    ob_clean();
    error_log('SaveTokenDetailsError:' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);