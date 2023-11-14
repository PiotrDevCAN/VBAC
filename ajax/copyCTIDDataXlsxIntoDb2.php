<?php 

use vbac\allTables;
use vbac\personTable;

$filename = "../ct_id_uploads/" . $_POST['filename'];

$personTable = new personTable(allTables::$PERSON);
$personTable->copyCTIDXlsxToDb2($filename);
