<?php
use vbac\allTables;
use itdq\AuditTable;
use vbac\AgileSquadRecord;
use vbac\AgileSquadTable;
use itdq\FormClass;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$agileSquadRecord = new AgileSquadRecord();
$agileSquadRecord->setFromArray($_POST);

$table = $_POST['version']=='Original' ? allTables::$AGILE_SQUAD : allTables::$AGILE_SQUAD_OLD;

$agileSquadRecordTable = new AgileSquadTable($table);

$saveResult = $_POST['mode']==FormClass::$modeDEFINE ? $agileSquadRecordTable->insert($agileSquadRecord) : $agileSquadRecordTable->update($agileSquadRecord);

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('result'=>$saveResult,'success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);
