<?php
//used to verify and process login

include realpath(dirname(__FILE__))."/../class/include.php";
$auth = new Auth();
if($auth->verifyResponse($_GET))
{

    echo '<pre>';
    var_dump($_GET);
    var_dump($_SESSION);
    var_dump($_COOKIE);
    echo '</pre>';
    exit;

    error_log("get state" . print_r($_GET,true));
    header("Access-Control-Allow-Origin: *");
    header("Location: ".$_GET['state']);
	exit();
} else {
    error_log("get state" . print_r($_GET,true));
    echo $_GET['state'];
    // header("Access-Control-Allow-Origin: *");
    // header("Location: ".$_GET['state']);
	exit();
}