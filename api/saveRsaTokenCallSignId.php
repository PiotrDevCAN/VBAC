<?php

use vbac\personTable;
use vbac\allTables;
use itdq\Loader;
use itdq\DbTable;
use itdq\AuditTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_REQUEST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

if($_REQUEST['token']!= $token){
    return;
}






// validate parameters will fit into the columns, without hard coding the column length here.

$personTable = new personTable(allTables::$PERSON);
$loader = new Loader();

$rsaTokenValidated=true; // If they've supplied RSA Token we will validate and set this BOOL as appropriate. This covers us if it's NOT supplied.
$callSignIdValidated = true;
$cnumValidated = true;

if(isset($_POST['CNUM'])){
    $activePeople = $personTable->activePersonPredicate();
    $cnumArray = $loader->load('CNUM',allTables::$PERSON, $activePeople . " AND (CNUM='" . db2_escape_string(trim($_POST['CNUM'])) . "') ");    
    $cnumValidated = isset($cnumArray[trim($_POST['CNUM'])]); 
} else {
    $cnumValidated = false;
}

if(isset($_POST['RSA_TOKEN'])){
    $rsaTokenLength = (int)$personTable->getColumnLength('RSA_TOKEN');
    $validRsaTokenLength = (strlen(trim($_POST['RSA_TOKEN']))==$rsaTokenLength);
    
    $allRsaToken = $loader->load('RSA_TOKEN',allTables::$PERSON);
    $duplicateRsaToken = isset($allRsaToken[trim($_POST['RSA_TOKEN'])]);

    $rsaTokenValidated = ($validRsaTokenLength && !$duplicateRsaToken);   
}

if(isset($_POST['CALLSIGN_ID'])){
    $callSignIdLength = (int)$personTable->getColumnLength('CALLSIGN_ID');
    $validCallSignIdLength = (strlen(trim($_POST['CALLSIGN_ID']))==$callSignIdLength);
    
    $allCallSIgnId = $loader->load('CALLSIGN_ID',allTables::$PERSON);
    $duplicateCallSignId = isset($allCallSIgnId[trim($_POST['CALLSIGN_ID'])]);
    
    $callSignIdValidated = ($validCallSignIdLength && !$duplicateCallSignId);
}


if( !$rsaTokenValidated or !$callSignIdValidated or !$cnumValidated ){
    ob_clean();
    $response = array();
    $response['success'] = 'false';
    $response['messages'] = 'Invalid Parameters provided. Details follow:';    
    if(!$rsaTokenValidated){
        $response['messages'].= $duplicateRsaToken ? " RSA Token is already allocated" : null;
        $response['messages'].= !$validRsaTokenLength ? " RSA Token supplied is not $rsaTokenLength bytes long": null;
    } 
    if(!$callSignIdValidated){
        $response['messages'].= $duplicateCallSignId ? " Call Sign ID is already allocated" : null;
        $response['messages'].= !$validCallSignIdLength ? " Call Sign ID supplied is not $callSignIdLength bytes long": null;
    } 
    if(!$cnumValidated){
        $response['messages'].= !isset($_POST['CNUM']) ? " No CNUM parameter passed" : null;
        if(isset($_POST['CNUM'])){
            $response['messages'].= !isset($cnumArray[$_POST['CNUM']]) ? " CNUM not found as an 'active' person in the PERSON table " : null;
        }
    }
    
    $response['parameters'] = print_r($_POST,true);
    error_log('Invalid Parameters provided :' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(422);
    echo json_encode($response);
    return;
}

// Save here.

$sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON ;    
$sql.= " SET ";
$sql.= isset($_POST['RSA_TOKEN']) ? " RSA_TOKEN='" . db2_escape_string(trim($_POST['RSA_TOKEN'])) . "' " : null;
$sql.= isset($_POST['RSA_TOKEN']) && isset($_POST['CALLSIGN_ID']) ? " , " : null;
$sql.= isset($_POST['CALLSIGN_ID']) ? " CALLSIGN_ID='" .  db2_escape_string(trim($_POST['CALLSIGN_ID'])) . "' " : null;
$sql.= " WHERE CNUM='" . db2_escape_string($_POST['CNUM']) . "' ";
  
$rs = db2_exec($_SESSION['conn'], $sql);
    
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
    error_log('SaveTokenDetailsError:' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(404);
}

echo json_encode($response , JSON_NUMERIC_CHECK);