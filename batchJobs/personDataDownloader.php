<?php

use itdq\Process;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($type)){
    $type = 'unknown';
}

$email = $_SESSION['ssoEmail'];
$scriptsDirectory = '/var/www/html/batchJobs/';
$processDirectory = 'processesCLI/'; 
$processFile = 'downloadPersonDetailsProcess.php';
try {
    $cmd = 'php ';
    $cmd .= '-d auto_prepend_file=' . $scriptsDirectory . 'php/siteheader.php ';
    $cmd .= '-d auto_append_file=' . $scriptsDirectory . 'php/sitefooter.php ';
    $cmd .= '-f ' . $scriptsDirectory . $processDirectory. $processFile . ' ' . $email . ' ' . str_replace(" ", "_", $type);
    $process = new Process($cmd);
    $pid = $process->getPid();
    echo "Aurora Person Table Extract Script has succeed to be executed: ".$email;
    error_log("Aurora Person Table Extract Script has succeed to be executed: ".$email);
} catch (Exception $exception) {
    echo $exception->getMessage();
    echo "Aurora Person Table Extract Script has failed to be executed: ".$email;
    error_log("Aurora Person Table Extract Script has failed to be executed: ".$email);
}
