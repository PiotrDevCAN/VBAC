<?php

use vbac\batchJob;

$processName = 'Update cFIRST fields Script';
$processFile = 'updatecFIRSTFieldsProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();