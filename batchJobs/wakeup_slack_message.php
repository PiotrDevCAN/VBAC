<?php
use itdq\Trace;
use itdq\slack;

$slack = new slack();
$response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,'<!channel> *CDI Scheduler has started*');
var_dump($response);
