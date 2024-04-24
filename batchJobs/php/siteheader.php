<?php

//use ByJG\Session\JwtSession;

use itdq\ByJgJwtSecureSession;
use itdq\Connection;
use itdq\Mailer;
use itdq\Redis;
use itdq\WorkerAPI;
use itdq\OKTAGroups;
use itdq\OKTAUsers;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ini_set('memory_limit', '3072M');
ini_set('max_execution_time', 360);

date_default_timezone_set('UTC');

set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);
session_start();

include ('vendor/autoload.php');
include ('splClassLoader.php');

include ('includes/startsWith.php');
include ('includes/endsWith.php');

$mailerClient = new Mailer();

require_once("php/errorHandlers.php");

// trigger_error handler
set_error_handler('myErrorHandler');

// Exception handler
set_exception_handler('myExceptionHandler');

// Fatal Shutdown handler
register_shutdown_function('fatalErrorShutdownHandler');

date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);

if (session_status() == PHP_SESSION_NONE) {
    /*
    * ByJG session
    */
    $handler = new ByJgJwtSecureSession();
}

include "php/w3config.php";
$dbClient = new Connection();
$redisClient = new Redis();
// $mailerClient = new Mailer();
// $workerAPIClient = new WorkerAPI();
// $OKTAGroups = new OKTAGroups();
// $OKTAUsers = new OKTAUsers();

$_SESSION['ssoEmail'] = 'Scheduled Job';