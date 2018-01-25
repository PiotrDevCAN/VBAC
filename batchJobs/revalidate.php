<?php
use itdq\Loader;
use vbac\personRecord;
use itdq\BluePages;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\DbTable;

AuditTable::audit("Revalidation invoked.",AuditTable::RECORD_TYPE_AUDIT);

set_time_limit(60);

$personTable = new personTable(allTables::$PERSON);

$loader = new Loader();
$activeIbmErsPredicate = "   (( REVALIDATION_STATUS is null or REVALIDATION_STATUS =  '" . personRecord::REVALIDATED_FOUND . "') and CNUM NOT LIKE '%xxx' ) ";
$allNonLeavers = $loader->load('CNUM',allTables::$PERSON, $activeIbmErsPredicate ); //

AuditTable::audit("Revalidation to check " . count($allNonLeavers) . " non-leavers.",AuditTable::RECORD_TYPE_DETAILS);


$chunkedCnum = array_chunk($allNonLeavers, 400);
$detailsFromBp = "&notesid&mail";
$bpEntries = array();

foreach ($chunkedCnum as $key => $cnumList){
    $bpEntries[$key] = BluePages::getDetailsFromCnumSlapMulti($cnumList, $detailsFromBp);
    foreach ($bpEntries[$key]->search->entry as $bpEntry){
        $serial = substr($bpEntry->dn,4,9);
        $mail        = ''; // Clear out previous value
        $notesid      = ''; // Clear out previous value
        foreach ($bpEntry->attribute as $details){
            $name = trim($details->name);
            $$name = trim($details->value[0]);
        }
        $notesid = str_replace(array('CN=','OU=','O='),array('','',''),$notesid);
        $personTable->confirmRevalidation($notesid,$mail,$serial);
        unset($allNonLeavers[$serial]);
    }
}

foreach ($allNonLeavers as $cnum){
    $personTable->flagLeaver($cnum);
}

AuditTable::audit("Revalidation completed.",AuditTable::RECORD_TYPE_AUDIT);

db2_commit($_SESSION['conn']);