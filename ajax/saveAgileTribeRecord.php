<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\AuditRecord;
use vbac\PersonSubPlatformTable;
use vbac\AgileTribeRecord;
use vbac\AgileTribeTable;
use itdq\FormClass;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$agileSquadRecord = new AgileTribeRecord();
$agileSquadRecord->setFromArray($_POST);

$agileTribeTable = new AgileTribeTable(allTables::$AGILE_TRIBE);

$saveResult = $_POST['mode']==FormClass::$modeDEFINE ? $agileTribeTable->insert($agileSquadRecord) : $agileTribeTable->update($agileSquadRecord);

$messages = ob_get_clean();
$success = empty($messages);
$response = array('result'=>$saveResult,'success'=>$success,'messages'=>$messages);
ob_clean();
echo json_encode($response);
