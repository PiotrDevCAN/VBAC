<?php
use itdq\Trace;
use itdq\slack;

$slack = new slack();
$slack->slackPostMessageWithEmoji(slack::CHANNEL_ID_BAU_D_ELT,'*DAILY DELIVERY HEARTBEAT* - Team, please indicate using the emojis below the overall status of your respective areas.', array('thumbsup', 'thumbsdown'));
