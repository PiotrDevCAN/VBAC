<?php

use vbac\pesEmail;
ob_start();
$pesEmailObj = new pesEmail();
$emailResponse = $pesEmailObj->sendPesEmail($_POST['firstname'],$_POST['lastname'],$_POST['emailaddress'], $_POST['country']);

$messages = ob_get_clean();
$success = strlen($messages)==0; 

$response['success'] = $success;
$response['messages'] = $messages;
$response['emailResponse'] = $emailResponse;

ob_clean();
echo json_encode($response);