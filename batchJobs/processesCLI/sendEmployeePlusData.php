<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;
use itdq\BlueMail;
use vbac\AgileSquadRecord;
use vbac\AgileTribeRecord;
use vbac\personRecord;
use vbac\personTable;
use vbac\reports\employeePlus;
use vbac\staticDataSkillsetsRecord;

set_time_limit(0);
ini_set('memory_limit','2048M');

$_ENV['email'] = 'on';

// require_once __DIR__ . '/../../src/Bootstrap.php';
// $helper = new Sample();
// if ($helper->isCli()) {
//     $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
//     return;
// }

$noreplemailid = $_ENV['noreplykyndrylemailid'];
$emailAddress = array(
    'philip.bibby@kyndryl.com',
    $_ENV['automationemailid']
);

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
// Set document properties
$spreadsheet->getProperties()->setCreator('vBAC')
    ->setLastModifiedBy('vBAC')
    ->setTitle('Employee Plus Report')
    ->setSubject('Employee Plus Report')
    ->setDescription('Employee Plus Report generated by vBAC')
    ->setKeywords('office 2007 openxml php vbac employee plus report')
    ->setCategory('Employee Plus');
    // Add some data

$now = new DateTime();
$resultSetOnly = true;

$report = new employeePlus();

try {
    $resultSet = $report->getReport($resultSetOnly);
    if ($resultSet) {
        DbTable::writeResultSetToXls($resultSet, $spreadsheet);
    }
    
    DbTable::autoFilter($spreadsheet);
    DbTable::autoSizeColumns($spreadsheet);
    DbTable::setRowColor($spreadsheet,'19DDEBF7',1);
    
    $spreadsheet->setActiveSheetIndex(0);
    $spreadsheet->getActiveSheet()->setTitle('Employee Plus');
    
    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $spreadsheet->setActiveSheetIndex(0);
    // Redirect output to a client�s web browser (Xlsx)
    DbTable::autoSizeColumns($spreadsheet);
    // $filePrefix = 'Employee Plus - ';
    $fileNameSuffix = $now->format('Ymd_His');
    // $fileNamePart = $filePrefix . $fileNameSuffix . '.xlsx';
    $fileNamePart = 'Employee Plus Report.xlsx';
    $scriptsDirectory = '/var/www/html/extracts/';
    $fileName = $scriptsDirectory.$fileNamePart;  

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    // $writer->save('php://output');
    $writer->save($fileName);

    $attachments = array();
    $handle = fopen($fileName, "r", true);
    if ($handle !== false) {
        $applicationForm = fread($handle, filesize($fileName));
        fclose($handle);
        $encodedAttachmentFile = base64_encode($applicationForm);
        $attachments[] = array(
            'filename'=>$fileNamePart,
            'content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'data'=>$encodedAttachmentFile,
            'path'=>$fileName
        );
    }
    
    $result = BlueMail::send_mail($emailAddress, 'Employee Plus Report : ' . $fileNameSuffix, 'Please find attached Employee Plus Report XLS',$noreplemailid,array(),array(),true,$attachments);    
    var_dump($result);

} catch (Exception $e) {
    
    //    ob_clean();
    
    echo "<br/><br/><br/><br/><br/>";
    
    echo $e->getMessage();
    echo $e->getLine();
    echo $e->getFile();
    echo "<h1>No data found to export to tracker</h1>";
}