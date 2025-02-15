<?php
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;
use vbac\dlpTable;
use vbac\dlpRecord;
use itdq\BlueMail;
use vbac\personRecord;

// require_once __DIR__ . '/../../src/Bootstrap.php';
$helper = new Sample();
if ($helper->isCli()) {
    $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    return;
}

$emailAddress = array($_ENV['devemailid']);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
// Set document properties
$spreadsheet->getProperties()->setCreator('vBAC')
->setLastModifiedBy('vBAC')
->setTitle('Data Leakage Protection - License Tracker Report')
->setSubject('DLP License Tracker')
->setDescription('DLP License Tracker generated by vBAC')
->setKeywords('office 2007 openxml php vbac dlp licenses tracker')
->setCategory('License Tracker');
// Add some data

$now = new DateTime();

$dlpTable = new dlpTable(allTables::$DLP);

$predicate = " AND D.STATUS in ('" . dlpRecord::STATUS_APPROVED . "','" . dlpRecord::STATUS_PENDING . "')";
try {
    
    ob_clean();
    
    $resultSet = $dlpTable->getForPortal($predicate, false, true);
    
    
    DbTable::writeResultSetToXls($resultSet, $spreadsheet);
    
    DbTable::autoFilter($spreadsheet);
    DbTable::autoSizeColumns($spreadsheet);
    DbTable::setRowColor($spreadsheet,'19DDEBF7',1);
    
    $spreadsheet->setActiveSheetIndex(0);
    $spreadsheet->getActiveSheet()->setTitle('Active Licenses');
    
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Xlsx)
    DbTable::autoSizeColumns($spreadsheet);
    $fileNameSuffix = $now->format('Ymd_His');    

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    
    ob_start();
    $writer->save('php://output');    
    $excelFile = ob_get_clean();
    ob_end_clean();
    $base64EncodedExcelFile = base64_encode($excelFile);
    
    $attachments = array();
    $attachments[] = !empty($excelFile) ? array('filename'=>'Dlp Licenses Report - ' . $fileNameSuffix . ".xls",'content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','data'=>$base64EncodedExcelFile) : null;
    
    BlueMail::send_mail($emailAddress, 'DLP Licenses Report : ' . $fileNameSuffix, 'Please find attached DLP License Report XLS',personRecord::$vbacNoReplyId,array(),array(),true,$attachments);    
    
} catch (Exception $e) {
    
    //    ob_clean();
    
    echo "<br/><br/><br/><br/><br/>";
    
    echo $e->getMessage();
    echo $e->getLine();
    echo $e->getFile();
    echo "<h1>No data found to export to tracker</h1>";
}