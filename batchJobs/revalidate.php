<?php
use itdq\Loader;
use vbac\personRecord;
use itdq\BluePages;
use vbac\personTable;
use vbac\allTables;
use itdq\AuditTable;
use itdq\DbTable;
use itdq\slack;

// $slack = new slack();

// AuditTable::audit("Revalidation invoked.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation invoked.");
// error_log($response);

if( isset($GLOBALS['DB_CONNECTION_TIME']) ) {
    echo 'DB connection time: ' . $GLOBALS['DB_CONNECTION_TIME'];
} else {
    echo 'DB connection time is missing !!';
}

set_time_limit(60);

// $personTable = new personTable(allTables::$PERSON);
// $loader = new Loader();

// At start of script
$time_start = microtime(true); 
// $personTable->flagPreboarders();
echo 'do nothing !!';
echo 'Total execution time 0 in seconds: ' . (microtime(true) - $time_start);

db2_commit($GLOBALS['conn']);

/*
// At start of script
$time_start_1 = microtime(true); 
$offboarders = " ( REVALIDATION_STATUS like 'offboard%') ";
$allOffboarders = $loader->load('CNUM',allTables::$PERSON, $offboarders );
var_dump(count($allOffboarders));
AuditTable::audit("Revalidation will ignore " . count($allOffboarders) . " offboarding/ed.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation will ignore " . count($allOffboarders) . " offboarding/ed.");
// error_log($response);
$allOffboarders= null; // free up some storage
// Anywhere else in the script
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start_1);

// At start of script
$time_start_2 = microtime(true); 
$preBoardersPredicate = "   ( REVALIDATION_STATUS = '" . personRecord::REVALIDATED_PREBOARDER . "') ";
$allPreboarders = $loader->load('CNUM',allTables::$PERSON, $preBoardersPredicate );
var_dump(count($allPreboarders));
AuditTable::audit("Revalidation will ignore " . count($allPreboarders) . " pre-boarders.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation will ignore " . count($allPreboarders) . " pre-boarders.");
// error_log($response);
$allPreboarders= null; // free up some storage
// Anywhere else in the script
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start_2);

// At start of script
$time_start_3 = microtime(true); 
$vendorsPredicate = "   ( REVALIDATION_STATUS = '" . personRecord::REVALIDATED_VENDOR . "') ";
$allVendors = $loader->load('CNUM',allTables::$PERSON, $vendorsPredicate );
var_dump(count($allVendors));
AuditTable::audit("Revalidation will ignore " . count($allVendors) . " vendors.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation will ignore " . count($allVendors) . " vendors.");
// error_log($response);
$allVendors= null; // free up some storage
// Anywhere else in the script
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start_3);

// At start of script
$time_start_4 = microtime(true); 
$activeIbmErsPredicate = "   ( trim(REVALIDATION_STATUS) = '' or REVALIDATION_STATUS is null or REVALIDATION_STATUS =  '" . personRecord::REVALIDATED_FOUND . "') ";
$allNonLeavers = $loader->load('CNUM',allTables::$PERSON, $activeIbmErsPredicate );
var_dump(count($allNonLeavers));
AuditTable::audit("Revalidation will check " . count($allNonLeavers) . " people currently flagged as found.",AuditTable::RECORD_TYPE_REVALIDATION);
// $response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation will check " . count($allNonLeavers) . " people currently flagged as found.");
// error_log($response);
$allNonLeavers= null; // free up some storage
// Anywhere else in the script
echo 'Total execution time in seconds: ' . (microtime(true) - $time_start_4);
*/

/*
$chunkedCnum = array_chunk($allNonLeavers, 100);
$detailsFromBp = "notesid&mail";
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

// At this stage, anyone still in the $allNonLeavers array - has NOT been found in BP and so is now POTENTIALLY a leaver and needs to be flagged as such.
AuditTable::audit("Revalidation found " . count($allNonLeavers) . " potential leavers.",AuditTable::RECORD_TYPE_REVALIDATION);
$response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation found " . count($allNonLeavers) . " potential leavers.");

foreach ($allNonLeavers as $cnum){
    set_time_limit(10);
    $personTable->flagPotentialLeaver($cnum);
}

// foreach ($allNonLeavers as $cnum){
//     set_time_limit(10);
//     $personTable->flagLeaver($cnum);
// }

AuditTable::audit("Revalidation completed.",AuditTable::RECORD_TYPE_REVALIDATION);
$response = $slack->slackApiPostMessage(slack::CHANNEL_ID_SM_CDI_AUDIT,$_ENV['environment'] . ":Revalidation completed.");

db2_commit($GLOBALS['conn']);
*/