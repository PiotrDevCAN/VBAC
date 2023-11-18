<?php

use vbac\personDataDownloader;
use vbac\personTable;

$type = personTable::PERSON_DETAILS_ACTIVE;

$downloader = new personDataDownloader($type);
$downloader->getFile();