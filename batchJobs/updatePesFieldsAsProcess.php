<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!isset($rootScriptName)){
    $rootScriptName = '';
}

$rootScriptName = str_replace('/var/www/html/batchJobs/', '', $rootScriptName);
switch($rootScriptName) {
    case 'updatePesFieldsFromUpes.php':
    case 'updatePesFieldsFromUpesKyndryl.php':
        throw new \Exception('This script has been permanently disabled.');
        break;
    default:
        throw new \Exception('Incorrect way of execution of script.');
        break;
}