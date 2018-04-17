<?php
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;
use itdq\Loader;
use vbac\assetRequestsTable;
// require_once __DIR__ . '/../../src/Bootstrap.php';
$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    return;
}

echo "<pre>";


// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
// Set document properties
$spreadsheet->getProperties()->setCreator('vBAC')
->setLastModifiedBy('vBAC')
->setTitle('Aurora Master Tracker generated from vBAC')
->setSubject('Aurora Master Tracker')
->setDescription('Aurora Master Tracker generated from vBAC')
->setKeywords('office 2007 openxml php vbac tracker')
->setCategory('Master Tracker');
// Add some data

$now = new DateTime();

$assetRequestTable = new assetRequestsTable(allTables::$ASSET_REQUESTS);

try {
        $assetRequestTable->getTracker($spreadsheet);
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $spreadsheet->setActiveSheetIndex(0);
        // Redirect output to a client’s web browser (Xlsx)

        DbTable::autoSizeColumns($spreadsheet);

        $fileNameSuffix = $now->format('Ymd_His');

        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="masterTracker' . $fileNameSuffix . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;



} catch (Exception $e) {

    ob_clean();

    echo $e->getMessage();
    echo "<h1>No data found to export to tracker</h2>";
}