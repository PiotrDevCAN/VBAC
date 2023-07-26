<?php

use itdq\AuditTable;
use vbac\allTables;
use vbac\personRecord;
use vbac\personTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personTable = new personTable(allTables::$PERSON);
$status = personRecord::PES_STATUS_PES_PROGRESSING;

if(isset($_REQUEST['cnum']) && !empty($_POST['cnum'])){
    if(is_array($_POST['cnum'])){
        foreach($_POST['cnum'] as $key => $cnum) {
            $personTable->setPesStatus($cnum, $status);
        }
    } else {
        $personTable->setPesStatus($_POST['cnum'], $status);
    }
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);