<?php

use vbac\personDataDownloader;
use vbac\personTable;

$type = personTable::PERSON_DETAILS_FULL;

$downloader = new personDataDownloader($type);
$downloader->getFile();