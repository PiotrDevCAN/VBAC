<?php

use itdq\slack;
include_once 'itdq/slack.php';

$slack = new slack();


$slack->sendMessageToChannel('Test message from Cirrus',slack::CHANNEL_SM_CDI);