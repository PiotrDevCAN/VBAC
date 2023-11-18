<?php

use vbac\batchJob;

$processName = 'Revalidation Script';
$processFile = 'revalidateProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();