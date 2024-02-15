<?php
use vbac\personTable;
use vbac\allTables;

set_time_limit(0);
ob_start();

$personTable = new personTable(allTables::$PERSON);
$personTable->findDirtyData();
