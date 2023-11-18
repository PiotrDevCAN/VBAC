<?php

use vbac\personDataDownloader;
use vbac\personTable;

$type = personTable::PERSON_DETAILS;

$downloader = new personDataDownloader($type);
$downloader->getFile();