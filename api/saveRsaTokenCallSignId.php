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

$rsaTokenSupplied=true; // If they've supplied RSA Token we will validate and set this BOOL as appropriate. This covers us if it's NOT supplied.
$callSignIdSupplied = true;
$cnumValidated = true;

if(isset($_REQUEST['CNUM'])){
    $activePeople = $personTable->activePersonPredicate();
    $cnumArray = $loader->load('CNUM',allTables::$PERSON, $activePeople . " AND (CNUM='" . htmlspecialchars(trim($_REQUEST['CNUM'])) . "') ");
    $cnumValidated = isset($cnumArray[trim($_REQUEST['CNUM'])]);
} else {
    $cnumValidated = false;
}

if(isset($_REQUEST['RSA_TOKEN'])){
    $rsaTokenLength = (int)$personTable->getColumnLength('RSA_TOKEN');
    $validRsaTokenLength = (strlen(trim($_REQUEST['RSA_TOKEN']))==$rsaTokenLength);

    $allRsaToken = $loader->load('RSA_TOKEN',allTables::$PERSON);
    $duplicateRsaToken = isset($allRsaToken[trim($_REQUEST['RSA_TOKEN'])]);

    $rsaTokenSupplied = ($validRsaTokenLength && !$duplicateRsaToken);
}

if(isset($_REQUEST['CALLSIGN_ID'])){
    $callSignIdLength = (int)$personTable->getColumnLength('CALLSIGN_ID');
    $validCallSignIdLength = (strlen(trim($_REQUEST['CALLSIGN_ID']))<=$callSignIdLength);

    $allCallSIgnId = $loader->load('CALLSIGN_ID',allTables::$PERSON);
    $duplicateCallSignId = isset($allCallSIgnId[trim($_REQUEST['CALLSIGN_ID'])]);

    $callSignIdSupplied = ($validCallSignIdLength && !$duplicateCallSignId);
}


if( !$rsaTokenSupplied or !$callSignIdSupplied or !$cnumValidated ){
    ob_clean();
    $response = array();
    $response['success'] = 'false';
    $response['messages'] = 'Invalid Parameters provided. Details follow:';
    if(!$rsaTokenSupplied){
        $response['messages'].= $duplicateRsaToken ? " RSA Token is already allocated" : null;
        $response['messages'].= !$validRsaTokenLength ? " RSA Token supplied is not $rsaTokenLength bytes long": null;
    }
    if(!$callSignIdSupplied){
        $response['messages'].= $duplicateCallSignId ? " Call Sign ID is already allocated" : null;
        $response['messages'].= !$validCallSignIdLength ? " Call Sign ID supplied is not $callSignIdLength bytes long": null;
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
$sql.= isset($_REQUEST['RSA_TOKEN']) ? " RSA_TOKEN='" . htmlspecialchars(trim($_REQUEST['RSA_TOKEN'])) . "' " : null;
$sql.= isset($_REQUEST['RSA_TOKEN']) && isset($_REQUEST['CALLSIGN_ID']) ? " , " : null;
$sql.= isset($_REQUEST['CALLSIGN_ID']) ? " CALLSIGN_ID='" .  htmlspecialchars(trim($_REQUEST['CALLSIGN_ID'])) . "' " : null;
$sql.= " WHERE CNUM='" . htmlspecialchars($_REQUEST['CNUM']) . "' ";

$rs = sqlsrv_query($GLOBALS['conn'], $sql);

if(!$rs){
    echo json_encode(sqlsrv_errors());
    DbTable::displayErrorMessage($rs, '', '', $sql);
}

$messages = ob_get_clean();
$success = empty($messages);
$response['success'] = $success;
$response['messages'] = $messages;

if(!$success){
    ob_clean();
    error_log('SaveTokenDetailsError:' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);