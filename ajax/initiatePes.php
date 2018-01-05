<?php
use vbac\personTable;
use vbac\allTables;
use vbac\personRecord;

ob_start();

ini_set('display_errors',1);
ini_set('display_startup_errors',1);

try {
    $table = new personTable(allTables::$PERSON);
    $personData = $table->getWithPredicate(" CNUM='" . db2_escape_string(trim($_POST['cnum'])) . "' ");
    $person = new personRecord();
    $person->setFromArray($personData);
    $person->sendPesRequest();
    $success = $person->setPesRequested();
    echo $success ? "PES Check initiated" : "Problem Initiating PES check";
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
}

$messages = ob_get_clean();
$response = array('success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);