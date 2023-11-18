<?php

use vbac\batchJob;

$processName = 'Test email Script';
$processFile = 'testEmail.php';

$job = new batchJob($processName, $processFile);
$job->run();