<?php

use vbac\personDataDownloader;
use vbac\personTable;

$type = personTable::PERSON_DETAILS_INACTIVE;

$downloader = new personDataDownloader($type);
$downloader->getFile();