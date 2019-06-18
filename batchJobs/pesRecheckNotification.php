<?php
use itdq\Loader;
use vbac\personRecord;
use itdq\BluePages;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\DbTable;
use itdq\slack;

$slack = new slack();

AuditTable::audit("PES Recheck email to PES Team - invoked.",AuditTable::RECORD_TYPE_DETAILS);
$slack->sendMessageToChannel("PES Recheck email to PES Team - invoked.", slack::CHANNEL_SM_CDI_AUDIT);

set_time_limit(60);

$personTable = new personTable(allTables::$PERSON);
$personTable->notifyRecheckDateApproaching();

AuditTable::audit("PES Recheck email to PES Team - completed.",AuditTable::RECORD_TYPE_DETAILS);
$slack->sendMessageToChannel("PES Recheck email to PES Team - completed.", slack::CHANNEL_SM_CDI_AUDIT);

db2_commit($_SESSION['conn']);