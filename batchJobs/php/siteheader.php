<?php

//use ByJG\Session\JwtSession;

use itdq\ByJgJwtSecureSession;
use itdq\Connection;
use itdq\Mailer;
use itdq\Redis;
use itdq\WorkerAPI;

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

/*
* ByJG session
*/
// $handler = new ByJgJwtSecureSession();

require_once("php/errorHandlers.php");

// trigger_error handler
set_error_handler('myErrorHandler');

// Exception handler
set_exception_handler('myExceptionHandler');

// Fatal Shutdown handler
register_shutdown_function('fatalErrorShutdownHandler');

date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);
// session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include "php/w3config.php";
$dbClient = new Connection();
$mailerClient = new Mailer();
$redisClient = new Redis();
// $workerAPIClient = new WorkerAPI();

$_SESSION['ssoEmail'] = 'Scheduled Job';