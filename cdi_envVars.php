<?php

$token = $_ENV['api_token'];
if($_REQUEST['token']!= $token){
    return;
}

$suppressPesEmails = $_ENV['suppressPesEmails'] ? true : false;
var_dump($suppressPesEmails);

echo '<pre>';
var_dump($_ENV);
echo '</pre>';
