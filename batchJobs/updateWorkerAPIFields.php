<?php

use vbac\batchJob;

$processName = 'Update Worker ID field Script';
$processFile = 'updateWorkerAPIFieldsProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();