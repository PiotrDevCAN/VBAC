<?php
//use ByJG\Session\JwtSession;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');

set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);
session_start();

include ('vendor/autoload.php');
include ('splClassLoader.php');

// $sessionConfig = (new \ByJG\Session\SessionConfig($_SERVER['SERVER_NAME']))
// ->withSecret($_ENV['jwt_token']);

// $handler = new \ByJG\Session\JwtSession($sessionConfig);
// session_set_save_handler($handler, true);


// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// date_default_timezone_set('UTC');
// set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);
// session_start();

include "../php/w3config.php";
include "connect.php";

$_SESSION['ssoEmail'] = 'Scheduled Job';