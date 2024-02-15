<?php
use itdq\Trace;
use itdq\BlueGroups;
use itdq\BlueMail;

$to = array();
$to[] = $_ENV['devemailid'];

$replyto = 'test@kyndryl.com';
$result = BlueMail::send_mail($to, 'Test', '<h1>Testing 1 2 3</h1>', $replyto);

var_dump($result);