<?php

use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ob_start();

$_SESSION['ssoEmail'] = $_SESSION['ssoEmail'];

ini_set('display_errors',1);
ini_set('display_startup_errors',1);

$success = false;

try {
    $person = new personRecord();
    $table = new personTable(allTables::$PERSON);
    $person->setFromArray(array('CNUM'=>$_POST['cnum'], 'WORKER_ID'=>$_POST['workerid']));
    $personData = $table->getRecord($person);
    $person->setFromArray($personData);
    ob_start();
    $person->editPersonModalBody();
    $body = ob_get_clean();
    ob_start();
    $success = true;
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
}
$ttBau = '';
if (isset($personData['TT_BAU'])) {
    $ttBau = $personData['TT_BAU'];
}

$ctbRtb = '';
if (isset($personData['CTB_RTB'])) {
    $ctbRtb = $personData['CTB_RTB'];    
}

$messages = ob_get_clean();
ob_start();
$response = array(
    'body'=>$body,
    'success'=>$success,
    'messages'=>$messages,
    'data'=>print_r($personData,true),
    'accountOrganisation'=>$ttBau,
    'ctbRtb'=>$ctbRtb
);
ob_clean();
echo json_encode($response);