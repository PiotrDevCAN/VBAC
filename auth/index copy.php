<?php
//used to verify and process login

include realpath(dirname(__FILE__))."/../class/include.php";
$auth = new Auth();
$auth->storeResponse($_GET);
if($auth->verifyResponse($_GET))
{    
    $access_token = $_SESSION['ssoToken']['access_token'];
    $refresh_token = $_SESSION['ssoToken']['refresh_token'];

    // $introspectData = $auth->getIntrospect($access_token);
    // $refreshTokenData = $auth->refreshToken($refresh_token);

    if($auth->getUserInfo($access_token))
    {
        error_log("get state" . print_r($_GET,true));
        header("Access-Control-Allow-Origin: *");
        header("Location: ".$_GET['state']);
        exit();
    }
}