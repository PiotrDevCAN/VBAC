<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\AuditRecord;
use vbac\AgileSquadRecord;
use vbac\AgileSquadTable;
use itdq\FormClass;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$agileSquadRecord = new AgileSquadRecord();
$agileSquadRecord->setFromArray($_POST);

$agileSquadRecordTable = new AgileSquadTable(allTables::$AGILE_SQUAD);

$saveResult = $_POST['mode']==FormClass::$modeDEFINE ? $agileSquadRecordTable->insert($agileSquadRecord) : $agileSquadRecordTable->update($agileSquadRecord);

$messages = ob_get_clean();
$success = empty($messages);
$response = array('result'=>$saveResult,'success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);
