<?php


use itdq\BlueMail;

$sendResponse = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'test email', 'test email',
    'rob.daniel@uk.ibm.com');


echo "<pre>";
var_dump($sendResponse);
echo "</pre>";