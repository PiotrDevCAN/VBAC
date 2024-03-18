<?php
use itdq\Trace;
use itdq\BlueGroups;
use itdq\BlueMail;

$to = array();
$to[] = $_ENV['devemailid'];

$replyto = 'test@kyndryl.com';
$result = BlueMail::send_mail($to, 'Test', '<h1>Testing 1 2 3 - New HOSTNAME </h1>', $replyto, array(), array(), false, array(), true);

var_dump($result);