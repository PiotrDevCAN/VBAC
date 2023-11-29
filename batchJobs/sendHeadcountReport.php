<?php

use vbac\batchJob;

$processName = 'Headcount Report send out Script';
$processFile = 'sendHeadcountReport.php';

$job = new batchJob($processName, $processFile);
$job->run();