<?php

use vbac\batchJob;

$processName = 'Send list of employees not assigned to Agile Squad / Tribe Script';
$processFile = 'unassignedEmployeesInAgileProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();