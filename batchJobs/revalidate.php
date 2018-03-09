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

$personTable->flagPreboarders();
db2_commit($_SESSION['conn']);

$offboarders = " ( REVALIDATION_STATUS like  'offboard%') ";
$allOffboarders = $loader->load('CNUM',allTables::$PERSON, $offboarders ); //
AuditTable::audit("Revalidation will ignore " . count($offboarders) . " offboarding/ed.",AuditTable::RECORD_TYPE_DETAILS);
$allOffboarders= null; // free up some storage

$preBoardersPredicate = "   ( REVALIDATION_STATUS =  '" . personRecord::REVALIDATED_PREBOARDER . "') ";
$allPreboarders = $loader->load('CNUM',allTables::$PERSON, $preBoardersPredicate ); //
AuditTable::audit("Revalidation will ignore " . count($allPreboarders) . " pre-boarders.",AuditTable::RECORD_TYPE_DETAILS);
$allPreboarders= null; // free up some storage

$activeIbmErsPredicate = "   ( REVALIDATION_STATUS is null or REVALIDATION_STATUS =  '" . personRecord::REVALIDATED_FOUND . "') ";
$allNonLeavers = $loader->load('CNUM',allTables::$PERSON, $activeIbmErsPredicate ); //
AuditTable::audit("Revalidation will check " . count($allNonLeavers) . " people currently flagged as found.",AuditTable::RECORD_TYPE_DETAILS);

$chunkedCnum = array_chunk($allNonLeavers, 400);
$detailsFromBp = "&notesid&mail";
$bpEntries = array();

foreach ($chunkedCnum as $key => $cnumList){
    $bpEntries[$key] = BluePages::getDetailsFromCnumSlapMulti($cnumList, $detailsFromBp);
    foreach ($bpEntries[$key]->search->entry as $bpEntry){
        set_time_limit(20);
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

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP and so is now a leaver and needs to be flagged as such.
AuditTable::audit("Revalidation found " . count($allNonLeavers) . " leavers.",AuditTable::RECORD_TYPE_DETAILS);

foreach ($allNonLeavers as $cnum){
    set_time_limit(10);
    $personTable->flagLeaver($cnum);
}

AuditTable::audit("Revalidation completed.",AuditTable::RECORD_TYPE_AUDIT);

db2_commit($_SESSION['conn']);