<?php

use vbac\batchJob;

$processName = 'Update PES Fields From cFIRST Script';
$processFile = 'updatePesFieldsFromcFIRSTProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();