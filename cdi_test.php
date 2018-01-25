<?php

use itdq\Loader;
use vbac\allTables;

$personTable = new personTable(allTables::$PERSON);
$data = $personTable->returnAsArray();

var_dump($data);



// $CO = 'GB';

// $loader = new Loader();
// $countryName = $loader->loadIndexed('COUNTRY_NAME','COUNTRY_CODE',allTables::$STATIC_COUNTRY_CODES, " COUNTRY_CODE='" . db2_escape_string(trim($CO)) . "' ");

// var_dump($countryName);

// var_dump(isset($countryName[$CO]));


// $CO = 'XX';

// $loader = new Loader();
// $countryName = $loader->loadIndexed('COUNTRY_NAME','COUNTRY_CODE',allTables::$STATIC_COUNTRY_CODES, " COUNTRY_CODE='" . db2_escape_string(trim($CO)) . "' ");

// var_dump($countryName);
// var_dump(isset($countryName[$CO]));


