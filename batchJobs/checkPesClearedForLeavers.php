<?php
use itdq\Loader;
use vbac\personRecord;
use itdq\BluePages;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\DbTable;
use vbac\pesEmail;

AuditTable::audit("Check for Leavers invoked.",AuditTable::RECORD_TYPE_REVALIDATION);

set_time_limit(0);
ini_set('memory_limit','2048M');

$personTable = new personTable(allTables::$PERSON);
$loader = new Loader();

$pesToCheck = " ( REVALIDATION_STATUS like '" . personRecord::REVALIDATED_OFFBOARDED . "%' AND REVALIDATION_STATUS NOT LIKE '%" . personRecord::REVALIDATED_LEAVER . "%' ) ";
$pesToCheck.= " AND PES_STATUS IN ('" . personRecord::PES_STATUS_CLEARED . "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_CLEARED_PERSONAL . "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_CLEARED_AMBER . "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_EXCEPTION. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_PROVISIONAL. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_REQUESTED. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_INITIATED. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_RECHECK_PROGRESSING. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_RESTART. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_RECHECK_REQ. "'";
$pesToCheck.= ",'" . personRecord::PES_STATUS_MOVER. "'";
$pesToCheck.= ")";

$allPeopleToCheck = $loader->load('CNUM',allTables::$PERSON, $pesToCheck ); //
AuditTable::audit("Check for Leavers will check " . count($allPeopleToCheck) . " Offboarded & PES Cleared.",AuditTable::RECORD_TYPE_REVALIDATION);

$chunkedCnum = array_chunk($allPeopleToCheck, 200);
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
AuditTable::audit("Check for Leavers found " . count($allPeopleToCheck) . "  leavers.",AuditTable::RECORD_TYPE_REVALIDATION);

foreach ($allPeopleToCheck as $cnum){
    set_time_limit(10);
    $personTable->flagPotentialLeaverByCNUM($cnum);
}

// pesEmail::notifyPesTeamOfLeavers($allPeopleToCheck); Mar 2021 - Carra doesn't want this email anymore.
AuditTable::audit("Check for Leavers completed.",AuditTable::RECORD_TYPE_REVALIDATION);