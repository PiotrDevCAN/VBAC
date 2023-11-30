<?php

use vbac\batchJob;

$processName = 'Recheck Missing Bands Script';
$processFile = 'recheckMissingBandsProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();