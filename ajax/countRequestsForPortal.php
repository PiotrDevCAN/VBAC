<?php
use vbac\allTables;
use vbac\assetRequestsTable;

ob_start();
$assetTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);
$pmoForExportBau    = $assetTable->countRequestsForPmoExport('true');
$pmoForExportNonBau = $assetTable->countRequestsForPmoExport('false');
$pmoForExport       = $pmoForExportBau + $pmoForExportNonBau;
$pmoExported        = $assetTable->countRequestsExported();
$nonPmoForExport    = $assetTable->countRequestsForNonPmoExport();
$bauRaised          = $assetTable->countRequestsRaised(true);
$nonBauRaised       = $assetTable->countRequestsRaised(false);

$messages = ob_get_clean();
$response = array('pmoForExport'=>$pmoForExport,'nonPmoForExport'=>$nonPmoForExport
    ,'pmoExported'=>$pmoExported,'bauForExport'=>$pmoForExportBau
    ,'nonBauForExport'=>$pmoForExportNonBau,'bauRaised'=>$bauRaised
    ,'nonBauRaised'=>$nonBauRaised,'messages'=>$messages);
ob_clean();
echo json_encode($response);