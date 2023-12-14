<?php

use vbac\personRecord;
use vbac\allTables;
use vbac\personTable;
use itdq\AuditTable;

ob_start();
$success = false;

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

AuditTable::audit("Reversed Offboarded for " . $_POST['cnum'] . " / " . $_POST['workerid'] . "by " . $_SESSION['ssoEmail']);

$person = new personRecord();
$table = new personTable(allTables::$PERSON);

try {
    if(!empty($_POST['cnum']) && !empty($_POST['workerid'])){
        $table->deOffboarded($_POST['cnum'], $_POST['workerid']);
    } else {
        echo "No cnum / worker id provided";
    }

    $success = true;

}  catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
    $success = false;
    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array(
    'success'=>$success,
    'messages'=>$messages,
    'cnum'=>$_POST['cnum'],
    'workerId'=>$_POST['workerid'],
    'post'=>print_r($_POST,true),
    'deoffboarded'=>true
);
ob_clean();
$encoded =  json_encode($response);
if($encoded){
    echo $encoded;
} else {
    echo json_encode(array('succces'=>false,'messages'=>'Failed to encode messages, contact support'));
}