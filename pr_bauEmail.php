<?php

use vbac\personDataDownloader;
use vbac\personTable;

$type = personTable::PERSON_BAU;

$downloader = new personDataDownloader($type);
$downloader->getFile();
