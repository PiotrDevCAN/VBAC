<?php

use itdq\slack;
include_once 'itdq/slack.php';

$slack = new slack();
$result = $slack->slackPostMessageWithEmoji(slack::CHANNEL_SM_CDI_AUDIT,'<!channel> testing', array('thumbsup', 'thumbsdown'));

echo "<pre>";
print_r($result);
echo "</pre>";