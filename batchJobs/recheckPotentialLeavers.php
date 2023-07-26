<?php

use itdq\Process;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $GLOBALS['Db2Schema'] = 'VBAC';

$scriptsDirectory = '/var/www/html/batchJobs/';
$processDirectory = 'processesCLI/'; 
$processFile = 'recheckPotentialLeaversProcess.php';
try {
    $cmd = 'php ';
    $cmd .= '-d auto_prepend_file=' . $scriptsDirectory . 'php/siteheader.php ';
    $cmd .= '-d auto_append_file=' . $scriptsDirectory . 'php/sitefooter.php ';
    $cmd .= '-f ' . $scriptsDirectory . $processDirectory. $processFile;
    $process = new Process($cmd);
    $pid = $process->getPid();
    echo "Recheck Potential Leavers Script has succeed to be executed: " . $scriptsDirectory . $processDirectory . $processFile;
} catch (Exception $exception) {
    echo $exception->getMessage();
    echo "Recheck Potential Leavers Script has failed to be executed: " . $scriptsDirectory . $processDirectory . $processFile;
}