<?php
//used to verify and process login

include realpath(dirname(__FILE__))."/../class/include.php";
$auth = new Auth();
$auth->storeParameters($_GET);
if($auth->verifyResponse($_GET))
{
    error_log("get state" . print_r($_GET,true));
    header("Access-Control-Allow-Origin: *");
    header("Location: ".$_GET['state']);
	exit();
}