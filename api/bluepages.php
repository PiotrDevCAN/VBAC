<?php

$ch = curl_init();

$url = "https://bluepages.ibm.com/BpHttpApisv3/slaphapi?" . $_SERVER['QUERY_STRING'];

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$result = curl_exec($ch);

curl_close($ch);

echo $result;
