<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);

include "vendor/autoload.php";
include "splClassLoader.php";
session_start();
$token = 'soEkCfj8zGNDLZ8yXH2YJjpehd8ijzlS';

if($_GET['token']!= $token){
    return;
}

include "connect.php";
