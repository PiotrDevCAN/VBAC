<?php
use itdq\AuditTable;
use itdq\AllItdqTables;
use itdq\BluePages;
use vbac\personTable;

$cnum = personTable::getNextVirtualCnum();

echo "cnum is";

var_dump($cnum);