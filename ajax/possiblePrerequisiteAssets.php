<?php

use itdq\Loader;
use vbac\allTables;

ob_start();

$assetTitle = isset($_REQUEST['assetTitle']) ? $_REQUEST['assetTitle'] : null;
$preReq     = isset($_REQUEST['assetPrereq']) ? $_REQUEST['assetPrereq'] : null;

$loader = new Loader();
$predicate = " LISTING_ENTRY_REMOVED is null ";
$possiblePrerequisites = $loader->load('ASSET_TITLE',allTables::$REQUESTABLE_ASSET_LIST,$predicate);


$allOptions = array();
$entry = new stdClass(); // For placeholder to work.
$entry->id = "";
$entry->text = "";
$allOptions[] = $entry;


foreach ($possiblePrerequisites as $option){
    $entry = new stdClass();
    $entry->id = trim($option);
    $entry->text = trim($option);
    $assetTitle == $option ? $entry->disabled = true : null;
    $preReq == $option ? $entry->selected = true : null;
    $allOptions[] = $entry;
}
ob_clean();
echo json_encode(array('results'=>$allOptions, 'REQUEST'=>print_r($_REQUEST,true)));