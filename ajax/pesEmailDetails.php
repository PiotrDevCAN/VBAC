<?php

use vbac\pesEmail;


ob_start();
$pesEmailObj = new pesEmail();


try { 
    $emailDetails = $pesEmailObj->getEmailDetails($_GET['emailaddress'], $_GET['country']);
} catch ( \Exception $e) {
    switch ($e->getCode()) {
        case 803:
            $emailDetails['warning']['filename'] = 'No email exists for combination of Internal/External and Country';
            echo "Warning";
        break;
        default:
            var_dump($e);
        break;
    }
}

$messages = ob_get_clean();
$success = strlen($messages)==0;

unset($emailDetails['attachments']); // dont need them at this point.
$emailDetails['success'] = $success;
$emailDetails['messages'] = $messages;
$emailDetails['cnum'] = $_GET['cnum'];


ob_clean();
echo json_encode($emailDetails);