<?php

use vbac\personTable;
use vbac\allTables;

use vbac\personRecord;
use itdq\slack;


$slack = new slack();

$slack->sendMessageToChannel("test", slack::CHANNEL_SM_CDI);



echo "<pre>";

// $_SESSION['Db2Schema'] = 'VBAC';

// $personTable = new personTable(allTables::$PERSON);

// $allMgrs = $personTable->activeFmEmailAddressesByCnum();


// $personRecord = new personRecord();
// $personRecord->sendCbnEmail();

// $_SESSION['Db2Schema'] = 'ROB_DEV';


// $pmoTaskId = array('rob.daniel@uk.ibm.com');
// $emailMessage = "Hello world";
// $groupOfFmEmail = array('daniero@uk.ibm.com','antstark@uk.ibm.com','e3h3j0u9u6l2q3a3@ventusdelivery.slack.com');


// \itdq\BlueMail::send_mail($pmoTaskId, 'CBN Test' , $emailMessage, 'vbacCbnNoReply@uk.ibm.com',array(),$groupOfFmEmail);




// echo "<pre>";
// print_r($allMgrs);