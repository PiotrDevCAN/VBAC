<?php

use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\slack;

$slack = new slack();

AuditTable::audit("PES Recheck email to PES Team - invoked.",AuditTable::RECORD_TYPE_DETAILS);
$slack->sendMessageToChannel("PES Recheck email to PES Team - invoked.(" . $_ENV['environment'] . ")", slack::CHANNEL_SM_CDI_AUDIT);

set_time_limit(0);
ini_set('memory_limit','2048M');

$timeMeasurements = array();
$start = microtime(true);

$personTable = new personTable(allTables::$PERSON);
$personTable->notifyRecheckDateApproaching();

$end = microtime(true);
$timeMeasurements['phase_0'] = (float)($end-$start);

AuditTable::audit("PES Recheck email to PES Team - completed.",AuditTable::RECORD_TYPE_DETAILS);
$slack->sendMessageToChannel("PES Recheck email to PES Team - completed.(" . $_ENV['environment'] . ")", slack::CHANNEL_SM_CDI_AUDIT);
