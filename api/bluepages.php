<?php

$ch = curl_init();

$url = "https://bluepages.ibm.com/BpHttpApisv3/slaphapi?" . $_SERVER['QUERY_STRING'];

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);

if(!$result){
    echo curl_errno($ch);
    echo curl_error($ch);
    die('CURL error, see above');
}
curl_close($ch);

echo $result;
