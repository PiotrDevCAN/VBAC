<?php
use vbac\allTables;
use itdq\AuditTable;
use itdq\FormClass;
use vbac\workLocationRecord;
use vbac\workLocationTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$workLocationRecord = new workLocationRecord();
$workLocationRecord->setFromArray($_POST);

switch($_POST['ONSHORE']) {
    case 'Yes':
        $onShore = '1';
        break;
    default:
        $onShore = '0';
        break;
}

switch($_POST['CBC_IN_PLACE']) {
    case 'Yes':
        $CBCInPlace = $_POST['CBC_IN_PLACE'];
        break;
    default:
        $CBCInPlace = '';
        break;
}

$additionalFields = array();
$additionalFields['ONSHORE'] = $onShore;
$additionalFields['CBC_IN_PLACE'] = $CBCInPlace;
if (!empty($additionalFields)) {
    $workLocationRecord->setFromArray($additionalFields);
}

$table = allTables::$STATIC_LOCATIONS;

$workLocationTable = new workLocationTable($table);

$saveResult = $_POST['mode']==FormClass::$modeDEFINE ? $workLocationTable->insert($workLocationRecord) : $workLocationTable->update($workLocationRecord);

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('result'=>$saveResult,'success'=>$success,'messages'=>$messages,'parms'=>print_r($_POST,true),'parms_2'=>$additionalFields);
ob_clean();
echo json_encode($response);
