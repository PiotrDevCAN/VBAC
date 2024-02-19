<?php
namespace vbac\emails;

use itdq\AuditTable;
use itdq\BlueMail;
use itdq\Loader;
use vbac\allTables;
use vbac\interfaces\notificationEmail;
use vbac\personRecord;

class pesStatusChangeEmail implements notificationEmail {

  private static $pesClearedPersonalEmail = 'Hello &&candidate&&,
    <br/>I can confirm that you have successfully passed Lloyds Bank PES Screening, with a personal reference, effective from &&effectiveDate&&
    <br/>If you need any more information regarding your PES clearance, please contact the taskid &&taskid&&.
    <br/>You are now required to successfully complete the mandatory Aurora Security Education.
    <br/>Undertake the training via the following link: <a href="'.personRecord::AURORA_TRAINING_URL.'">'.personRecord::AURORA_TRAINING_URL.'</a>
    <br/>Please note that your PES clearance will require revalidation after 1 or 3 years (depending on your access levels), you will be contacted 8 weeks before your revalidation date with instructions.
    <br/>Many Thanks for your cooperation';

  private static $pesClearedPersonalEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');

  private static $pesClearedEmail = 'Hello &&candidate&&,
    <br/>I can confirm that you have successfully passed Lloyds Bank PES Screening, effective from &&effectiveDate&&
    <br/>If you need any more information regarding your PES clearance, please contact the taskid &&taskid&&.

    <br/>If this is the first time you have been PES Cleared for Lloyds Bank, you are required to successfully complete the mandatory Aurora Security Education.
    <br/>If you have previously successfully completed the Aurora Security Education, then this requirement does not apply.
    <br/>Undertake the training via the following link: <a href="'.personRecord::AURORA_TRAINING_URL.'">'.personRecord::AURORA_TRAINING_URL.'</a>
    <br/>Please note that your PES clearance will require revalidation after 1 or 3 years (depending on your access levels), you will be contacted 8 weeks before your revalidation date with instructions.
    <br/>Many Thanks for your cooperation,';

  private static $pesClearedEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');

  private static $pesClearedAmberEmail = 'Hello &&candidate&&,
    <br/>I can confirm that you have successfully passed Lloyds Bank PES Screening, effective from &&effectiveDate&&
    <br/>If you need any more information regarding your PES clearance, please contact the taskid &&taskid&&.

    <br/>If this is the first time you have been PES Cleared for Lloyds Bank, you are required to successfully complete the mandatory Aurora Security Education.
    <br/>If you have previously successfully completed the Aurora Security Education, then this requirement does not apply.
    <br/>Undertake the training via the following link: <a href="'.personRecord::AURORA_TRAINING_URL.'">'.personRecord::AURORA_TRAINING_URL.'</a>
    <br/>Please note that your PES clearance will require revalidation after 1 or 3 years (depending on your access levels), you will be contacted 8 weeks before your revalidation date with instructions.
    <br/>Many Thanks for your cooperation,';

  private static $pesClearedAmberEmailPattern = array('/&&candidate&&/','/&&effectiveDate&&/','/&&taskid&&/');

  private static $pesCancelPesEmail = 'PES Team,
    <br/>Please stop processing the PES Clearance for : &&candidateFirstName&& <b>&&candidateSurname&&</b> CNUM:( &&cnum&& )
    <br/>This action has been requested by  &&requestor&&.';

  private static $pesCancelPesEmailPattern = array('/&&candidateFirstName&&/','/&&candidateSurname&&/','/&&cnum&&/','/&&requestor&&/');

  private static $pesRestartPesEmail = 'PES Team,
    <br/>Please <b>restart</b> processing the PES Clearance for : &&candidateFirstName&& <b>&&candidateSurname&&</b> CNUM:( &&cnum&& )
    <br/>This action has been requested by  &&requestor&&.';

  private static $pesRestartPesEmailPattern = array('/&&candidateFirstName&&/','/&&candidateSurname&&/','/&&cnum&&/','/&&requestor&&/');

  private static $pesClearedProvisionalEmail = '<p>Hello &&candidate&&,</p>
    <p>Due to the recent situation we understand that many people will be unable to meet with fellow IBM\'ers to have their documents Certified.  We have implemented a \'provisional clearance\' process and will be accepting all documents without certification - however these documents will require to be certified as soon as the restrictions are lifted.</p>
    <p>Therefore I can confirm that you have provisionally passed  Lloyds Bank PES Screening.</p>
    <p>Please note that this will not give you full PES clearance, and your account may not recognise Provisional Clearance, therefore, if you can get your documents certified correctly (as per below) please do so.</p>
    <p>When sending your document please only send to the PES team.</p>
    <p><b>The Certification MUST be done by another IBM�er</b>, to confirm that they have seen the original document. The following statement should be handwritten on <b>each document</b>, on the <b>same side as the image</b>.</p>
    <p style=\'text-align:center;color:red\'>True & Certified Copy<br/>Name of certifier  in BLOCK CAPITALS<br/>IBM Serial number of certifier<br/>Certification Date<br/>Signature of certifier</p>
    <p>If you need any more information regarding your PES clearance, please let me know.</p>
    <p>Many Thanks for your cooperation,</p>';

  private static $pesClearedProvisionalEmailPattern = array('/&&candidate&&/');
  
  function send(personRecord $person, $newPesStatus = null, $isPesSuppressable = true){
    
    $loader = new Loader();

    $cnum = $person->getValue('CNUM');
    $fmIsIbmEmail = false;
    $fmCNUM = $person->getValue('FM_CNUM');
    if(!empty($fmCNUM)){
        $fmEmailArray = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . htmlspecialchars(trim($fmCNUM)) . "' ");
        $fmEmail = isset($fmEmailArray[trim($fmCNUM)]) ? $fmEmailArray[trim($fmCNUM)] : $fmCNUM;
        $fmIsIbmEmail = strtolower(substr($fmEmail,-7))=='ibm.com';
        $fmIsKyndrylEmail = strtolower(substr($fmEmail,-11))=='kyndryl.com';
      } else {
        $fmEmail = 'Unknown';
        $fmIsIbmEmail = false;
        $fmIsKyndrylEmail = false;
      }
    $fmEmail = $fmIsIbmEmail ? $fmEmail : null;
    $firstName = !empty($person->getValue('FIRST_NAME')) ? $person->getValue('FIRST_NAME') : "firstName";
    $lastName  = !empty($person->getValue('LAST_NAME')) ? $person->getValue('LAST_NAME') : "lastName";
    $emailAddress = !empty($person->getValue('EMAIL_ADDRESS')) ? $person->getValue('EMAIL_ADDRESS') : "emailAddress";
    $isIbmEmail = strtolower(substr($emailAddress,-7))=='ibm.com';
    $isKyndrylEmail = strtolower(substr($emailAddress,-11))=='kyndryl.com';
    $pesStatus = !empty($newPesStatus) ? $newPesStatus : "pesStatus";
    $pesDateResponded = !empty($person->getValue('PES_DATE_RESPONDED')) ? $person->getValue('PES_DATE_RESPONDED') : "pesDateResponded";
    $pesDateCleared = !empty($person->getValue('PES_CLEARED_DATE')) ? $person->getValue('PES_CLEARED_DATE') : "pesDateCleared";
    $ctbRtb = !empty($person->getValue('CTB_RTB')) ? trim($person->getValue('CTB_RTB')) : null;
    $pesTaskId = personRecord::getPesTaskIdByCIO($ctbRtb);
    if(
      !$isIbmEmail 
      && !$isKyndrylEmail 
      && !$fmIsIbmEmail 
      && !$fmIsKyndrylEmail)
    {
      throw new \Exception('No IBM/Kyndryl Email Address for individual or Functional Manager');
    }
    $to = array();
    $cc = array();
    $bcc = array();
    $noAttachments = array();

    switch ($pesStatus) {
        case personRecord::PES_STATUS_CLEARED_PERSONAL:
            $pattern   = self::$pesClearedPersonalEmailPattern;
            $emailBody = self::$pesClearedPersonalEmail;
            $replacements = array(
              $firstName, 
              $pesDateResponded, 
              $pesTaskId
            );
            $title = 'vBAC PES Status Change';
            !empty($emailAddress) ? $to[] = $emailAddress : null;
            !empty($fmEmail)      ? $to[] = $fmEmail : null;
            break;
        case personRecord::PES_STATUS_CLEARED_AMBER:
            $pattern   = self::$pesClearedAmberEmailPattern;
            $emailBody = self::$pesClearedAmberEmail;
            $replacements = array(
              $firstName, 
              $pesDateCleared,
              $pesTaskId
            );
            $title = 'vBAC PES Status Change';
            !empty($emailAddress) ? $to[] = $emailAddress : null;
            !empty($fmEmail)      ? $to[] = $fmEmail : null;
            break;
        case personRecord::PES_STATUS_CLEARED:
            $pattern   = self::$pesClearedEmailPattern;
            $emailBody = self::$pesClearedEmail;
            $replacements = array(
              $firstName, 
              $pesDateCleared,
              $pesTaskId
            );
            $title = 'vBAC PES Status Change';
            !empty($emailAddress) ? $to[] = $emailAddress : null;
            !empty($fmEmail)      ? $to[] = $fmEmail : null;
            break;
        case personRecord::PES_STATUS_PROVISIONAL: // For Covid
            $pattern   = self::$pesClearedProvisionalEmailPattern;
            $emailBody = self::$pesClearedProvisionalEmail;
            $replacements = array(
              $firstName
            );
            $title = 'vBAC PES Status Change';
            !empty($emailAddress) ? $to[] = $emailAddress : null;
            !empty($fmEmail)      ? $to[] = $fmEmail : null;
            break;
        case personRecord::PES_STATUS_CANCEL_REQ:
            $pattern   = self::$pesCancelPesEmailPattern;
            $emailBody = self::$pesCancelPesEmail;
            $title = 'vBAC Cancel Request';
            $replacements = array(
              $firstName,
              $lastName,
              $cnum,
              $_SESSION['ssoEmail']
            );
            foreach (personRecord::$pesKyndrylTaskIds as $key => $taskId){
              $to[] = $taskId;
            }
            !empty($fmEmail) ? $cc[] = $fmEmail : null;
            $cc[] = $_SESSION['ssoEmail'];
            break;
        case personRecord::PES_STATUS_RESTART:
            $pattern   = self::$pesRestartPesEmailPattern;
            $emailBody = self::$pesRestartPesEmail;
            $title = 'vBAC Restart Request';
            $replacements = array(
              $firstName,
              $lastName,
              $cnum,
              $_SESSION['ssoEmail']
            );
            foreach (personRecord::$pesKyndrylTaskIds as $key => $taskId){
              $to[] = $taskId;
            }
            !empty($fmEmail) ? $cc[] = $fmEmail : null;
            $cc[] = $_SESSION['ssoEmail'];
        default:
            $to[] = $_ENV['devemailid'];
            $title = 'vBAC Default Request';
            $pattern = array();
            $emailBody = 'Failing status: ' . $pesStatus;
            $replacements = array();
          break;
    }

    AuditTable::audit(print_r($pattern,true),AuditTable::RECORD_TYPE_DETAILS);
    AuditTable::audit(print_r($replacements,true),AuditTable::RECORD_TYPE_DETAILS);
    AuditTable::audit(print_r($emailBody,true),AuditTable::RECORD_TYPE_DETAILS);

    $message = preg_replace($pattern, $replacements, $emailBody);

    AuditTable::audit(print_r($message,true),AuditTable::RECORD_TYPE_DETAILS);
    
    $responseData = BlueMail::send_mail($to, $title ,$message, $pesTaskId, $cc, $bcc, true, $noAttachments, $isPesSuppressable);
    list(
        'sendResponse' => $response, 
        'Status' => $status
    ) = $responseData;

    return array(
      'response' => $response,
      'to' => $to,
      'message' => $message,
      'pesTaskId' => $pesTaskId
    );
  }
}