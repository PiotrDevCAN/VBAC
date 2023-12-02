<?php

use vbac\batchJob;

$processName = 'PES Recheck Notification Script';
$processFile = 'pesRecheckNotificationProcess.php';

$job = new batchJob($processName, $processFile);
$job->run();