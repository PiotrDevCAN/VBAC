<?php

use itdq\Process;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($rootScriptName)){
    $rootScriptName = '';
}
$scriptsDirectory = '/var/www/html/batchJobs/';
$rootScriptName = str_replace('/var/www/html/batchJobs/', '', $rootScriptName);
$processDirectory = 'processesCLI/'; 
$processFile = 'updatePesFieldsProcess.php';
switch($rootScriptName) {
    case 'updatePesFieldsFromUpes.php':
        throw new \Exception('This script has been permanently disabled.');
        break;
    case 'updatePesFieldsFromUpesKyndryl.php':
        throw new \Exception('This script has been permanently disabled.');
        /*
        try {
            $cmd = 'php ';
            $cmd .= '-d auto_prepend_file=' . $scriptsDirectory . 'php/siteheader.php ';
            $cmd .= '-d auto_append_file=' . $scriptsDirectory . 'php/sitefooter.php ';
            $cmd .= '-f ' . $scriptsDirectory . $processDirectory. $processFile . ' ' . $url;
            $process = new Process($cmd);
            $pid = $process->getPid();
            echo "PES Fields Script has succeed to be executed: ".$rootScriptName;
        } catch (Exception $exception) {
            echo $exception->getMessage();
            echo "PES Fields Script has failed to be executed: ".$rootScriptName;
        }
        */
        break;
    default:
        throw new \Exception('Incorrect way of execution of script.');
        break;
}