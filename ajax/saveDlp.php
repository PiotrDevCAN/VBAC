<?php
use itdq\AuditTable;
use vbac\dlpTable;
use vbac\allTables;
use itdq\Loader;


ob_start();
$parms = array_map('trim', $_POST);
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($parms,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$actionsTaken = null;
$loader = new Loader();
$approvingMgr = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON, " CNUM='" . db2_escape_string($parms['approvingManager']) . "' " );
$approverEmail = $approvingMgr[$parms['approvingManager']];
$licencee = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON," CNUM='" . db2_escape_string($parms['licencee']) . "' " );
$licenceeNotes = trim($licencee[$parms['licencee']]);

$dlpTable = new dlpTable(allTables::$DLP);

$licencedAlready = $dlpTable->licencedAlready($parms['licencee'], $parms['currentHostname']);

var_dump($licencedAlready);

if($licencedAlready){
    $actionsTaken.= "<br/>" . $licenceeNotes . " already holds a licence for Hostname:" .  $parms['currentHostname'];
    
    $save = $dlpTable->recordTransfer($parms['licencee'],$parms['currentHostname'], $parms['hostname']);
    $actionsTaken.= $save ? "<p class='bg-success'>Licence transfered from Hostname:" .  $parms['currentHostname'] . "To:" . $parms['hostname'] . "</p>" : "<p class='bg-danger'>Attempt to record licence transfer has failed. See error message below<p>";
}
    
$save = $dlpTable->recordLicence($parms['licencee'], $parms['hostname'],$approverEmail );
$actionsTaken.= $save ? "<p class='bg-success'>New Licence recorded for $licenceeNotes on Hostname:" .  $parms['hostname'] . "</p>" : "<p class='bg-danger'>Attempt to save new Licence record has failed. See error message below</p>";
 
$messages = ob_get_clean();
$success = (trim($messages) == "");
$response = array('success'=>$success,'actionsTaken'=>$actionsTaken,'messages'=>$messages,"post"=>print_r($parms,true));
ob_clean();
echo json_encode($response);