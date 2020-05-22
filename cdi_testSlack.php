<?php

use itdq\slack;
include_once 'itdq/slack.php';


echo "<br/><br/><br/>";

echo "<pre>";
print_r($_ENV);
echo "</pre>";

$slack = new slack();


$result = $slack->slackApiPostMessage(slack::CHANNEL_SM_CDI,'Test message from Cirrus');

echo "<pre>";
print_r($result);
echo "</pre>";