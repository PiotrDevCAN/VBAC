<?php


use vbac\personTable;
use itdq\Loader;
use vbac\allTables;
use vbac\assetRequestRecord;
use itdq\BlueMail;

// $_ENV['environment'] = 'VBAC';

// $loader = new Loader();

// $activePeople = $loader->loadIndexed('REVALIDATION_STATUS','CNUM',allTables::$PERSON,personTable::activePersonPredicate());

// $predicate = " 1=1 ";
// $predicate.= assetRequestRecord::ableToOwnAssets();
// $ableToOwn= $loader->loadIndexed('REVALIDATION_STATUS','CNUM',allTables::$PERSON,$predicate);

// echo "<pre>";
// var_dump(count($activePeople));
// echo "</pre>";
// echo "<pre>";
// var_dump(count($ableToOwn));

// $difference1 = array_diff_key($ableToOwn, $activePeople);
// $difference2 = array_diff_key($activePeople, $ableToOwn);
// echo "</pre>";
// echo "<pre>";

// echo "<pre>";
// var_dump($difference1);
// echo "</pre>";
// var_dump($difference2);
// echo "</pre>";

phpinfo();
// $sendResponse = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), "Test 1",'text here','rob.daniel@uk.ibm.com',array('rob.daniel@uk.ibm.com'));
// echo "<pre>";
// print_r($sendResponse);
// echo "</pre>";

// $sendResponse = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), "Test 2",'text here','rob.daniel@uk.ibm.com',array(null));
// echo "<pre>";
// print_r($sendResponse);
// echo "</pre>";

// $sendResponse = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), "Test 3",'text here','rob.daniel@uk.ibm.com',array(""));
// echo "<pre>";
// print_r($sendResponse);
// echo "</pre>";