<?php

use vbac\personDataDownloader;
use vbac\personTable;

$type = personTable::PERSON_DETAILS_ACTIVE_ODC;

$downloader = new personDataDownloader($type);
$downloader->getFile();