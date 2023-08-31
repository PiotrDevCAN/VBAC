<?php

use itdq\Process;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$email = $_SESSION['ssoEmail'];
$scriptsDirectory = '/var/www/html/batchJobs/';
$processDirectory = 'processesCLI/'; 
$processFile = 'sendEmployeeData.php';
try {
    $cmd = 'php ';
    $cmd .= '-d auto_prepend_file=' . $scriptsDirectory . 'php/siteheader.php ';
    $cmd .= '-d auto_append_file=' . $scriptsDirectory . 'php/sitefooter.php ';
    $cmd .= '-f ' . $scriptsDirectory . $processDirectory. $processFile;
    $process = new Process($cmd);
    $pid = $process->getPid();
    echo "Employee Data Extract Script has succeed to be executed: ".$email.PHP_EOL;
    echo $cmd;
} catch (Exception $exception) {
    echo $exception->getMessage();
    echo "Employee Data Extract Script has succeed to be executed: ".$email.PHP_EOL;
}