<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);

include "vendor/autoload.php";
include "splClassLoader.php";
session_start();
$token = $_SERVER['api_token'];
$_SESSION['Db2Schema'] = strtoupper($_SERVER['environment']);
include "connect.php";
personRecord::employeeTypeMappingToDb2();
