<?php
use itdq\BlueMail;

$attachment = array(array('filename'=>'test.txt','content_type'=>'text/plain','data'=>'VGhpcyBpcyBhIGJhc2U2NCBlbmNvZGVkIHRleHQ='));

$sendResponse = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), 'Third email with', 'Second email with attachment','rob.daniel@uk.ibm.com',array(),array(),false,$attachment);


echo "<hr/>";


$base = base64_encode("This is a base64 encoded text");
var_dump($base);

$decoded = base64_decode('VGhpcyBpcyBhIGJhc2U2NCBlbmNvZGVkIHRleHQ=');
var_dump($decoded);
