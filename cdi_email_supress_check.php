<?php



use itdq\BlueMail;
use vbac\pesEmail;

$response = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'Do not supress', 'This should always get through ' . print_r($_ENV['suppressPesEmails'],true), 'daniero@uk.ibm.com',array(),array(),true,array(),pesEmail::EMAIL_NOT_PES_SUPRESSABLE);
print_r($response);

echo "<hr/>";

$response = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'Supress this one', 'This should not always get through'  . print_r($_ENV['suppressPesEmails'],true), 'daniero@uk.ibm.com',array(),array(),true,array(),pesEmail::EMAIL_PES_SUPRESSABLE);
print_r($response);