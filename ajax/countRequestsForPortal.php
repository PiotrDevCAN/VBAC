<?php
use vbac\allTables;
use vbac\assetRequestsTable;

ob_start();
$assetTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$pmoForExport = $assetTable->countRequestsForPmoExport();
$nonPmoForExport = $assetTable->countRequestsForNonPmoExport();
$messages = ob_get_clean();
$response = array('pmoForExport'=>$pmoForExport,'nonPmoForExport'=>$nonPmoForExport);
ob_clean();
echo json_encode($response);