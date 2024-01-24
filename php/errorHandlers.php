<?php

function sendErrorNotification($response = array()) {

    $code = $response['error_code'];

    $mailError = $GLOBALS['mailer'];
    // We're in DEV mode for emails - override the recipients.
    // But if we're in "batch" mode, the ssoEmail doesn't contain a valid email address, so send it to devemailid or me.
    if (isSet($_SESSION['ssoEmail']) && filter_var($_SESSION['ssoEmail'], FILTER_VALIDATE_EMAIL)) {
        $localEmail = $_SESSION['ssoEmail'];
    } else {
        $localEmail = ! empty($_ENV['devemailid']) ? $_ENV['devemailid'] : 'Piotr.Tajanowicz@kyndryl.com';
    }

    $recipient = $_ENV['email'] == 'user' ? $localEmail : $_ENV['devemailid'];
    $mailError->clearAllRecipients();
    $mailError->addAddress($recipient);
    $mailError->clearCCs();
    $mailError->clearBCCs();

    $replyto = $_ENV['noreplyemailid'];
    $mailError->setFrom($replyto);
    $mailError->isHTML(true);
    // $mailError->Subject = "**" . $_ENV['environment'] . "**" . 'Error has occurred while running PHP script';

    switch($code) {
        case E_ERROR:
            // fatal error
            $subject = "**" . $_ENV['environment'] . "**" . 'Fatal Error has occurred while running PHP script';
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
    $mailError->Body = serialize($response);
    if (!$mailError->send()) {
    
    } else {

    }
}

function myExceptionHandler(Throwable $e) {
    $output = array(
        'error_code' => $e->getCode(), 
        'error_string' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    );
    sendErrorNotification($output);
}

function myErrorHandler($code = null, $message = null, $file = null, $line = null, $context = null) {
    $output = array(
        'error_code' => $code, 
        'error_string' => $message, 
        'file' => $file, 
        'line' => $line
    );
    sendErrorNotification($output);
}

function fatalErrorShutdownHandler() {
    $last_error = error_get_last();
    if (!is_null($last_error)) {
        // print "Looks like there was an error: " . print_r($last_error, true) . PHP_EOL;
        switch($last_error['type']) {
            case E_ERROR:
                // fatal error
                myErrorHandler(E_ERROR, $last_error['message'].' FROM fatalErrorShutdownHandler', $last_error['file'], $last_error['line']);
                break;
            default:
                break;
        }  
    } else {
        // normal shutdown without an error
        // print "Running a normal shutdown without error." . PHP_EOL;
    }
}