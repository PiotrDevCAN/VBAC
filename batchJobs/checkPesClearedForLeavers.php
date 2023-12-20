<?php

use vbac\batchJob;

$processName = 'Check PES Cleared for Leavers Script';
$processFile = 'checkPesClearedForLeaversProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();