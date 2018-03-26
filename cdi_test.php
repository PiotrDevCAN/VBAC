<?php

use vbac\personTable;
use vbac\allTables;

$table = new personTable(allTables::$PERSON);


$cols = $table->getColumns();

var_dump($cols);