<?php

use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ob_start();


$GLOBALS['ltcuser']['mail'] = $_SESSION['ssoEmail'];

ini_set('display_errors',1);
ini_set('display_startup_errors',1);

$success = false;

try {
    $person = new personRecord();
    $table = new personTable(allTables::$PERSON);
    $person->setFromArray(array('CNUM'=>$_POST['cnum']));
    $personData = $table->getRecord($person);
    $person->setFromArray($personData);
    ob_start();
    $person->editPersonModalBody();
    $body = ob_get_clean();
    $success = true;
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
}

$messages = ob_get_clean();
$response = array('body'=>$body,'success'=>$success,'messages'=>$messages,'data'=>print_r($personData,true),'accountOrganisation'=>$personData['TT_BAU']);
ob_clean();
echo json_encode($response);