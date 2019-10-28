<?php


use vbac\personTable;
use itdq\Loader;
use vbac\allTables;
use vbac\assetRequestRecord;

// $_SERVER['environment'] = 'VBAC';

$loader = new Loader();

$activePeople = $loader->loadIndexed('REVALIDATION_STATUS','CNUM',allTables::$PERSON,personTable::activePersonPredicate());

$predicate = " 1=1 ";
$predicate.= assetRequestRecord::ableToOwnAssets();
$ableToOwn= $loader->loadIndexed('REVALIDATION_STATUS','CNUM',allTables::$PERSON,$predicate);

echo "<pre>";
var_dump(count($activePeople));
echo "</pre>";
echo "<pre>";
var_dump(count($ableToOwn));

$difference = array_diff_key($ableToOwn, $activePeople);
echo "</pre>";
echo "<pre>";
var_dump($difference);
echo "</pre>";