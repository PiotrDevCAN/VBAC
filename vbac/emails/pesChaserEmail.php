<?php
namespace vbac\emails;

use itdq\BlueMail;
use vbac\interfaces\notificationEmail;
use vbac\personRecord;
use vbac\personTable;
use vbac\pesEmail;

class pesChaserEmail implements notificationEmail {

    function send(personRecord $person, $chaserLevel = null, $flm = null){

        $cnum = $person->getValue('CNUM');
        $workerId = $person->getValue('WORKER_ID');
        $emailAddress = $person->getValue('EMAIL_ADDRESS');

        $pesEmailPattern = array(); // Will be overridden when we include_once from emailBodies later.
        // $pesEmail = null;        // Will be overridden when we include_once from emailBodies later.
        $pesEmail = '';             // Will be overridden when we include_once from emailBodies later.
        $names = personTable::getNamesFromCnum($cnum);
        list('FIRST_NAME' => $firstName, 'LAST_NAME' => $lastName) = $names;
        
        $pesTaskId = personRecord::getPesTaskId();
        
        $emailBodyFileName = 'chaser' . trim($chaserLevel) . ".php";
        $replacements = array($firstName, $pesTaskId);
        
        include_once 'emailBodies/' . $emailBodyFileName;
        $emailBody = preg_replace($pesEmailPattern, $replacements, $pesEmail);
        
        $sendResponse = BlueMail::send_mail(array($emailAddress), "Reminder- Pre Employment Screening - $cnum / $workerId : $firstName, $lastName", $emailBody, $pesTaskId, array($flm),array(),true, array(),pesEmail::EMAIL_PES_SUPRESSABLE);
        return $sendResponse;
    }
}