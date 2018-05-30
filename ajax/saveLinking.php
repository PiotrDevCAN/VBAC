<?phpuse vbac\personRecord;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;

ob_start();

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$person = new personRecord();
$table = new personTable(allTables::$PERSON);

$preBoarder = new personRecord();
$preBoarder->setFromArray(array('CNUM'=>$_POST['person_preboarded']));
$preBoarderData = $table->getFromDb($preBoarder);

$ibmer = new personRecord();
$ibmer->setFromArray(array('CNUM'=>$_POST['ibmer_preboarded']));
$ibmerData = $table->getFromDb($ibmer);$ibmerData['PRE_BOARDED'] = $_POST['person_preboarded'];

$preboarderPesStatus = $preBoarderData['PES_STATUS'];
$preboarderPesStatusD = $preBoarderData['PES_STATUS_DETAILS'];

$ibmerPesStatus = $preBoarderData['PES_STATUS'];
$ibmerPesStatusD = $preBoarderData['PES_STATUS_DETAILS'];
if($ibmerPesStatus == personRecord::PES_STATUS_INITIATED  ){    $ibmerData['PES_STATUS'] = $preboarderPesStatus;    $ibmerData['PES_STATUS_DETAILS'] = $ibmperPesStatusD . ":" . $preboarderPesStatusD;}$ibmer->setFromArray($ibmerData);$table->saveRecord($ibmer);

$preBoarderData['PES_STATUS_DETAILS'] = 'Boarded as ' . $ibmerData['CNUM'] . ":" . $ibmerData['NOTES_ID'] . " Status was:" . $preboarderPesStatus;$preBoarderData['EMAIL_ADDRESS'] = str_replace('ibm.com', '###.com', strtolower($preBoarderData['EMAIL_ADDRESS']));
$preBoarder->setFromArray($preBoarderData);
$table->saveRecord($preBoarder);

$messages = ob_get_clean();
$response = array( 'success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);