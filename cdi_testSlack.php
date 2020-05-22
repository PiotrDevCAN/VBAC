<?php

use itdq\slack;
include_once 'itdq/slack.php';


echo "<br/><br/><br/>";

echo "<pre>";
print_r($_ENV);

$slack = new slack();


$result = $slack->sendMessageToChannel('Test message from Cirrus',slack::CHANNEL_SM_CDI);

echo "<pre>";
print_r($result);
echo "</pre>";