<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?><div class='container test '>
<?php

echo "<br><br>";
echo "==============================================";
$ip = "9.15.48.48";
$port = "389";
// exec("ping -c 3 $ip 2>&1", $output, $result);
exec("nmap -p $port $ip 2>&1", $output1, $result);
foreach($output1 as $response1){
    echo "<p>".$response1."</p>\r\n";
}

echo "<br><br>";
echo "==============================================";
$ip = "9.15.48.48";
$port = "636";
// exec("ping -c 3 $ip 2>&1", $output, $result);
exec("nmap -p $port $ip 2>&1", $output2, $result);
foreach($output2 as $response2){
    echo "<p>".$response2."</p>\r\n";
}

echo "<br><br>";
echo "==============================================";
$ip = "9.15.0.48";
$port = "389";
// exec("ping -c 3 $ip 2>&1", $output, $result);
exec("nmap -p $port $ip 2>&1", $output3, $result);
foreach($output3 as $response3){
    echo "<p>".$response3."</p>\r\n";
}

echo "<br><br>";
echo "==============================================";
$ip = "9.15.0.48";
$port = "636";
// exec("ping -c 3 $ip 2>&1", $output, $result);
exec("nmap -p $port $ip 2>&1", $output4, $result);
foreach($output4 as $response4){
    echo "<p>".$response4."</p>\r\n";
}

?></div><?php
