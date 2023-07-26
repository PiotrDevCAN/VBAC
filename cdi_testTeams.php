<?php

// use itdq\teams;
// include_once 'itdq/teams.php';

// $slack = new slack();
// $result = $slack->slackPostMessageWithEmoji(slack::CHANNEL_SM_CDI_AUDIT,'<!channel> testing', array('thumbsup', 'thumbsdown'));

$INCOMING_WEBHOOK_URL = 'https://kyndryl.webhook.office.com/webhookb2/11067c20-091b-4dcc-9d85-62c1b784104e@f260df36-bc43-424c-8f44-c85226657b01/IncomingWebhook/da90c1070730409e8efa80440e3d76bd/72f63af0-d95e-4b51-9bd8-d0a1134a769a';

// create connector instance
$connector = new \Sebbmyr\Teams\TeamsConnector($INCOMING_WEBHOOK_URL);
// create card
$card  = new \Sebbmyr\Teams\Cards\SimpleCard(['title' => 'Simple card title', 'text' => 'Simple card text']);
// send card via connector
$connector->send($card);

echo "<pre>";
print_r($result);
echo "</pre>";