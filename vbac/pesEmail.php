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

    private function getAttachments($intExt,$emailType){
        switch (true) {
            case $intExt=='External' && $emailType=='UK':
            case $intExt=='Internal' && $emailType=='UK':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc');
                break;
            case $intExt=='External' && $emailType=='India':
                $pesAttachments = array('../emailAttachments/ODC application form v3.0.xls'
                                       ,'../emailAttachments/FSS Global Application Form v2.0.doc');
                break;
            case $intExt=='Internal' && $emailType=='India':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc'
                                       ,'../emailAttachments/ODC application form v3.0.xls');
                break;
            case $emailType=='Czech':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc');
                break;
            case $emailType=='USA':
            case $intExt=='Internal' && $emailType=='International_CRC':
            case $intExt=='Internal' && $emailType=='International_Credit_Check':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc'
                                       ,'../emailAttachments/New Overseas Consent Form GDPR.pdf');
                break;
            case $emailType=='core_4':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc');
                break;
            default:
                throw new \Exception('No matches found for ' . $intExt . ' and ' . $emailType, 803);
                break;
        }

        return $pesAttachments;
    }
    
    private function getRecheckAttachments($recheckEmailFileName=null){
        switch ($recheckEmailFileName) {
            case 'recheck_L1_Core4.php':            
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc');
                break;
            case 'recheck_L1_India_Non_Core4.php':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc'
                                       ,'../emailAttachments/ODC application form v3.0.xls'
                                       ,'../emailAttachments/Owens Consent Form.pdf');
                break;
            case 'recheck_L1_UK.php':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc');
                break;
            case 'recheck_L2_Core4.php':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc');
                break;
            case 'recheck_L2_India_Non_Core4.php':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc'
                                       ,'../emailAttachments/ODC application form v3.0.xls'
                                       ,'../emailAttachments/Owens Consent Form.pdf');
                break;
            case 'recheck_L2_UK.php':
                $pesAttachments = array('../emailAttachments/FSS Global Application Form v2.0.doc');
                break;
            case 'recheck_offboarded.php':
                $pesAttachments = null;
                break;
            default:
                throw new \Exception('No matches found for ' . $recheckEmailFileName, 804);
                break;
        }
        
        return $pesAttachments;
    }
    

    function getEmailDetails($emailAddress, $country,$openSeat=null,$recheck='no'){
        
        $revalidationStatus = personTable::getRevalidationFromCnum(null, $emailAddress);
        
        $offboarded = substr($revalidationStatus,0,10)=='offboarded' ? true : false;
        
        error_log($emailAddress . ":" . $revalidationStatus);
        error_log(substr($revalidationStatus,0,10));
        error_log($offboarded);
            
        $intExt = $recheck!='yes' ?  $this->determineInternalExternal($emailAddress) : null;
        
        $emailField = $recheck=='yes' ? "RECHECK_EMAIL" : "PES_EMAIL";
        
        if($offboarded && $recheck=='yes'){
            $pesEmail = 'recheck_offboarded.php';            
        } else {
            $sql = " SELECT $emailField ";
            $sql.= ' FROM ' . strtoupper($_ENV['environment']) . "." . allTables::$STATIC_COUNTRY_CODES;
            $sql.= " WHERE  upper(country_name)= '" . db2_escape_string(strtoupper($country)) . "' ";
            
            error_log('Recheck:'. print_r($recheck,true));
            error_log($sql);
            
            $rs = db2_exec($GLOBALS['conn'], $sql);
            
            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }
            
            $row = db2_fetch_assoc($rs);
            
            $pesEmail = trim($row[$emailField]);
        }
        


        if(empty($pesEmail)){
            throw new \Exception("$emailField not defined for country : " . $country,800);
        }
       
        switch($recheck=='yes'){
            case true :                
                $pesLevelOrig = personTable::getPesLevelFromEmail($emailAddress);
                $pesLevel = str_replace('evel ', '', trim($pesLevelOrig)); // Condense to L1, L2 etc.
                $pesEmailBodyFilename = str_replace('xx', $pesLevel,$pesEmail);
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
                        $pesEmailBodyFilename = $intExt . "-" . $emailType . ".php";
                        break;
                    case 'unknown':
                        throw new \Exception('No email defined for ' . $country, 801);
                        break;
                    default:
                        // We don't need to further clarify the PES EMAIL Body file;
                        $pesEmailBodyFilename = $pesEmail;
                        break;
                }
                break;
            default : 
                throw new \Exception('$recheck is neither TRUE nor FALSE', 801);
                break;
        }
        
        $attachments = $recheck=='yes' ? $this->getRecheckAttachments($pesEmailBodyFilename) :  $this->getAttachments($intExt, $emailType);

        return array('filename'=> $pesEmailBodyFilename, 'attachments'=>$attachments, 'attachmentFileNames'=> $attachments,'emailType'=>$emailType,'splitResults'=>$results);
    }


    function sendPesEmail($firstName, $lastName, $emailAddress, $country, $openseat, $cnum,$recheck='no'){
            $emailDetails = $this->getEmailDetails($emailAddress, $country,null,$recheck);
            
            $emailBodyFileName = $emailDetails['filename'];
            $pesAttachments = isset($emailDetails['attachments']) ? $emailDetails['attachments'] : array();
            $replacements = array($firstName,$openseat);

            $pesEmailPattern =""; // It'll get set by the include_once, but this stops IDE flagging a warning.
            $pesEmail="";         // It'll get set by the include_once, but this stops IDE flagging a warning.
            
            include_once 'emailBodies/' . $emailBodyFileName;
            $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);

            $revalidation = $recheck=='yes' ? " - REVALIDATION " : "";
            
            
            $sendResponse = BlueMail::send_mail(array($emailAddress), "NEW URGENT - Pre Employment Screening $revalidation - $cnum : $firstName, $lastName", $emailBody,'LBGVETPR@uk.ibm.com',array(),array(),false,$pesAttachments);
            return $sendResponse;

    }

    function sendPesEmailChaser($cnum, $emailAddress, $chaserLevel, $flm){

        $pesEmailPattern = array(); // Will be overridden when we include_once from emailBodies later.
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.
        $names = personTable::getNamesFromCnum($cnum);
        $firstName = $names['FIRST_NAME'];
        $lastName = $names['LAST_NAME'];

        $emailBodyFileName = 'chaser' . trim($chaserLevel) . ".php";
        $replacements = array($firstName);

        include_once 'emailBodies/' . $emailBodyFileName;
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);

        $sendResponse = BlueMail::send_mail(array($emailAddress), "Reminder- Pre Employment Screening - $cnum : $firstName, $lastName", $emailBody,'LBGVETPR@uk.ibm.com',array($flm));
        return $sendResponse;


    }

    function sendPesProcessStatusChangedConfirmation($cnum, $firstName, $lastName, $emailAddress, $processStatus, $flm=null){

        $pesEmailPattern = array(); // Will be overridden when we include_once from emailBodies later.
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $emailBodyFileName = 'processStatus' . trim($processStatus) . ".php";
        $replacements = array($firstName);

        include_once 'emailBodies/' . $emailBodyFileName;
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);

        $sendResponse = BlueMail::send_mail(array($emailAddress), "Status Change - Pre Employment Screening - $cnum : $firstName, $lastName", $emailBody,'LBGVETPR@uk.ibm.com', array($flm));
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
        $allRevalidation = $loader->loadIndexed('REVALIDATION_STATUS','CNUM',allTables::$PERSON,$cnumPredicate);

        include_once 'emailBodies/leaversForPes.php';
        $pesEmail.= "<h3>The following people have been identified as having left IBM</h3>";
        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th><th style='padding:25px;'>Revalidation Status</th></tr></thead><tbody>";

        foreach ($leavers as $serial) {
            $pesStatus = isset($allPesStatus[$serial]) ? $allPesStatus[$serial] : 'unknown';
            $notesId   = isset($allNotesid[$serial]) ? $allNotesid[$serial] : 'unknown';
            $revalidation   = isset($allRevalidation[$serial]) ? $allRevalidation[$serial] : 'unknown';
            $pesEmail.="<tr><td style='padding:15px;'>" . $serial . "</td><td style='padding:15px;'>" . $notesId  . "</td><td style='padding:15px;'>" . $pesStatus . "</td><td style='padding:15px;'>" . $revalidation . "</td></tr>";
        }

        $pesEmail.="</tbody></table>";
        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";

        return BlueMail::send_mail(personRecord::$pesTaskId, "vBAC Leavers", $pesEmail,'LBGVETPR@uk.ibm.com');
    }

    static function notifyPesTeamOfOffboarding($cnum, $revalidationStatusWas, $notesId){
        $loader = new Loader();
        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $cnumPredicate = " CNUM = '" . trim($cnum) . "' ";
        $allPesStatus = $loader->loadIndexed('PES_STATUS','CNUM',allTables::$PERSON,$cnumPredicate);

        $pesEmail.= '<h2>The following person has begun the Offboarding process in vBAC<h2>';
        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th><th style='padding:25px;'>Revalidation Status Was</th></tr></thead><tbody>";

        $pesStatus = isset($allPesStatus[$cnum]) ? $allPesStatus[$cnum] : 'unknown';

        $pesEmail.="<tr><td style='padding:15px;'>" . $cnum . "</td><td style='padding:15px;'>" . $notesId  . "</td><td style='padding:15px;'>" . $pesStatus . "</td><td style='padding:15px;'>" . $revalidationStatusWas . "</td></tr>";

        $pesEmail.="</tbody></table>";
        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";

        return BlueMail::send_mail(array('LBGVETPR@uk.ibm.com'), "vbac Offboarding - $cnum : $notesId (Reval:$revalidationStatusWas)", $pesEmail,'LBGVETPR@uk.ibm.com');
    }

    static function notifyPesTeamOfOffboarded($cnum,$revalidationStatus){
        $loader = new Loader();
        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $cnumPredicate = " CNUM = '" . trim($cnum) . "' ";
        $allPesStatus = $loader->loadIndexed('PES_STATUS','CNUM',allTables::$PERSON,$cnumPredicate);
        $allNotesid = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$cnumPredicate);

        $pesEmail.= '<h2>The following person is now Offboarded in vBAC<h2>';
        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th><th style='padding:25px;'>Revalidation Status was</th></tr></thead><tbody>";

        $pesStatus = isset($allPesStatus[$cnum]) ? $allPesStatus[$cnum] : 'unknown';
        $notesId   = isset($allNotesid[$cnum]) ? $allNotesid[$cnum] : 'unknown';
        $pesEmail.="<tr><td style='padding:15px;'>" . $cnum . "</td><td style='padding:15px;'>" . $notesId  . "</td><td style='padding:15px;'>" . $pesStatus . "</td><td style='padding:15px;'>" . $revalidationStatus . "</td></tr>";

        $pesEmail.="</tbody></table>";
        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";

        return BlueMail::send_mail(array('LBGVETPR@uk.ibm.com'), "vbac Offboarded - $cnum : $notesId", $pesEmail,'LBGVETPR@uk.ibm.com');
    }

    static function notifyPesTeamOfOffStopRequest($cnum,$requestor=null){

        $requestor = empty($requestor) ? $_SESSION['ssoEmail'] : $requestor;

        $loader = new Loader();
        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $cnumPredicate = " CNUM = '" . trim($cnum) . "' ";
        $allPesStatus = $loader->loadIndexed('PES_STATUS','CNUM',allTables::$PERSON,$cnumPredicate);
        $allNotesid = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$cnumPredicate);

        $pesEmail.= '<h2>A request to STOP PES checking the following individual has been raised.<h2>';
        $pesEmail.= "<h4>Requested by : " . $requestor . "</h4>";
        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th></tr></thead><tbody>";

        $pesStatus = isset($allPesStatus[$cnum]) ? $allPesStatus[$cnum] : 'unknown';
        $notesId   = isset($allNotesid[$cnum]) ? $allNotesid[$cnum] : 'unknown';
        $pesEmail.="<tr><td style='padding:15px;'>" . $cnum . "</td><td style='padding:15px;'>" . $notesId  . "</td><td style='padding:15px;'>" . $pesStatus . "</td></tr>";

        $pesEmail.="</tbody></table>";
        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";

        return BlueMail::send_mail(array('LBGVETPR@uk.ibm.com'), "vbac Stop Requested - $cnum : $notesId", $pesEmail,'LBGVETPR@uk.ibm.com');
    }



}
