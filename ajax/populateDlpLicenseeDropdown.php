<?php

use vbac\personTable;
use itdq\Loader;
use vbac\assetRequestRecord;
use vbac\allTables;
use vbac\dlpRecord;

$loader = new Loader();
$predicate = " 1=1 " . assetRequestRecord::ableToOwnAssets();
$myManagersCnum = personTable::myManagersCnum();

$selectableNotesId = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$predicate);
$selectableEmailAddress = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON,$predicate);
$selectableRevalidationStatus = $loader->loadIndexed('REVALIDATION_STATUS','CNUM',allTables::$PERSON,$predicate);   

//$currentLicences = $loader->loadIndexed('HOSTNAME','CNUM', allTables::$DLP," TRANSFERRED_TO_HOSTNAME is null && STATUS not in ('" . dlpRecord::STATUS_REJECTED . "','" . dlpRecord::STATUS_TRANSFERRED . "') ");
$currentLicences = $loader->loadIndexed('HOSTNAME','CNUM', allTables::$DLP," STATUS not in ('" . dlpRecord::STATUS_TRANSFERRED . "','". dlpRecord::STATUS_REJECTED. "')  ");

$options = array();


foreach ($selectableNotesId as $cnum => $notesId){
    $isOffboarding = substr($selectableRevalidationStatus[$cnum],0,11)=='offboarding';
    $dataOffboarding = " data-revalidationstatus" . "='" . $selectableRevalidationStatus[$cnum] . "' ";
    $displayedName = !empty(trim($notesId)) ?  trim($notesId) : $selectableEmailAddress[$cnum];
    $hostname = isset($currentLicences[trim($cnum)]) ? " (" .  $currentLicences[trim($cnum)] . ")" : " (no licence)";
    if(!$isOffboarding){
        $options[]= (object) array('id'=> trim($cnum),'text'=> $displayedName . $hostname);
    }
};



$response = array("results"=>$options,'currentLicences'=>print_r($currentLicences,true));

ob_clean();
echo json_encode($response);