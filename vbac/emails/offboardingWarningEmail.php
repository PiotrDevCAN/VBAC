<?php
namespace vbac\emails;

use itdq\BlueMail;
use itdq\Loader;
use vbac\allTables;
use vbac\personRecord;

class offboardingWarningEmail {

    private static $warnPmoDateChange = 'Please consider OFFBOARDING the following individual:
      Name : &&name&&
      Serial: &&cnum&&
      Email Address : &&email&&
      Notes Id : &&notesid&&

      Projected End Date : &&projectedEndDate&&

      Country working in : &&country&&
      LoB : &&lob&&
      Employee Type:&&type&&
      Functional Mgr: &&functionalMgr&&'
    ;

    private static $warnPmoDateChangePattern = array(
      '/&&name&&/',
      '/&&cnum&&/',
      '/&&email&&/',
      '/&&notesid&&/',
      '/&&projectedEndDate&&/',
      '/&&country&&/',
      '/&&lob&&/',
      '/&&type&&/',
      '/&&functionalMgr&&/',
    );

    function sendOffboardingWarning(personRecord $person){

        $loader = new Loader();

        $cnum  = !empty($person->getValue('CNUM')) ? $person->getValue('CNUM') : "serial  missing from vBAC";
        $fmCNUM = $person->getValue('FM_CNUM');
        if(!empty($fmCNUM)){
            $fmEmailArray = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . htmlspecialchars(trim($fmCNUM)) . "' ");
            $fmEmail = isset($fmEmailArray[trim($fmCNUM)]) ? $fmEmailArray[trim($fmCNUM)] : $fmCNUM;
        } else {
            $fmEmail = 'Unknown';
        }
        $firstName = !empty($person->getValue('FIRST_NAME')) ? $person->getValue('FIRST_NAME') : "firstName";
        $lastName  = !empty($person->getValue('LAST_NAME')) ? $person->getValue('LAST_NAME') : "lastName";        
        $emailAddress = !empty($person->getValue('EMAIL_ADDRESS')) ? $person->getValue('EMAIL_ADDRESS') : "emailAddress  missing from vBAC";
        $notesId = !empty($person->getValue('NOTES_ID')) ? $person->getValue('NOTES_ID') : "notesId  missing from vBAC";
        $country = !empty($person->getValue('COUNTRY')) ? $person->getValue('COUNTRY') : "county  missing from vBAC";
        $lob     = !empty($person->getValue('LOB')) ? $person->getValue('LOB') : "lob  missing from vBAC";
        $type    = !empty($person->getValue('EMPLOYEE_TYPE')) ? $person->getValue('EMPLOYEE_TYPE') : "employee type missing from vBAC";
        $projectedEndDate = !empty($person->getValue('PROJECTED_END_DATE')) ? $person->getValue('PROJECTED_END_DATE') : "projected_end_date";

        $replacements = array($firstName . " " . $lastName,
          $cnum,
          $emailAddress,
          $notesId,
          $projectedEndDate,
          $country,
          $lob,
          $type,
          $fmEmail
        );
        $to = personRecord::$pmoTaskId;
        $title = 'vBAC Projected End Date Change - ' . $cnum ." (" . trim($firstName) . " " . trim($lastName) . ")";
        $message = preg_replace(self::$warnPmoDateChangePattern, $replacements, self::$warnPmoDateChange);

        $sendResponse = BlueMail::send_mail($to, $title, $message, personRecord::$vbacNoReplyId);
        return $sendResponse;
    }
}