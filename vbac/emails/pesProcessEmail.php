<?php
namespace vbac\emails;

use itdq\BlueMail;
use vbac\interfaces\notificationEmail;
use vbac\personRecord;
use vbac\pesEmail;

class pesProcessEmail implements notificationEmail {

    function send(personRecord $person, $recheck = 'no'){

        $cnum = $person->getValue('CNUM');
        $workerId = $person->getValue('WORKER_ID');
        $emailAddress = $person->getValue('EMAIL_ADDRESS');
        $country = $person->getValue('COUNTRY');
        $openSeat = $person->getValue('OPEN_SEAT');
        $firstName = $person->getValue('FIRST_NAME');
        $lastName = $person->getValue('LAST_NAME');

        $email = new pesEmail();
        $emailDetailsData = $email->getEmailDetails($person, $recheck, true);
        list('filename' => $filename, 'attachments' => $attachments, 'attachmentFileNames' => $attachmentFileNames) = $emailDetailsData;
        
        $emailBodyFileName = $filename;
        $pesAttachments = isset($attachments) ? $attachments : array();
        
        $pesTaskId = personRecord::getPesTaskId();
        $replacements = array($firstName, $openSeat, $pesTaskId);
        
        $pesEmailPattern =""; // It'll get set by the include_once, but this stops IDE flagging a warning.
        $pesEmail="";         // It'll get set by the include_once, but this stops IDE flagging a warning.
        
        include_once 'emailBodies/' . $emailBodyFileName;
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        
        $revalidation = $recheck=='yes' ? " - REVALIDATION " : "";
        
        $sendResponse = BlueMail::send_mail(array($emailAddress), "NEW URGENT - Pre Employment Screening $revalidation - $cnum / $workerId : $firstName, $lastName", $emailBody, $pesTaskId, array(), array(),false,$pesAttachments, pesEmail::EMAIL_PES_SUPRESSABLE);
        return $sendResponse;
    }
}