<?php
use itdq\BlueMail;

$to = array();
$to[] = $_ENV['devemailid'];

$replyto = $_ENV['noreplyemailid'];
BlueMail::send_mail($to, 'Test', '<h1>Testing 1 2 3</h1>', $replyto);