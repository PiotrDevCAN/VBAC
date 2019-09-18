<?php
namespace vbac;

use itdq\DbTable;
use vbac\allTables;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\BlueMail;
use itdq\Loader;

class pesEmail {

    private function getLloydsGlobalApplicationForm(){
        // LLoyds Global Application Form v2.0.doc
        $filename = "../emailAttachments/LLoyds Global Application Form v2.0.doc";
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

        $encodedXlsAttachment = base64_encode($xlsAttachment);
        return $encodedXlsAttachment;
    }


    private function determineInternalExternal($emailAddress){
        $ibmEmail = stripos(strtolower($emailAddress), "ibm.com") !== false;

        return $ibmEmail ? 'Internal' : 'External';
    }

    private function getAttachments($intExt,$emailType){
        switch (true) {
            case $intExt=='External' && $emailType=='UK':
                $encodedApplicationForm = $this->getLloydsGlobalApplicationForm();
                $pesAttachments        = array(array('filename'=>'LLoyds Global Application Form v2.0.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                );
                break;
            case $intExt=='Internal' && $emailType=='UK':
                $encodedApplicationForm = $this->getLloydsGlobalApplicationForm();
                $pesAttachments = array(array('filename'=>'LLoyds Global Application Form v2.0.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                );
                break;
            case $intExt=='External' && $emailType=='India':
                $encodedXlsAttachment = $this->getOdcApplicationForm();
                $encodedApplicationForm = $this->getLloydsGlobalApplicationForm();
                $pesAttachments = array(array('filename'=>'ODC application form v3.0.xlsx','content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','data'=>$encodedXlsAttachment)
                    ,array('filename'=>'LLoyds Global Application Form v2.0.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                );
                break;
            case $intExt=='Internal' && $emailType=='India':
                $encodedApplicationForm = $this->getLloydsGlobalApplicationForm();
                $encodedXlsAttachment = $this->getOdcApplicationForm();
                $pesAttachments = array(array('filename'=>'LLoyds Global Application Form v2.0.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                    ,array('filename'=>'ODC application form v3.0.xlsx','content_type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','data'=>$encodedXlsAttachment)
                );
                break;
            case $emailType=='Czech':
                $encodedApplicationForm = $this->getLloydsGlobalApplicationForm();
                $pesAttachments = array(array('filename'=>'LLoyds Global Application Form v2.0.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                );
                break;
            case $emailType=='USA':
                $encodedConsentForm = $this->getOverseasConsentForm();
                $encodedApplicationForm = $this->getLloydsGlobalApplicationForm();
                $pesAttachments = array(array('filename'=>'LLoyds Global Application Form v2.0.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                    ,array('filename'=>'New Overseas Consent Form GDPR.pdf','content_type'=>'application/pdf','data'=>$encodedConsentForm)
                );
                break;
            case $emailType=='core_4':
                $encodedApplicationForm = $this->getLloydsGlobalApplicationForm();
                $pesAttachments = array(array('filename'=>'LLoyds Global Application Form v2.0.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                );
                break;
            case $intExt=='Internal' && $emailType=='International_CRC':
                $encodedApplicationForm = $this->getLloydsGlobalApplicationForm();
                $encodedConsentForm = $this->getOverseasConsentForm();
                $pesAttachments = array(array('filename'=>'LLoyds Global Application Form v2.0.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                    ,array('filename'=>'New Overseas Consent Form GDPR.pdf','content_type'=>'application/pdf','data'=>$encodedConsentForm)
                );
                break;
            case $intExt=='Internal' && $emailType=='International_Credit_Check':
                $encodedApplicationForm = $this->getLloydsGlobalApplicationForm();
                $encodedConsentForm = $this->getOverseasConsentForm();
                $pesAttachments = array(array('filename'=>'LLoyds Global Application Form v2.0.doc','content_type'=>'application/msword','data'=>$encodedApplicationForm)
                    ,array('filename'=>'New Overseas Consent Form GDPR.pdf','content_type'=>'application/pdf','data'=>$encodedConsentForm)
                );
                break;
            default:

                throw new Exception('No matches found for ' . $intExt . ' and ' . $emailType, 803);
                ;
                break;
        }

        return $pesAttachments;
    }

    function getEmailDetails($emailAddress, $country,$openSeat=null){
        $countryCodeTable = new DbTable(allTables::$STATIC_COUNTRY_CODES);
        $intExt = $this->determineInternalExternal($emailAddress);

        $sql = ' SELECT PES_EMAIL ';
        $sql.= ' FROM ' . strtoupper($_SERVER['environment']) . "." . allTables::$STATIC_COUNTRY_CODES;
        $sql.= " WHERE  upper(country_name)= '" . db2_escape_string(strtoupper($country)) . "' ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($rs);

        $pesEmail = trim($row['PES_EMAIL']);

        if(empty($pesEmail)){

            throw new \Exception('PES_EMAIL not defined for country : ' . $country,800);
        }

        $results = preg_split('/[-.]/', $pesEmail);
        $locationType = $results[0];
        $emailType  = isset($results[1]) ? $results[1] : null;
        switch ($locationType) {
            case 'xxx':
                // Need to know if Internal or External
                $pesEmailBodyFilename = $intExt . "-" . $emailType . ".php";
            break;
            case 'unknown':
                throw new Exception('No email defined for ' . $country, 801);
                break;
            default:
                // We don't need to further clarify the PES EMAIL Body file;
                $pesEmailBodyFilename = $pesEmail;
            break;
        }

        $attachments = $this->getAttachments($intExt, $emailType);

        foreach ($attachments as $attachment) {
            $attachmentFileNames[] = $attachment['filename'];
        }


        return array('filename'=> $pesEmailBodyFilename, 'attachments'=>$attachments, 'attachmentFileNames'=> $attachmentFileNames,'emailType'=>$emailType,'splitResults'=>$results);
    }


    function sendPesEmail($firstName, $lastName, $emailAddress, $country, $openseat, $cnum){
            $emailDetails = $this->getEmailDetails($emailAddress, $country);
            $emailBodyFileName = $emailDetails['filename'];
            $pesAttachments = $emailDetails['attachments'];
            $replacements = array($firstName,$openseat);

            include_once 'emailBodies/' . $emailBodyFileName;
            $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);

            $sendResponse = BlueMail::send_mail(array($emailAddress), "NEW URGENT - Pre Employment Screening - $cnum : $firstName, $lastName", $emailBody,'LBGVETPR@uk.ibm.com',array(),array(),false,$pesAttachments);
            return $sendResponse;

    }

    function sendPesEmailChaser($cnum, $emailAddress, $chaserLevel){

        $pesEmailPattern = array(); // Will be overridden when we include_once from emailBodies later.
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.
        $names = personTable::getNamesFromCnum($cnum);
        $firstName = $names['FIRST_NAME'];
        $lastName = $names['LAST_NAME'];
        $requestor = trim($_POST['requestor']);

        $emailBodyFileName = 'chaser' . trim($chaserLevel) . ".php";
        $replacements = array($firstName);

        include_once 'emailBodies/' . $emailBodyFileName;
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);

        $sendResponse = BlueMail::send_mail(array($emailAddress), "Reminder- Pre Employment Screening - $cnum : $firstName, $lastName", $emailBody,'LBGVETPR@uk.ibm.com',array($requestor));
        return $sendResponse;


    }

    function sendPesProcessStatusChangedConfirmation($cnum, $firstName, $lastName, $emailAddress, $processStatus, $requestor=null){

        $pesEmailPattern = array(); // Will be overridden when we include_once from emailBodies later.
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $emailBodyFileName = 'processStatus' . trim($processStatus) . ".php";
        $replacements = array($firstName);

        include_once 'emailBodies/' . $emailBodyFileName;
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);

        $sendResponse = BlueMail::send_mail(array($emailAddress), "Status Change - Pre Employment Screening - $cnum : $firstName, $lastName", $emailBody,'LBGVETPR@uk.ibm.com', array($requestor));
        return $sendResponse;


    }


    static function notifyPesTeamOfUpcomingRechecks($detialsOfPeopleToBeRechecked=null){

        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        include_once 'emailBodies/recheckReport.php';

        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";

        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th><th style='padding:25px;'>Revalidation Status</th><th style='padding:25px;'>Recheck Date</th></tr></thead><tbody>";

        foreach ($detialsOfPeopleToBeRechecked as $personToBeRechecked) {
            $pesEmail.="<tr><td style='padding:15px;'>" . $personToBeRechecked['CNUM'] . "</td><td style='padding:15px;'>" . $personToBeRechecked['NOTES_ID']  . "</td><td style='padding:15px;'>" . $personToBeRechecked['PES_STATUS'] . "</td><td style='padding:15px;'>" . $personToBeRechecked['REVALIDATION_STATUS'] . "</td><td style='padding:15px;'>" . $personToBeRechecked['PES_RECHECK_DATE'] . "</td></tr>";
            ;
        }

        $pesEmail.="</tbody></table>";

        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";



        $emailBody = $pesEmail;

        $sendResponse = BlueMail::send_mail(array('LBGVETPR@uk.ibm.com'), "Upcoming Rechecks", $emailBody,'LBGVETPR@uk.ibm.com');
        return $sendResponse;


    }


    static function notifyPesTeamNoUpcomingRechecks(){

        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        include_once 'emailBodies/recheckReport.php';

        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<p>No upcoming rechecks have been found</p>";
        $emailBody = $pesEmail;

        $sendResponse = BlueMail::send_mail(array('LBGVETPR@uk.ibm.com'), "Upcoming Rechecks-None", $emailBody,'LBGVETPR@uk.ibm.com');
        return $sendResponse;


    }


    static function notifyPesTeamOfLeavers(array $leavers){
        $loader = new Loader();
        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $cnumPredicate = " CNUM IN ('" . implode("','",$leavers) . "') ";
        $allPesStatus = $loader->loadIndexed('PES_STATUS','CNUM',allTables::$PERSON,$cnumPredicate);
        $allNotesid = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$cnumPredicate);

        include_once 'emailBodies/leaversForPes.php';
        $pesEmail.= "<h3>The following people have been identified as having left IBM</h3>";
        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th></tr></thead><tbody>";

        foreach ($leavers as $serial) {
            $pesStatus = isset($allPesStatus[$serial]) ? $allPesStatus[$serial] : 'unknown';
            $notesId   = isset($allNotesid[$serial]) ? $allNotesid[$serial] : 'unknown';
            $pesEmail.="<tr><td style='padding:15px;'>" . $serial . "</td><td style='padding:15px;'>" . $notesId  . "</td><td style='padding:15px;'>" . $pesStatus . "</td></tr>";
        }

        $pesEmail.="</tbody></table>";
        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";

        return BlueMail::send_mail(array('LBGVETPR@uk.ibm.com'), "vbac Leavers", $pesEmail,'LBGVETPR@uk.ibm.com');
    }

    static function notifyPesTeamOfOffboarding($cnum){
        $loader = new Loader();
        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $cnumPredicate = " CNUM = '" . trim($cnum) . "' ";
        $allPesStatus = $loader->loadIndexed('PES_STATUS','CNUM',allTables::$PERSON,$cnumPredicate);
        $allNotesid = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$cnumPredicate);

        $pesEmail.= '<h2>The following perseon is being Offboarded in vBAC<h2>';
        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th></tr></thead><tbody>";

        $pesStatus = isset($allPesStatus[$cnum]) ? $allPesStatus[$cnum] : 'unknown';
        $notesId   = isset($allNotesid[$cnum]) ? $allNotesid[$cnum] : 'unknown';
        $pesEmail.="<tr><td style='padding:15px;'>" . $cnum . "</td><td style='padding:15px;'>" . $notesId  . "</td><td style='padding:15px;'>" . $pesStatus . "</td></tr>";

        $pesEmail.="</tbody></table>";
        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";

        return BlueMail::send_mail(array('LBGVETPR@uk.ibm.com'), "vbac Offboarding", $pesEmail,'LBGVETPR@uk.ibm.com');
    }

    static function notifyPesTeamOfOffboarded($cnum){
        $loader = new Loader();
        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $cnumPredicate = " CNUM = '" . trim($cnum) . "' ";
        $allPesStatus = $loader->loadIndexed('PES_STATUS','CNUM',allTables::$PERSON,$cnumPredicate);
        $allNotesid = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$cnumPredicate);

        $pesEmail.= '<h2>The following perseon has been Offboarded in vBAC<h2>';
        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th></tr></thead><tbody>";

        $pesStatus = isset($allPesStatus[$cnum]) ? $allPesStatus[$cnum] : 'unknown';
        $notesId   = isset($allNotesid[$cnum]) ? $allNotesid[$cnum] : 'unknown';
        $pesEmail.="<tr><td style='padding:15px;'>" . $cnum . "</td><td style='padding:15px;'>" . $notesId  . "</td><td style='padding:15px;'>" . $pesStatus . "</td></tr>";

        $pesEmail.="</tbody></table>";
        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";

        return BlueMail::send_mail(array('LBGVETPR@uk.ibm.com'), "vbac Offboarded", $pesEmail,'LBGVETPR@uk.ibm.com');
    }



}
