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

if( (!$rsaTokenSupplied && !$callSignIdSupplied)){
    ob_clean();
    $response = array();
    $response['success'] = 'false';
    $response['messages'] = 'Parameters provided were not valid. Details follow:';

    if(!$callSignIdSupplied && !$rsaTokenSupplied){
        $response['messages'].= " Please specifiy EITHER RSA_TOKEN or CALLSIGN_ID, never both.";
    }

    $response['parameters'] = print_r($_REQUEST,true);
    error_log('Invalid Parameters provided :' . json_encode($response , JSON_NUMERIC_CHECK));
    http_response_code(422);
    echo json_encode($response);
    return;
}

// Lookup here.

$sql = " SELECT CNUM, NOTES_ID ";
$sql.= $rsaTokenSupplied ? ", RSA_TOKEN " : null;
$sql.= $callSignIdSupplied ? ", CALLSIGN_ID " : null;
$sql.= " FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON ;
$sql.= " WHERE  ";
$sql.= $rsaTokenSupplied ? " RSA_TOKEN = '" . htmlspecialchars($_REQUEST['RSA_TOKEN']) . "'  " : null;
$sql.= $callSignIdSupplied ? " CALLSIGN_ID = '" . htmlspecialchars($_REQUEST['CALLSIGN_ID']) . "'  " : null;
$rs = sqlsrv_query($GLOBALS['conn'], $sql);

if(!$rs){
    echo json_encode(sqlsrv_errors());
    echo json_encode(sqlsrv_errors());
    DbTable::displayErrorMessage($rs, '', '', $sql);
}

$hits = 0;
$notesId = array();
$cnum = array();

while ($row = sqlsrv_fetch_array($rs)){
    $hits++;
    $row = array_map('trim', $row);
    $notesId[] = $row['NOTES_ID'];
    $cnum[] = (string)$row['CNUM'];
}

if($hits==0){
    echo "No Owner found for ";
    echo $rsaTokenSupplied ? " RSA TOKEN:" . $_REQUEST['RSA_TOKEN'] : null;
    echo $callSignIdSupplied ? " CALLSIGN_ID:" . $_REQUEST['CALLSIGN_ID'] : null;
    $response['notesid'] = '';
    $response['cnum']= '';
} else if($hits>1){
    echo "Multiple($hits) Owners found";
    $response['notesid'] = $notesId;
    $response['cnum']= $cnum;
} else {
    $response['notesid'] = $notesId[0];
    $response['cnum']= (string)$cnum[0];
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages) && $hits==1;
$response['success'] = $success;
$response['messages'] = $messages;



ob_clean();
echo json_encode($response);