<?php
use itdq\BlueMail;
use vbac\personTable;
use vbac\allTables;

// $attachment = array(array('filename'=>'test.txt','content_type'=>'text/plain','data'=>'VGhpcyBpcyBhIGJhc2U2NCBlbmNvZGVkIHRleHQ='));

// $sendResponse = BlueMail::send_mail(array('rob.daniel@gmail.com'), 'Test email', 'Second email with attachment','rob.daniel@uk.ibm.com',array('rob.daniel@gmail.com','rob.daniel@uk.ibm.com'),array(),false,$attachment);


// echo "<hr/>";


// $base = base64_encode("This is a base64 encoded text");
// var_dump($base);

// $decoded = base64_decode('VGhpcyBpcyBhIGJhc2U2NCBlbmNvZGVkIHRleHQ=');
// var_dump($decoded);


$personTable = new personTable(allTables::$PERSON);

$personTable->notifyRecheckDateApproaching();
