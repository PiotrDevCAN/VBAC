<?php

use vbac\batchJob;

$processName = 'Employee Complete Data Extract Script';
$processFile = 'sendEmployeeCompleteData.php';

$job = new batchJob($processName, $processFile);
$job->run();