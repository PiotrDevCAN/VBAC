<?php

use vbac\batchJob;

$processName = 'Load cFirst Candidates Script';
$processFile = 'loadcFIRSTCandidatesProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();