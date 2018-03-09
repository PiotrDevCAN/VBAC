<?php


use itdq\BlueMail;

$sendResponse = BlueMail::send_mail(array('robdaniel@uk.ibm.com'), 'test email', 'test email',
    'robdaniel@uk.ibm.com');


echo "<pre>";
var_dump($sendResponse);
echo "</pre>";