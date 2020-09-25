<?php

use vbac\pesEmail;


ob_start();
$pesEmailObj = new pesEmail();

try {
    $recheck = $_GET['recheck']=='true';
    $emailDetails = $pesEmailObj->getEmailDetails($_GET['emailaddress'],$_GET['country'],null,$recheck);
} catch ( \Exception $e) {
    switch ($e->getCode()) {
        case 800:
        case 801:
            $emailDetails['warning']['filename'] = $e->getMessage();
            echo "Warning";
             break;
        case 803:
            $emailDetails['warning']['filename'] = 'No email exists for combination of Internal/External and Country';
            echo "Warning";
            break;
        case 804:
            $emailDetails['warning']['filename'] = 'No email exists for combination of Internal/External and Country';
            echo "Warning";
            break;
        default:
            var_dump($e);
        break;
    }
}

$messages = ob_get_clean();
ob_start();
$success = strlen($messages)==0;

unset($emailDetails['attachments']); // dont need them at this point.
$emailDetails['success'] = $success;
$emailDetails['messages'] = $messages;
$emailDetails['cnum'] = $_GET['cnum'];
$emailDetails['recheck'] = $_GET['recheck'];


ob_clean();
echo json_encode($emailDetails);