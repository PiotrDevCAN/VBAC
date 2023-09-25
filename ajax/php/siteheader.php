<?php
use itdq\JwtSecureSession;

function do_auth($group = null)
{    
    if(stripos($_ENV['environment'], 'local')) {
        $_SESSION['ssoEmail'] = $_ENV['SERVER_ADMIN'];
    } else {
        include_once "SSO/class/include.php";
        $auth = new Auth();
        if(!$auth->ensureAuthorized()){
            die('Invalid logon attempt');
        } else {
            $_SESSION['ssoEmail'] = $_SESSION['ssoEmail'];
            if(isset($_SESSION['somethingChanged']))
            {
                echo "<br/><br/><span style='font-weight:bold;'>Warning: </span> The values that are returned from w3ID/IBMID has probably been changed.<br/><br/>No need to panic, this is very easy to fix.<br/>This is your session currently:<br/><br/><code>";
                var_dump($_SESSION);
                echo "</code><br/><br/>" . "Everything you see there except <span style='font-weight:bold;'>somethingChanged</span> is coming from w3ID/IBMID service.";
                echo '<br/>You now need to look into <span style="font-weight:bold;">private function processOpenIDConnectCallback($data)</span> in <span style="font-weight:bold;">class/auth.class.php</span> and read the comments.';
                echo "<br/>Please keep in mind, that even if sign in is technically working now, you should not use the code in production without strict checking of those values.";
                echo "<br/><br/>";
                echo "What you can do now is:";
                echo "<br/>a) Paste this warning message to <a href='https://github.ibm.com/CWT/auth-openidconnect-w3/issues/' target='_blank'>GitHub Issues</a> and wait for it to be fixed.";
                echo '<br/>b) Very easily adjust the code in private function processOpenIDConnectCallback($data) with new and correct values and <a href="https://github.ibm.com/CWT/auth-openidconnect-w3/issues/" target="_blank">open a new issue</a> or <a href="https://github.ibm.com/CWT/auth-openidconnect-w3/pulls" target="_blank">create a new pull request</a>.';
                echo '<br/><br/>Note: When trying to fix this yourself, do remember to always clear cookies when refreshing the page.';
            }
        }
    }
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

ini_set('memory_limit', '512M');
ini_set('max_execution_time', 360);

error_reporting(E_ALL);

date_default_timezone_set('UTC');
set_include_path("./" . PATH_SEPARATOR . "../" . PATH_SEPARATOR . "../../" . PATH_SEPARATOR . "../../../" . PATH_SEPARATOR);

include ('includes/obHtmlCompress.php');
include ('vendor/autoload.php');
include ('splClassLoader.php');

include ('includes/startsWith.php');
include ('includes/endsWith.php');

require_once("../php/errorHandlers.php");

set_error_handler('myErrorHandler');
register_shutdown_function('fatalErrorShutdownHandler');

$GLOBALS['Db2Schema'] = strtoupper($_ENV['environment']);
$GLOBALS['Db2Schema'] = str_replace('_LOCAL', '', $GLOBALS['Db2Schema']);

$sessionConfig = (new \ByJG\Session\SessionConfig($_SERVER['SERVER_NAME']))
->withTimeoutMinutes(120)
->withSecret($_ENV['jwt_token']);

$handler = new JwtSecureSession($sessionConfig);
session_set_save_handler($handler, true);

session_start();
error_log(__FILE__ . "session:" . session_id());
do_auth();
include "connect.php";
