<?php
use vbac\personTable;
use itdq\Loader;
use vbac\allTables;

$loader = new Loader();
$allNotesidByCnum = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON);

echo "<pre>";
var_dump($allNotesidByCnum);
echo "</pre>";