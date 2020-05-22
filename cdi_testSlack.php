<?php

use itdq\slack;
include_once 'itdq/slack.php';


echo "<br/><br/><br/>";

echo "<pre>";
print_r($_ENV);
echo "</pre>";

$slack = new slack();


$result = $slack->sendMessageToChannel('Testing from Cirrus',slack::CHANNEL_GENERAL);

echo "<pre>";
print_r($result);
echo "</pre>";