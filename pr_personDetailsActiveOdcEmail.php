<?php

use vbac\personTable;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$type = personTable::PERSON_DETAILS_ACTIVE_ODC;

require "batchJobs/personDataDownloader.php";
