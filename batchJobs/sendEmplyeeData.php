<?php

use vbac\batchJob;

$processName = 'Employee Plus Data Extract Script';
$processFile = 'sendEmployeePlusData.php';

$job = new batchJob($processName, $processFile);
$job->run();