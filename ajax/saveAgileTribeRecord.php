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

$agileTribeRecord = new AgileTribeRecord();
$agileTribeRecord->setFromArray($_POST);

$table = $_POST['version']=='Original' ? allTables::$AGILE_TRIBE : allTables::$AGILE_TRIBE_NEW;

$agileTribeTable = new AgileTribeTable($table);

$saveResult = $_POST['mode']==FormClass::$modeDEFINE ? $agileTribeTable->insert($agileTribeRecord) : $agileTribeTable->update($agileTribeRecord);

$messages = ob_get_clean();
$success = empty($messages);
$response = array('result'=>$saveResult,'success'=>$success,'messages'=>$messages,'parms'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);
