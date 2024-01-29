<?php

use itdq\Connection;
use itdq\WorkerAPI;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '3072M');
ini_set('max_execution_time', 360);

error_reporting(E_ALL);
date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR . "../itdq/" . PATH_SEPARATOR .  "../vbac/" );

include "vendor/autoload.php";
include "splClassLoader.php";

session_start();
$token = $_ENV['api_token'];

$_SESSION['ssoEmail'] = empty($_SESSION['ssoEmail']) ? 'API Invocation' : $_SESSION['ssoEmail'];
$dbClient = new Connection();

$workerAPIClient = new WorkerAPI();