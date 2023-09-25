<?php
use itdq\JwtSecureSession;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '512M');
ini_set('max_execution_time', 360);

error_reporting(E_ALL);

date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR . "../itdq/" . PATH_SEPARATOR .  "../vbac/" );

if($_REQUEST['token']!= $_ENV['api_token']){
    // no token no response
    ob_clean();
    http_response_code(400);
    echo json_encode(array('success'=>false,'data'=>null));    
    exit();
}

include ('vendor/autoload.php');
include ('splClassLoader.php');

require_once("../php/errorHandlers.php");

set_error_handler('myErrorHandler');
register_shutdown_function('fatalErrorShutdownHandler');

$sessionConfig = (new \ByJG\Session\SessionConfig($_SERVER['SERVER_NAME']))
->withSecret($_ENV['jwt_token']);

$handler = new JwtSecureSession($sessionConfig);
session_set_save_handler($handler, true);

session_start();
error_log(__FILE__ . "session:" . session_id());

$token = $_ENV['api_token'];

$GLOBALS['Db2Schema'] = strtoupper($_ENV['environment']);
$GLOBALS['Db2Schema'] = str_replace('_LOCAL', '', $GLOBALS['Db2Schema']);

$_SESSION['ssoEmail'] = empty($_SESSION['ssoEmail']) ? 'API Invocation' : $_SESSION['ssoEmail'];
include "connect.php";
