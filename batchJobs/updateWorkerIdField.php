<?php

use vbac\batchJob;

$processName = 'Update Worker ID field Script';
$processFile = 'updateWorkerIdFieldProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();