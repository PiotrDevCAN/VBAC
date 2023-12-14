<?php
use itdq\AuditTable;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;

ob_start();
$success = false;

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$person = new personRecord();
$table = new personTable(allTables::$PERSON);

try {
    $person->setFromArray(array(
        'CNUM'=>$_POST['cnum'],
        'WORKER_ID'=>$_POST['workerid']
    ));
    $personData = $table->getRecord($person);
    $person->setFromArray($personData);
    $person->setFromArray(array(
        'PROPOSED_LEAVING_DATE'=>$_POST['proposedLeavingDate']
    ));
    $person->initiateOffboarding();

    $success = true;

}  catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages) ? $success : false;
$response = array(
    'success'=>$success,
    'messages'=>$messages,
    'cnum'=>$_POST['cnum'],
    'workerId'=>$_POST['workerid'],
    'post'=>print_r($_POST,true),
    'proposedLeavingDate'=>$_POST['proposedLeavingDate'],
    'initiated'=>true,
    'offboarding'=>true
);
ob_clean();
$encoded =  json_encode($response);
if($encoded){
    echo $encoded;
} else {
    echo json_encode(array('succces'=>false,'messages'=>'Failed to encode messages, contact support'));
}
