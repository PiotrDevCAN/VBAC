<?php


use vbac\personTable;
use vbac\assetRequestRecord;

include_once 'itdq\xls.php';
include_once 'itdq\FormClass.php';
include_once 'itdq\DbRecord.php';
include_once 'itdq\DbTable.php';
include_once 'vbac\personRecord.php';
include_once 'vbac\personTable.php';
include_once 'vbac\assetRequestRecord.php';

$pred = personTable::activePersonPredicate();

$pred2 = assetRequestRecord::ableToOwnAssets();

print_r($pred);

echo "\n";

print_r($pred2);