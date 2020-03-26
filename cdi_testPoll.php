<?php
use itdq\Trace;
use itdq\slack;

Trace::pageOpening($_SERVER['PHP_SELF']);

$slack = new slack();

// $join = $slack->slackJoinChannel(slack::CHANNEL_ID_POLYTEST);

// $joinObj = json_decode($join);
// echo "<pre>";
// print_r($joinObj);

// $slack->slackPostMessageWithEmoji(slack::CHANNEL_ID_BAU_D_ELT,'Testing message, please ignore', array('thumbsup', 'thumbsdown'));


$slack->slackPostMessageWithEmoji(slack::CHANNEL_ID_BAU_D_ELT,'*DAILY DELIVERY HEARTBEAT* - Team, please indicate using the emojis below the overall status of your respective areas.', array('thumbsup', 'thumbsdown'));

// DAILY DELIVERY HEARTBEAT - Team, please indicate using the emojis below the overall status of your respective areas.


Trace::pageLoadComplete($_SERVER['PHP_SELF']);