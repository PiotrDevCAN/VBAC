<?php

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\BlueMail;
use itdq\DbTable;
use vbac\reports\person\downloadablePersonBAU;
use vbac\reports\person\downloadablePersonDetails;
use vbac\reports\person\downloadablePersonDetailsActive;
use vbac\reports\person\downloadablePersonDetailsActiveODC;
use vbac\reports\person\downloadablePersonDetailsFull;
use vbac\reports\person\downloadablePersonDetailsInactive;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

error_log("download Aurora Person Table Extract process started");

if (isset($argv[1])) {

    $toEmailParam = trim($argv[1]);
    $trackerType = strtolower(trim($argv[2]));

    error_log("Execution parameters: ".$toEmailParam." ".$trackerType);

    switch ($trackerType) {
        case 'details':
            $report = new downloadablePersonDetails();
            break;
        case 'details_full':
            $report = new downloadablePersonDetailsFull();
            break;
        case 'details_active':
            $report = new downloadablePersonDetailsActive();
            break;
        case 'details_active_odc':
            $report = new downloadablePersonDetailsActiveODC();
            break;
        case 'details_inactive':
            $report = new downloadablePersonDetailsInactive();
            break;
        case 'bau':
            $report = new downloadablePersonBAU();
            break;
        default:
            throw new \Exception('Incorrect way of execution of script.');
            break;
    }

    // require_once __DIR__ . '/../../src/Bootstrap.php';
    // $helper = new Sample();
    // if ($helper->isCli()) {
    //     $helper->log('This example should only be run from a Web Browser' . PHP_EOL);
    //     return;
    // }

    $details = $report->getDetails();
    list(
        'title' => $title,
        'subject' => $subject,
        'description' => $description,
        'prefix' => $filePrefix
    ) = $details;

    // Create new Spreadsheet object
    $spreadsheet = new Spreadsheet();
    // Set document properties
    $spreadsheet->getProperties()->setCreator('vBAC')
        ->setLastModifiedBy('vBAC')
        ->setTitle($title)
        ->setSubject($subject)
        ->setDescription($description)
        ->setKeywords('office 2007 openxml php vbac tracker')
        ->setCategory('Person Extract');
        // Add some data

    $now = new DateTime();
    $resultSetOnly = true;
    $recordsFound = false;
    
    set_time_limit(0);
    ini_set('memory_limit','2048M');

    try {
        $resultSet = $report->getReport($resultSetOnly);
        if ($resultSet) {
            $recordsFound = DbTable::writeResultSetToXls($resultSet, $spreadsheet);
        }
        if($recordsFound){
            DbTable::autoFilter($spreadsheet);
            DbTable::autoSizeColumns($spreadsheet);
            DbTable::setRowColor($spreadsheet,'105abd19',1);
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->setTitle('Person Table');
            DbTable::autoSizeColumns($spreadsheet);
            $fileNameSuffix = $now->format('Ymd_His');
            $fileNamePart = $filePrefix . $fileNameSuffix . '.xlsx';
            $scriptsDirectory = '/var/www/html/extracts/';
            $fileName = $scriptsDirectory.$fileNamePart;

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            // $writer->save('php://output');
            $writer->save($fileName);

            // $excelOutput = ob_get_clean();

            $toEmail = array($toEmailParam);
            $subject = 'The Aurora Person Table RAW extract';
            
            $extractRequestEmail = 'Hello &&requestor&&,
            <br/>
            <br/>Please find the attached Aurora Person Table RAW extract.
            <br/>File name: &&fileName&&
            <hr>
            <br/>Many thanks for your cooperation
            <br>vBAC Team';
            
            $extractRequestEmailPattern = array('/&&requestor&&/','/&&fileName&&/');

            $replacements = array($toEmailParam, $fileNamePart);
            $emailBody = preg_replace($extractRequestEmailPattern, $replacements, $extractRequestEmail);

            $pesTaskid = $_ENV['noreplyemailid'];

            if (file_exists($fileName)) {
                // throw new \Exception("The file $fileName exists");
            } else {
                // throw new \Exception("The file $fileName does not exist");
            }

            $pesAttachments = array();
            $handle = fopen($fileName, "r", true);
            if ($handle !== false) {
                $applicationForm = fread($handle, filesize($fileName));
                fclose($handle);
                $encodedAttachmentFile = base64_encode($applicationForm);
                $pesAttachments[] = array(
                    'filename'=>$fileNamePart,
                    'content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'data'=>$encodedAttachmentFile,
                    'path'=>$fileName
                );
            }

            $sendResponse = BlueMail::send_mail($toEmail, $subject, $emailBody, $pesTaskid, array(), array(), false, $pesAttachments);

            var_dump($sendResponse);

            // if ($handle !== false) {
            //     unlink($fileName);
            // }
        } else {
            echo "<h1>No records found to prepare this report</h1>";
        }
    } catch (Exception $e) {
    
        //    ob_clean();
        
        echo "<br/><br/><br/><br/><br/>";
        
        echo $e->getMessage();
        echo $e->getLine();
        echo $e->getFile();
        echo "<h1>No data found to export to tracker</h1>";
    }
} else {
    throw new \Exception('Recipient email address was missing.');
}