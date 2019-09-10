<?php
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;
use itdq\Loader;
use vbac\assetRequestsTable;
use vbac\personRecord;
use vbac\personWithSubPTable;

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
->setTitle('Aurora Person Table Extract generated from vBAC')
->setSubject('Full Person Table')
->setDescription('urora Person Table Extract generated from vBAC')
->setKeywords('office 2007 openxml php vbac tracker')
->setCategory('Person Extract');
// Add some data

$now = new DateTime();

$personTable = new assetRequestsTable(allTables::$PERSON);

try {
    $sheet = 1;
    $sql = " Select * ";
    $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
    $sql .= " WHERE PES_STATUS_DETAILS is null or PES_STATUS_DETAILS not like 'Boarded%' ";  // dont show boarded pre-boarders

    set_time_limit(0);
    ini_set('memory_limit', '256M');

    $rs = db2_exec($_SESSION['conn'], $sql);

    if($rs){
        $recordsFound = personWithSubPTable::writeResultSetToXls($rs, $spreadsheet);
        if($recordsFound){
            DbTable::autoFilter($spreadsheet);
            DbTable::autoSizeColumns($spreadsheet);
            DbTable::setRowColor($spreadsheet,'105abd19',1);
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setTitle('Person Table');
            DbTable::autoSizeColumns($spreadsheet);
            $fileNameSuffix = $now->format('Ymd_His');

            ob_clean();
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="personExtractFull' . $fileNameSuffix . '.xlsx"');
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
        }
    }
} catch (Exception $e) {

//    ob_clean();

    echo "<br/><br/><br/><br/><br/>";

    echo $e->getMessage();
    echo $e->getLine();
    echo $e->getFile();
    echo "<h1>Problem found. See above.</h1>";
}