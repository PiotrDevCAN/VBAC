
<?php
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;
// require_once __DIR__ . '/../../src/Bootstrap.php';
$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    return;
}
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



// $spreadsheet->setActiveSheetIndex(0)
// ->setCellValue('A1', 'Hello')
// ->setCellValue('B2', 'world!')
// ->setCellValue('C1', 'Hello')
// ->setCellValue('D2', 'world!');


$sql = " SELECT P.*,AR.* ";
$sql .= " FROM " . $_SESSION['Db2Schema']. "." . allTables::$ASSET_REQUESTS  . " as AR ";
$sql .= " LEFT JOIN " . $_SESSION['Db2Schema']. "." . allTables::$PERSON . " as P ";
$sql .= " ON P.CNUM = AR.CNUM ";

$rs = db2_exec($_SESSION['conn'], $sql);


DbTable::writeResultSetToXls($rs, $spreadsheet);



$sheet = $spreadsheet->getActiveSheet();
$cellIterator = $sheet->getRowIterator()->current()->getCellIterator();
$cellIterator->setIterateOnlyExistingCells(true);
/** @var PHPExcel_Cell $cell */
foreach ($cellIterator as $cell) {
    $sheet->getColumnDimension($cell->getColumn())->setAutoSize(true);
}


$spreadsheet->getActiveSheet()->setAutoFilter(
    $spreadsheet->getActiveSheet()
    ->calculateWorksheetDimension()
    );



// Rename worksheet
$spreadsheet->getActiveSheet()->setTitle('Master Tracker');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$spreadsheet->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Xlsx)

$now = new DateTime();
$fileNameSuffix = $now->format('Ymd_his');


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