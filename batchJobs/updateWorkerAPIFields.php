<?php

use vbac\batchJob;

$processName = 'Update Worker API fields Script';
$processFile = 'updateWorkerAPIFieldsProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();