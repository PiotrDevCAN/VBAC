<?php
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;


ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

ini_set('display_errors',1);
ini_set('display_startup_errors',1);

try {
    $table = new personTable(allTables::$PERSON);
    $response = $table->setFmFlag($_POST['cnum'],$_POST['flag']);
    if($response){
        $body = "<p>Functional Mgr Flag set to : " . $_POST['flag'] . " for :" . $_POST['notesid'] . "</p>";
    }
} catch (Exception $e) {
    echo $e->getCode();
    echo $e->getMessage();
}
$messages = ob_get_clean();
ob_start();
$response = array('body'=>$body,'messages'=>$messages);
ob_clean();
echo json_encode($response);