<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function myErrorHandler($code, $message, $file, $line) {
    $mailError = new PHPMailer();
    // We're in DEV mode for emails - override the recipients.
    // But if we're in "batch" mode, the ssoEmail doesn't contain a valid email address, so send it to devemailid or me.
    if (isSet($_SESSION['ssoEmail']) && filter_var($_SESSION['ssoEmail'], FILTER_VALIDATE_EMAIL)) {
        $localEmail = $_SESSION['ssoEmail'];
    } else {
        $localEmail = ! empty($_ENV['devemailid']) ? $_ENV['devemailid'] : 'piotr.tajanowicz@kyndryl.com';
    }

    $recipient = $_ENV['email'] == 'user' ? $localEmail : $_ENV['devemailid'];
    $mailError->clearAllRecipients();
    $mailError->addAddress($recipient);
    $mailError->clearCCs();
    $mailError->clearBCCs();

    // $mailError->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output ; SMTP::DEBUG_OFF
    // $mailError->isSMTP(); // Send using SMTP
    // $mailError->Host = 'na.relay.ibm.com'; // Set the SMTP server to send through
    // $mailError->SMTPAuth = false;
    // $mailError->SMTPAutoTLS = false;
    // $mailError->Port = 25;
    // $replyto = $_ENV['noreplyemailid'];

    $mailError->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output ; SMTP::DEBUG_OFF
    $mailError->isSMTP(); // Send using SMTP
    $mailError->Host = $_ENV['smtp-server']; // Set the SMTP server to send through
    $mailError->SMTPAuth = true;
    $mailError->SMTPAutoTLS = true;
    $mailError->SMTPSecure = 'ssl';
    $mailError->Port = 465; // 25, 465, or 587
    $mailError->Username = $_ENV['smtp-user-name'];             
    $mailError->Password = $_ENV['smtp-user-pw']; 

    $replyto = $_ENV['noreplyemailid'];
    $mailError->setFrom($replyto);
    $mailError->isHTML(true);
    // $mailError->Subject = "**" . $_ENV['environment'] . "**" . 'Error has occurred while running PHP script';

    switch($code) {
        case E_ERROR:
            // fatal error
            $subject = "**" . $_ENV['environment'] . "**" . 'Error has occurred while running PHP script';
            break;
        case E_USER_ERROR:
            $subject = "**" . $_ENV['environment'] . "**" . 'User Error has occurred while running PHP script';
            break;
        case E_USER_NOTICE:
            $subject = "**" . $_ENV['environment'] . "**" . 'User Notice has occurred while running PHP script';
            break;
        case E_USER_WARNING:
            $subject = "**" . $_ENV['environment'] . "**" . 'User Warning has occurred while running PHP script';
            break;
        case E_USER_DEPRECATED:
            $subject = "**" . $_ENV['environment'] . "**" . 'User Deprecated has occurred while running PHP script';
            break;
        default:
            $subject = "**" . $_ENV['environment'] . "**" . 'Default event has occurred while running PHP script';
            break;
    }
    $mailError->Subject = $subject;

    $response = array(
        'code' => $code, 
        'message' => $message, 
        'file' => $file, 
        'line' => $line
    );
    $mailError->Body = serialize($response);
    if (!$mailError->send()) {
            
    }
}

function fatalErrorShutdownHandler() {
    $last_error = error_get_last();
    if (!is_null($last_error)) {
        switch($last_error['type']) {
            case E_ERROR:
                // fatal error
                myErrorHandler(E_ERROR, $last_error['message'].' FROM fatalErrorShutdownHandler', $last_error['file'], $last_error['line']);
                break;
            default:
                break;
        }  
    }
}