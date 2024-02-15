<?php
namespace vbac\emails;

use itdq\BlueMail;
use vbac\interfaces\notificationEmail;
use vbac\personRecord;
use vbac\pesEmail;

class pesStatusChangedConfirmationEmail implements notificationEmail {

    function send(personRecord $person, $processStatus = null, $flm = null){

        $cnum = $person->getValue('CNUM');
        $workerId = $person->getValue('WORKER_ID');
        $firstName = $person->getValue('FIRST_NAME');
        $lastName = $person->getValue('LAST_NAME');
        $emailAddress = $person->getValue('EMAIL_ADDRESS');

        $pesEmailPattern = array(); // Will be overridden when we include_once from emailBodies later.
        // $pesEmail = null;        // Will be overridden when we include_once from emailBodies later.
        $pesEmail = '';             // Will be overridden when we include_once from emailBodies later.
        
        $pesTaskId = personRecord::getPesTaskId();
        
        $emailBodyFileName = 'processStatus' . trim($processStatus) . ".php";
        $replacements = array($firstName, $pesTaskId);
        
        include_once 'emailBodies/' . $emailBodyFileName;
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        
        $flmArray = empty($flm) ? array() : array($flm);
        
        $sendResponse = BlueMail::send_mail(array($emailAddress), "Status Change - Pre Employment Screening - $cnum / $workerId : $firstName, $lastName", $emailBody, $pesTaskId, $flmArray, array(), true,  array(), pesEmail::EMAIL_PES_SUPRESSABLE);
        return $sendResponse;
    }
}

