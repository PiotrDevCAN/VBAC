<?phpuse itdq\AuditTable;
use vbac\personRecord;
use vbac\personTable;
use vbac\allTables;use itdq\AuditTable;

ob_start();$success = false;

AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$person = new personRecord();
$table = new personTable(allTables::$PERSON);

try {
    $person->setFromArray(array('CNUM'=>$_POST['cnum']));
    $personData = $table->getRecord($person);    $person->setFromArray($personData);    $person->initiateOffboarding();

//     $timeToInitiateOffboarding = $person->checkIfTimeToInitiateOffboarding();
//     $timeToInitiateOffboarding ? $person->initiateOffboarding() : null;    $success = true;}  catch (Exception $e) {    echo $e->getCode();    echo $e->getMessage();    $success = false;    AuditTable::audit("Exception" . __FILE__ . " Code:<b>" . $e->getCode() . "</b> Msg:<b>" . $e->getMessage() . "</b>", AuditTable::RECORD_TYPE_DETAILS);}$messages = ob_get_clean();$response = array('success'=>$success,'messsages'=>$messages,'initiated'=>true,'cnum'=>$_POST['CNUM'], 'post'=>print_r($_POST,true),'offboarding'=>true);ob_clean();$encoded =  json_encode($response);if($encoded){    echo $encoded;} else {    echo json_encode(array('succces'=>false,'messages'=>'Failed to encode messages, contact support'));}