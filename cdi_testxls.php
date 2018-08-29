<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\BlueMail;

include_once 'emailBodies/External_Indian.php';

$inputFileName = './emailAttachments/ODC application form V2.0.xls';

/** Load $inputFileName to a Spreadsheet Object  **/
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

// $spreadsheet = new Spreadsheet();
// Set document properties
$spreadsheet->getProperties()->setCreator('vBAC')
->setLastModifiedBy('vBAC')
->setTitle('Aurora Master Tracker generated from vBAC')
->setSubject('Aurora Master Tracker')
->setDescription('Aurora Master Tracker generated from vBAC')
->setKeywords('office 2007 openxml php vbac tracker')
->setCategory('testing 1 2 3');

$spreadsheet->getActiveSheet()
->getCell('C17')
->setValue('Emp no. here');




$spreadsheet->setActiveSheetIndex(0);
// Redirect output to a client’s web browser (Xlsx)
ob_clean();
// header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
// header('Content-Disposition: attachment;filename="ODC application form V2.0.xlsx"');
// header('Cache-Control: max-age=0');
// // If you're serving to IE 9, then the following may be needed
// header('Cache-Control: max-age=1');
// // If you're serving to IE over SSL, then the following may be needed
// header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
// header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
// header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
// header('Pragma: public'); // HTTP/1.0
$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
ob_start();
$writer->save('php://output');
$xlsAttachment = ob_get_clean();

$encodedXlsAttachment = base64_encode($xlsAttachment);
$attachment = array(array('filename'=>'ODC application form V2.0.xlsx','content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','data'=>$encodedXlsAttachment));

$replacements = array('Fred');

$emailBody = preg_replace($pesExternalIndianEmailPattern, $replacements, $pesExternalIndianEmail);
$sendResponse1 = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), '1NEW URGENT - <country> Pre Employment Screening - <first, last>', $emailBody,'rob.daniel@uk.ibm.com',array(),array(),false,$attachment);

$sendResponse2 = BlueMail::send_mail(array('robdaniel@ntlworld.com'), '2NEW URGENT - <country> Pre Employment Screening - <first, last>', $emailBody,'rob.daniel@uk.ibm.com',array(),array(),false,$attachment);

$sendResponse3 = BlueMail::send_mail(array('ragdaniel@icloud.com'), '3NEW URGENT - <country> Pre Employment Screening - <first, last>', $emailBody,'rob.daniel@uk.ibm.com',array(),array(),false,$attachment);

var_dump($sendResponse1);
var_dump($sendResponse2);
var_dump($sendResponse3);


