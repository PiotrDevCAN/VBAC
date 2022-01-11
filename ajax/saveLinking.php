<?php
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</pre>",AuditTable::RECORD_TYPE_DETAILS);

// $person = new personRecord();
$table = new personTable(allTables::$PERSON);
try {
    $table->linkPreBoarderToIbmer($_POST['person_preboarded'], $_POST['ibmer_preboarded']);
} catch (Exception $e) {
    echo $e->getMessage();
}



// $preBoarder = new personRecord();
// $preBoarder->setFromArray(array('CNUM'=>$_POST['person_preboarded']));
// $preBoarderData = $table->getFromDb($preBoarder);

// $ibmer = new personRecord();
// $ibmer->setFromArray(array('CNUM'=>$_POST['ibmer_preboarded']));
// $ibmerData = $table->getFromDb($ibmer);
// $ibmerData['PRE_BOARDED'] = $_POST['person_preboarded'];

// $preboarderPesStatus = $preBoarderData['PES_STATUS'];
// $preboarderPesStatusD = $preBoarderData['PES_STATUS_DETAILS'];
// $preBoarderPesEvidence = $preBoarderData['PES_DATE_EVIDENCE'];

// $ibmerPesStatus = $ibmerData['PES_STATUS'];
// $ibmerPesStatusD = $ibmerData['PES_STATUS_DETAILS'];
// // $ibmerPesEvidence = $ibmerData['PES_DATE_EVIDENCE'];

// if(trim($ibmerPesStatus) == personRecord::PES_STATUS_INITIATED || trim($ibmerPesStatus) == personRecord::PES_STATUS_REQUESTED ){
//     $ibmerData['PES_STATUS'] = $preboarderPesStatus;
//     $ibmerData['PES_STATUS_DETAILS'] = $ibmerPesStatusD . ":" . $preboarderPesStatusD;
//     $ibmerData['PES_DATE_EVIDENCE'] = $preBoarderPesEvidence;
// }
// $ibmer->setFromArray($ibmerData);
// $table->saveRecord($ibmer);

// $preBoarderData['PES_STATUS_DETAILS'] = personRecord::PES_STATUS_DETAILS_BOARDED_AS . " " . $ibmerData['CNUM'] . ":" . $ibmerData['NOTES_ID'] . " Status was:" . $preboarderPesStatus;
// $preBoarderData['EMAIL_ADDRESS'] = str_replace('ibm.com', '###.com', strtolower($preBoarderData['EMAIL_ADDRESS']));
// $preBoarder->setFromArray($preBoarderData);
// $table->saveRecord($preBoarder);

$messages = ob_get_clean();
ob_start();
$success = empty($messages);

$response = array( 'success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);