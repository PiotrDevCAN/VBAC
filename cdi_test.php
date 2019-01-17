<?php

use vbac\personTable;
use vbac\allTables;

echo "<pre>";

$_SESSION['Db2Schema'] = 'VBAC';

$personTable = new personTable(allTables::$PERSON);

$allMgrs = $personTable->activeFmEmailAddressesByCnum();

echo "<pre>";
print_r($allMgrs);