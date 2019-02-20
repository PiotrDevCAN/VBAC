<?php 

use vbac\odcAccessTable;
use vbac\allTables;

$filename = "../odc_uploads/" . $_POST['filename'];

$odcAccessTable = new odcAccessTable(allTables::$ODC_ACCESS);
$odcAccessTable->copyXlsxToDb2($filename);
