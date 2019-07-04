<?php
use itdq\Trace;
use vbac\personTable;
use vbac\allTables;

Trace::pageOpening($_SERVER['PHP_SELF']);


$personTable = new personTable(allTables::$PERSON);

$allrecheckers = $personTable->notifyRecheckDateApproaching();

?><pre><?php
print_r($allrecheckers);
?></pre><?php


Trace::pageLoadComplete($_SERVER['PHP_SELF']);