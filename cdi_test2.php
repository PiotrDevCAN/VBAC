<?php

// use itdq\Email;

// Email::send_mail('rob.daniel@uk.ibm.com','', 'Test', 'Testing 1 2 3', 'rob.daniel@uk.ibm.com');


$message = 'Testing 1 2 3 4' . chr(13) . chr(10) . '13 10 6 7 8 9';

$response = \itdq\BlueMail::send_mail(array('rob.daniel@uk.ibm.com'),'Testing Off',$message, 'vbacNoReply@uk.ibm.com');

echo "<pre>";
print_r($response);

echo $message;

echo "</pre>";

