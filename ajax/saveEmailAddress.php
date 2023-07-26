<?php
use itdq\AuditTable;
use vbac\personTable;
use vbac\allTables;

ob_start();
AuditTable::audit("Invoked:<b>" . __FILE__ . "</b>Parms:<pre>" . print_r($_POST,true) . "</b>",AuditTable::RECORD_TYPE_DETAILS);

$cnum    = !empty($_POST['eam_cnum']) ? $_POST['eam_cnum'] : null;
$email   = !empty($_POST['eam_email']) ? $_POST['eam_email'] : null;
$field   = !empty($_POST['eam_field']) ? $_POST['eam_field'] : null;

if (!empty($cnum) && !empty($email) && !empty($field) ) {
    switch ($field){
        case 'EMAIL_ADDRESS':
        case 'IBM_EMAIL_ADDRESS':
        case 'KYN_EMAIL_ADDRESS':
            $personTable = new personTable(allTables::$PERSON);
            $personTable->setEmailField($cnum, $field, $email);
            break;
        default:
            echo 'Incorrect field value';
    }
} else {
    echo 'Provide all required parameters';
}

$messages = ob_get_clean();
ob_start();
$success = empty($messages);
$response = array('success'=>$success,'messages'=>$messages,'post'=>print_r($_POST,true));
ob_clean();
echo json_encode($response);