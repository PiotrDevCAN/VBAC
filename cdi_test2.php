<?php

// use itdq\Email;

// Email::send_mail('rob.daniel@uk.ibm.com','', 'Test', 'Testing 1 2 3', 'rob.daniel@uk.ibm.com');

$response = \itdq\BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'Testing Off', 'Testing 1 2 3 4 5 6 7 8 9', 'vbacNoReply@uk.ibm.com');

echo "<pre>";
print_r($response);
echo "</pre>";