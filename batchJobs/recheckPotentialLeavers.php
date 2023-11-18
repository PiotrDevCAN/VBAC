<?php

use vbac\batchJob;

$processName = 'Recheck Potential Leavers Script';
$processFile = 'recheckPotentialLeaversProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();