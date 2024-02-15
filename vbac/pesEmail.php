<?php
namespace vbac;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\DbTable;
use vbac\allTables;

class pesEmail {

    const EMAIL_PES_SUPRESSABLE = true;
    const EMAIL_NOT_PES_SUPRESSABLE = false;
    const FILE_TYPE_WORD = 'application/msword';
    const FILE_TYPE_PDF = 'application/pdf';
    const FILE_TYPE_XLS = 'application/xls';

    private function getLloydsGlobalApplicationForm(){
        // FSS Global Application Form v2.0.doc
        $filename = "../emailAttachments/FSS Global Application Form v2.0.doc";
        $handle = fopen($filename, "r");
        $applicationForm = fread($handle, filesize($filename));
        fclose($handle);
        $encodedApplicationForm = base64_encode($applicationForm);
        return $encodedApplicationForm;
    }

    private function getOverseasConsentForm(){
        $filename = "../emailAttachments/Owens_Consent_Form.pdf";
        $handle = fopen($filename, "r");
        $applicationForm = fread($handle, filesize($filename));
        fclose($handle);
        $encodedApplicationForm = base64_encode($applicationForm);
        return $encodedApplicationForm;
    }

    private function getOdcApplicationForm(){
        $inputFileName = '../emailAttachments/ODC application form v3.0.xls';

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
//         ob_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        $xlsAttachment = ob_get_clean();
        ob_start();

        $encodedXlsAttachment = base64_encode($xlsAttachment);
        return $encodedXlsAttachment;
    }

    private function determineInternalExternal($emailAddress){
        $ibmEmail = stripos(strtolower($emailAddress), "ibm.com") !== false;
        return $ibmEmail ? 'Internal' : 'External';
    }

    private function getAttachments($intExt, $emailType, $attachFiles = true){
        switch (true) {
            case $intExt=='External' && $emailType=='UK':
            case $intExt=='Internal' && $emailType=='UK':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc'
                );
                break;
            case $intExt=='External' && $emailType=='India':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/ODC application form v3.0.xls',
                    '../emailAttachments/FSS Global Application Form v2.0.doc'
                );
                break;
            case $intExt=='Internal' && $emailType=='India':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc',
                    '../emailAttachments/ODC application form v3.0.xls'
                );
                break;
            case $emailType=='Czech':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc'
                );
                break;
            case $emailType=='USA':
            case $intExt=='Internal' && $emailType=='International_CRC':
            case $intExt=='Internal' && $emailType=='International_Credit_Check':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc',
                    '../emailAttachments/New Overseas Consent Form GDPR.pdf'
                );
                break;
            case $emailType=='core_4':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc'
                );
                break;
            default:
                throw new \Exception('No matches found for ' . $intExt . ' and ' . $emailType, 803);
                break;
        }

        if ($attachFiles) {
            $pesAttachments = array();
            foreach($pesAttachmentsFileNames as $pesAttachmentFileName) {
                $pesAttachments[] = $this->getAttachmentFile($pesAttachmentFileName);
            }
        } else {
            $pesAttachments = array();
        }

        return array(
            'attachments' => $pesAttachments,
            'attachmentFileNames' => $pesAttachmentsFileNames
        );
    }
    
    private function getRecheckAttachments($recheckEmailFileName=null, $attachFiles = true){
        switch ($recheckEmailFileName) {
            case 'recheck_L1_Core4.php':            
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc'
                );
                break;
            case 'recheck_L1_India_Non_Core4.php':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc',
                    '../emailAttachments/ODC application form v3.0.xls',
                    '../emailAttachments/Owens Consent Form.pdf'
                );
                break;
            case 'recheck_L1_UK.php':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc'
                );
                break;
            case 'recheck_L2_Core4.php':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc'
                );
                break;
            case 'recheck_L2_India_Non_Core4.php':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc',
                    '../emailAttachments/ODC application form v3.0.xls',
                    '../emailAttachments/Owens Consent Form.pdf'
                );
                break;
            case 'recheck_L2_UK.php':
                $pesAttachmentsFileNames = array(
                    '../emailAttachments/FSS Global Application Form v2.0.doc'
                );
                break;
            case 'recheck_offboarded.php':
                $pesAttachmentsFileNames = null;
                break;
            default:
                throw new \Exception('No matches found for ' . $recheckEmailFileName, 804);
                break;
        }

        if ($attachFiles) {
            $pesAttachments = array();
            foreach($pesAttachmentsFileNames as $pesAttachmentFileName) {
                $pesAttachments[] = $this->getAttachmentFile($pesAttachmentFileName);
            }
        } else {
            $pesAttachments = array();
        }

        return array(
            'attachments' => $pesAttachments,
            'attachmentFileNames' => $pesAttachmentsFileNames
        );
    }

    private static function getApplicationFormFile($fileName)
    {
        $handle = fopen($fileName, "r", true);
        $applicationForm = fread($handle, filesize($fileName));
        fclose($handle);
        return base64_encode($applicationForm);
    }

    private static function getXlsApplicationFormFile($fileName)
    {
        $inputFileName = $fileName;

        /** Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);

        // $spreadsheet = new Spreadsheet();
        // Set document properties
        $spreadsheet->getProperties()->setCreator('vBAC')
            ->setLastModifiedBy('vBAC')
            ->setTitle('PES Application Form generated by vBAC')
            ->setSubject('PES Application')
            ->setDescription('PES Application Form generated by vBAC')
            ->setKeywords('office 2007 openxml php vbac tracker')
            ->setCategory('category');

        $spreadsheet->getActiveSheet()
            ->getCell('C17')
            ->setValue('Emp no. here');

        $spreadsheet->setActiveSheetIndex(0);
        // ob_clean();
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        $xlsAttachment = ob_get_clean();

        return base64_encode($xlsAttachment);
    }

    function getAttachmentFile($fileName = ''){
        $fileNameShort = str_replace('../emailAttachments/', '' ,$fileName);
        $fileExtension = substr($fileName, -3);
        $contentType = '';
        $encodedAttachmentFile = null;
        $directoryPath = $fileName;
        switch($fileExtension) {
            case 'pdf':
                $contentType = self::FILE_TYPE_PDF;
                $encodedAttachmentFile = self::getApplicationFormFile($fileName);
                break;
            case 'doc':
                $contentType = self::FILE_TYPE_WORD;
                $encodedAttachmentFile = self::getApplicationFormFile($fileName);
                break;
            case 'xls':
                $contentType = self::FILE_TYPE_XLS;
                $encodedAttachmentFile = self::getXlsApplicationFormFile($fileName);
                break;
            default:
                break;
        }
        if (!empty($fileName)) {
            $data = array(
                'filename' => $fileNameShort,
                'content_type' => $contentType,
                'data' => $encodedAttachmentFile,
                'path' => $directoryPath
            );
        } else {
            $data = array();
        }
        return $data;
    }
    
    function getEmailDetails(personRecord $person, $recheck='no', $attachFiles = true){
        
        $cnum = $person->getValue('CNUM');
        $workerId = $person->getValue('WORKER_ID');
        $emailAddress = $person->getValue('EMAIL_ADDRESS');
        $country = $person->getValue('COUNTRY'); 
        $openSeat = $person->getValue('OPEN_SEAT'); 

        $revalidationStatus = personTable::getRevalidationStatus($cnum, $workerId, $emailAddress);
        
        $offboarded = substr($revalidationStatus,0,10)==personRecord::REVALIDATED_OFFBOARDED ? true : false;
        
        error_log($cnum . ":" . $workerId . ":" . $emailAddress . ":" . $revalidationStatus);
        error_log(substr($revalidationStatus,0,10));
        error_log($offboarded);
            
        $intExt = $recheck!='yes' ?  $this->determineInternalExternal($emailAddress) : null;
        
        $emailField = $recheck=='yes' ? "RECHECK_EMAIL" : "PES_EMAIL";
        
        if($offboarded && $recheck=='yes'){
            $pesEmail = 'recheck_offboarded.php';            
        } else {
            $sql = " SELECT $emailField ";
            $sql.= ' FROM ' . strtoupper($_ENV['environment']) . "." . allTables::$STATIC_COUNTRY_CODES;
            $sql.= " WHERE  upper(country_name)= '" . htmlspecialchars(strtoupper($country)) . "' ";
            
            error_log('Recheck:'. print_r($recheck,true));
            error_log($sql);
            
            $rs = sqlsrv_query($GLOBALS['conn'], $sql);
            
            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }
            
            $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
            
            $pesEmail = trim($row[$emailField]);
        }
        
        if(empty($pesEmail)){
            throw new \Exception("$emailField not defined for country : " . $country,800);
        }
       
        switch($recheck=='yes'){
            case true :                
                $pesLevelOrig = personTable::getPesLevelFromEmail($emailAddress);
                $pesLevel = str_replace('evel ', '', trim($pesLevelOrig)); // Condense to L1, L2 etc.
                $pesEmailBodyFileName = str_replace('xx', $pesLevel,$pesEmail);
                $emailType=null;
                $results = null;
                break;
            case false: 
                $results = preg_split('/[-.]/', $pesEmail);
                $locationType = $results[0];
                $emailType  = isset($results[1]) ? $results[1] : null;
                switch ($locationType) {
                    case 'xxx':
                        // Need to know if Internal or External
                        $pesEmailBodyFileName = $intExt . "-" . $emailType . ".php";
                        break;
                    case 'unknown':
                        throw new \Exception('No email defined for ' . $country, 801);
                        break;
                    default:
                        // We don't need to further clarify the PES EMAIL Body file;
                        $pesEmailBodyFileName = $pesEmail;
                        break;
                }
                break;
            default : 
                throw new \Exception('$recheck is neither TRUE nor FALSE', 801);
                break;
        }
        
        $attachmentsData = $recheck=='yes' ? $this->getRecheckAttachments($pesEmailBodyFileName, $attachFiles) :  $this->getAttachments($intExt, $emailType, $attachFiles);
        list('attachments' => $attachments, 'attachmentFileNames' => $attachmentFileNames) = $attachmentsData;

        return array(
            'filename' => $pesEmailBodyFileName, 
            'attachments' => $attachments, 
            'attachmentFileNames' => $attachmentFileNames, 
            'emailType' =>$emailType, 
            'splitResults' => $results
        );
    }
}
