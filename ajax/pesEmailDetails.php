<?php

use vbac\pesEmail;
ob_start();
$pesEmailObj = new pesEmail();
$emailDetails = $pesEmailObj->getEmailDetails($_GET['emailaddress'], $_GET['country']);

$messages = ob_get_clean();
$success = strlen($messages)==0;

unset($emailDetails['attachments']); // dont need them at this point.
$emailDetails['success'] = $success;
$emailDetails['messages'] = $messages;


ob_clean();
echo json_encode($emailDetails);