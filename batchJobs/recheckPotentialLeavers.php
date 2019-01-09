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

AuditTable::audit("Revalidation recheck potential leavers invoked.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("Revalidation recheck potential leavers invoked.", slack::CHANNEL_SM_CDI_AUDIT);

set_time_limit(60);

$personTable = new personTable(allTables::$PERSON);
$slack = new slack();
$loader = new Loader();

$potentialLeavers = " ( REVALIDATION_STATUS like  'potential%') ";
$allpotentialLeavers = $loader->load('CNUM',allTables::$PERSON, $potentialLeavers ); //
AuditTable::audit("Revalidation re-check will re-check " . count($allpotentialLeavers) . " potential leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("Revalidation re-check will re-check " . count($allpotentialLeavers) . " potential leavers.", slack::CHANNEL_SM_CDI_AUDIT);

$chunkedCnum = array_chunk($allpotentialLeavers, 400);
$detailsFromBp = "&notesid&mail";
$bpEntries = array();

foreach ($chunkedCnum as $key => $cnumList){
    $bpEntries[$key] = BluePages::getDetailsFromCnumSlapMulti($cnumList, $detailsFromBp);
    foreach ($bpEntries[$key]->search->entry as $bpEntry){
        set_time_limit(20);
        $serial = substr($bpEntry->dn,4,9);
        $mail        = ''; // Clear out previous value
        $notesid     = ''; // Clear out previous value
        foreach ($bpEntry->attribute as $details){
            $name = trim($details->name);
            $$name = trim($details->value[0]);
        }
        $notesid = str_replace(array('CN=','OU=','O='),array('','',''),$notesid);
        $personTable->confirmRevalidation($notesid,$mail,$serial);
        unset($allpotentialLeavers[$serial]);
    }
}

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP TWICE and so is now a leaver and needs to be flagged as such.
AuditTable::audit("Revalidation re-check found " . count($allpotentialLeavers) . "  leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("Revalidation re-check found " . count($allpotentialLeavers) . "  leavers.", slack::CHANNEL_SM_CDI_AUDIT);


foreach ($allpotentialLeavers as $cnum){
    set_time_limit(10);
    $personTable->flagLeaver($cnum);
}

AuditTable::audit("Revalidation re-check completed.",AuditTable::RECORD_TYPE_REVALIDATION);
$slack->sendMessageToChannel("Revalidation re-check completed.", slack::CHANNEL_SM_CDI_AUDIT);

db2_commit($_SESSION['conn']);