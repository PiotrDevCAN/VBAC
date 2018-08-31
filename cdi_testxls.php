<?php
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\BlueMail;

function getLloydsGlobalApplicationForm(){
    $filename = "./emailAttachments/Lloyds Global Application Form v1.4.doc";
    $handle = fopen($filename, "r");
    $applicationForm = fread($handle, filesize($filename));
    fclose($handle);
    $encodedApplicationForm = base64_encode($applicationForm);
    return $encodedApplicationForm;
}

function getOverseasConsentForm(){
    $filename = "./emailAttachments/Overseas Consent Form Owens (2).pdf";
    $handle = fopen($filename, "r");
    $applicationForm = fread($handle, filesize($filename));
    fclose($handle);
    $encodedApplicationForm = base64_encode($applicationForm);
    return $encodedApplicationForm;
}

function getOdcApplicationForm(){
    $inputFileName = './emailAttachments/ODC application form V2.0.xls';
    
    /** Load $inputFileName to a Spreadsheet Object  **/
    $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
    
    // $spreadsheet = new Spreadsheet();
    // Set document properties
    $spreadsheet->getProperties()->setCreator('vBAC')
    ->setLastModifiedBy('vBAC')
    ->setTitle('Ventus PES application generated from vBAC')
    ->setSubject('Ventus PES application')
    ->setDescription('Ventus PES application generated from vBAC')
    ->setKeywords('office 2007 openxml php vbac tracker')
    ->setCategory('testing 1 2 3');
    
    $spreadsheet->getActiveSheet()
    ->getCell('C17')
    ->setValue('Emp no. here');
    
    $spreadsheet->setActiveSheetIndex(0);
    ob_clean();
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    ob_start();
    $writer->save('php://output');
    $xlsAttachment = ob_get_clean();
    
    $encodedXlsAttachment = base64_encode($xlsAttachment);
    return $encodedXlsAttachment;    
}

$intExt = 'Internal';
$country = 'UK';

$replacements = array('Fred');

switch (true) {
    case $intExt=='External' && $country=='UK':
        include_once 'emailBodies/External_UK.php';
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        $encodedApplicationForm = getLloydsGlobalApplicationForm();
        $pesAttachments        = array(array('filename'=>'Lloyds Global Application Form v1.4.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
        );
    break;
    case $intExt=='External' && $country=='India':
        include_once 'emailBodies/External_India.php';
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        $encodedXlsAttachment = getOdcApplicationForm();
        $encodedApplicationForm = getLloydsGlobalApplicationForm();        
        $pesAttachments = array(array('filename'=>'ODC application form V2.0.xlsx','content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','data'=>$encodedXlsAttachment)
                               ,array('filename'=>'Lloyds Global Application Form v1.4.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
        );
    break; 
    case $country=='Czech':
        include_once 'emailBodies/Either_Czech.php';
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        $encodedApplicationForm = getLloydsGlobalApplicationForm();       
        $pesAttachments = array(array('filename'=>'Lloyds Global Application Form v1.4.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
        );
        break; 
    case $country=='USA':
        include_once 'emailBodies/Either_USA.php';
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);      
        $encodedConsentForm = getOverseasConsentForm();
        $encodedApplicationForm = getLloydsGlobalApplicationForm();
        $pesAttachments = array(array('filename'=>'Lloyds Global Application Form v1.4.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                               ,array('filename'=>'Overseas Consent Form Owens (2).pdf','content_type'=>'application/pdf','data'=>$encodedConsentForm)
        );
        break; 
    case $intExt=='Internal' && $country=='core_4':
        include_once 'emailBodies/Internal_core_4.php';
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        $encodedApplicationForm = getLloydsGlobalApplicationForm();
        $pesAttachments = array(array('filename'=>'Lloyds Global Application Form v1.4.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)            
        );
        break; 
    case $intExt=='Internal' && $country=='India':
        include_once 'emailBodies/Internal_India.php';
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        $encodedApplicationForm = getLloydsGlobalApplicationForm();
        $encodedXlsAttachment = getOdcApplicationForm();
        $pesAttachments = array(array('filename'=>'Lloyds Global Application Form v1.4.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                               ,array('filename'=>'ODC application form V2.0.xlsx','content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','data'=>$encodedXlsAttachment)
        );
        break;
    case $intExt=='Internal' && $country=='International_CRC':
        include_once 'emailBodies/Internal_International_CRC.php';
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        $encodedApplicationForm = getLloydsGlobalApplicationForm();
        $encodedConsentForm = getOverseasConsentForm();
        $pesAttachments = array(array('filename'=>'Lloyds Global Application Form v1.4.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                               ,array('filename'=>'Overseas Consent Form Owens (2).pdf','content_type'=>'application/pdf','data'=>$encodedConsentForm)
        );
        break;
    case $intExt=='Internal' && $country=='International_with_Criminal_Check':
        include_once 'emailBodies/Internal_International_with_Criminal_Check.php';
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        $encodedApplicationForm = getLloydsGlobalApplicationForm();
        $encodedConsentForm = getOverseasConsentForm();
        $pesAttachments = array(array('filename'=>'Lloyds Global Application Form v1.4.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
            ,array('filename'=>'Overseas Consent Form Owens (2).pdf','content_type'=>'application/pdf','data'=>$encodedConsentForm)
        );
        break;
    case $intExt=='Internal' && $country=='UK':
        include_once 'emailBodies/Internal_UK.php';
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        $encodedApplicationForm = getLloydsGlobalApplicationForm();
         $pesAttachments = array(array('filename'=>'Lloyds Global Application Form v1.4.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
        );
        break;
    default:
        die('no match found' . $intExt . $country);
        ;
    break;
}


$sendResponse1 = BlueMail::send_mail(array('rob.daniel@uk.ibm.com'), "NEW URGENT - $country Pre Employment Screening - Fred, Smith>", $emailBody,'LBGVETPR@uk.ibm.com',array(),array(),false,$pesAttachments);

//$sendResponse2 = BlueMail::send_mail(array('robdaniel@ntlworld.com'), '2NEW URGENT - <country> Pre Employment Screening - <first, last>', $emailBody,'rob.daniel@uk.ibm.com',array(),array(),false,$attachments);





