<?php

use vbac\personTable;
use vbac\allTables;

use vbac\personRecord;




echo "<pre>";

$_SESSION['Db2Schema'] = 'VBAC';

$personTable = new personTable(allTables::$PERSON);

$allMgrs = $personTable->activeFmEmailAddressesByCnum();



$personRecord = new personRecord();
$personRecord->sendCbnEmail();





echo "<pre>";
print_r($allMgrs);