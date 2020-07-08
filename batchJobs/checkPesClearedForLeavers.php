<?php
use itdq\Loader;
use vbac\personRecord;
use itdq\BluePages;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\DbTable;
use itdq\slack;
use vbac\pesEmail;

$slack = new slack();

AuditTable::audit("PES Revalidation invoked.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("PES Revalidation invoked.", slack::CHANNEL_SM_CDI_AUDIT);

set_time_limit(60);

$personTable = new personTable(allTables::$PERSON);
$slack = new slack();
$loader = new Loader();

$pesToCheck = " ( REVALIDATION_STATUS like '" . personRecord::REVALIDATED_OFFBOARDED . "%' AND REVALIDATION_STATUS not like '%leaver%' ) ";
$pesToCheck.= " AND PES_STATUS in ('" . personRecord::PES_STATUS_CLEARED . "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_CLEARED_PERSONAL . "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_EXCEPTION. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_PROVISIONAL. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_REQUESTED. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_INITIATED. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_RESTART. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_RECHECK_REQ. "'";
$pesToCheck.= ")";

$allPeopleToCheck = $loader->load('CNUM',allTables::$PERSON, $pesToCheck ); //
AuditTable::audit("PES Revalidation will check " . count($allPeopleToCheck) . " Offboarded & PES Cleared.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("PES Revalidation will check " . count($allPeopleToCheck) . " Offboarded & PES Cleared.", slack::CHANNEL_SM_CDI_AUDIT);

$chunkedCnum = array_chunk($allPeopleToCheck, 300);
$detailsFromBp = "&notesid";
$bpEntries = array();

foreach ($chunkedCnum as $key => $cnumList){
    $bpEntries[$key] = BluePages::getDetailsFromCnumSlapMulti($cnumList, $detailsFromBp);
    foreach ($bpEntries[$key]->search->entry as $bpEntry){
        set_time_limit(20);
        $serial = substr($bpEntry->dn,4,9);
        unset($allPeopleToCheck[$serial]);
    }
}
// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP TWICE and so is now a leaver and needs to be flagged as such.
AuditTable::audit("PES Revalidation found " . count($allPeopleToCheck) . "  leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("PES Revalidation found  " . count($allPeopleToCheck) . "  leavers.", slack::CHANNEL_SM_CDI_AUDIT);

pesEmail::notifyPesTeamOfLeavers($allPeopleToCheck);
AuditTable::audit("PES Revalidation completed.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("PES Revalidation completed.", slack::CHANNEL_SM_CDI_AUDIT);

db2_commit($GLOBALS['conn']);