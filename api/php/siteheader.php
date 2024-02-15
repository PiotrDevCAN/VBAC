<?php

use itdq\Connection;
use itdq\Mailer;
use itdq\Redis;
use itdq\WorkerAPI;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '3072M');
ini_set('max_execution_time', 360);

error_reporting(E_ALL);

date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR . "../itdq/" . PATH_SEPARATOR .  "../vbac/" );

// if($_REQUEST['token']!= $_ENV['api_token']){
//     // no token no response
//     ob_clean();
//     http_response_code(400);
//     echo json_encode(array('success'=>false,'data'=>null));    
//     exit();
// }

include ('vendor/autoload.php');
include ('splClassLoader.php');

require_once("../php/errorHandlers.php");

// trigger_error handler
set_error_handler('myErrorHandler');

// Exception handler
set_exception_handler('myExceptionHandler');

// Fatal Shutdown handler
register_shutdown_function('fatalErrorShutdownHandler');

$sessionConfig = (new \ByJG\Session\SessionConfig($_SERVER['SERVER_NAME']))
->withSecret($_ENV['jwt_token']);

$handler = new JwtSecureSession($sessionConfig);
session_set_save_handler($handler, true);

session_start();
error_log(__FILE__ . "session:" . session_id());

$token = $_ENV['api_token'];

$_SESSION['ssoEmail'] = empty($_SESSION['ssoEmail']) ? 'API Invocation' : $_SESSION['ssoEmail'];
$dbClient = new Connection();
$redisClient = new Redis();
$mailerClient = new Mailer();
// $workerAPIClient = new WorkerAPI();