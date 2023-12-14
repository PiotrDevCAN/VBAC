<?php
namespace vbac\emails;

use itdq\AuditTable;
use itdq\BlueMail;
use vbac\personRecord;

class cbcEmail {
    
    private static $cbcEmailBody = "<h3>The following person has been boarded with a location that may not have a CBC/DOU in place.</h3>"
      . "<table>"
      . "<tbody>"
      . "<tr><th>Notes Id</th><td>&&notesid&&</td></tr>"
      . "<tr><th>CNUM</th><td>&&cnum&&</td></tr>"
      . "<tr><th>Country</th><td>&&countryCode&&</td></tr>"
      . "<tr><th>LBG Location</th><td>&&lbgLocation&&</td></tr>"
      . "<tr><th>Role</th><td>&&role&&</td></tr>"
      . "<tbody>"
      . "<table>";

    private static $cbcEmailPattern = array('/&&notesid&&/','/&&cnum&&/','/&&workerId&&/','/&&countryCode&&/','/&&lbgLocation&&/','/&&role&&/');

    function sendCbcEmail(personRecord $person){

        $sendResponse = false;
        
        $cnum = $person->getValue('CNUM');
        $firstName = !empty($person->getValue('FIRST_NAME')) ? $person->getValue('FIRST_NAME') : "firstName";
        $lastName  = !empty($person->getValue('LAST_NAME')) ? $person->getValue('LAST_NAME') : "lastName";
        $workerId = !empty($person->getValue('EMAIL_ADDRESS')) ? $person->getValue('WORKER_ID') : "workerId";
        $notesId = !empty($person->getValue('NOTES_ID')) ? $person->getValue('NOTES_ID') : "notesId";
        $country = !empty($person->getValue('COUNTRY')) ? $person->getValue('COUNTRY') : "country";
        $LBGLocation = !empty($person->getValue('LBG_LOCATION')) ? $person->getValue('LBG_LOCATION') : "LBG location";
        $role = !empty($person->getValue('ROLE_ON_THE_ACCOUNT')) ? $person->getValue('ROLE_ON_THE_ACCOUNT') : "role";
        
        $to = personRecord::$pmoTaskId;
        $title = 'vBAC CBC Check Required -' . $cnum ." (" . trim($firstName) . " " . trim($lastName) . ")";
        $countryCode = strtoupper(substr(trim($cnum),-3));
        switch ($countryCode) {
            case 'XXX':
                // Not an IBM'er
                break;
            case '709' :
                // India
            case '744' :
                // India
            case '866' :
                // UK
                break;
            default:
                AuditTable::audit('CBC Check Required for ' . $notesId . " ($countryCode)", AuditTable::RECORD_TYPE_AUDIT);
                $replacements = array($notesId, $cnum, $workerId, $country, $LBGLocation,  $role );
                $message = preg_replace(self::$cbcEmailPattern, $replacements, self::$cbcEmailBody);
                $sendResponse = BlueMail::send_mail($to, $title, $message, personRecord::$vbacNoReplyId, personRecord::$securityOps);
            break;
        }
        return $sendResponse;
    }
}