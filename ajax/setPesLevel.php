<?php


use itdq\AuditTable;
use vbac\personTable;
use vbac\allTables;
use vbac\pesTrackerTable;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$personTable = new personTable(allTables::$PERSON);
$pesTracker = new pesTrackerTable(allTables::$PES_TRACKER);

$pesLevel = !empty($_POST['level']) ? trim($_POST['level']) : 'Level 2';

if(!empty($_POST['cnum'])){
    $setSuccess = $personTable->setPesLevel($_POST['cnum'], $pesLevel);
}

if($setSuccess){
    $dummyRow = array('CNUM'=>$_POST['cnum'],'PES_LEVEL'=>$pesLevel);
    $cellContents = pesTrackerTable::getCellContentsForPesLevel($dummyRow);
    $comment = $pesTracker->savePesComment($_POST['cnum'],"PES_LEVEL set to : " . $pesLevel);
}


$messages = ob_get_clean();
$success = empty($messages) && $setSuccess;
$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true),'cell'=>$cellContents,'comment'=>$comment);
ob_clean();
echo json_encode($response);