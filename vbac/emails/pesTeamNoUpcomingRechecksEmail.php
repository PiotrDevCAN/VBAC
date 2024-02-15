<?php
namespace vbac\emails;

use itdq\BlueMail;
use vbac\interfaces\notificationEmail;
use vbac\personRecord;

class pesTeamNoUpcomingRechecksEmail implements notificationEmail {
    
    function send(personRecord $person){

        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $pesTaskId = personRecord::getPesTaskId();

        include_once 'emailBodies/recheckReport.php';

        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<p>No upcoming rechecks have been found</p>";
        $emailBody = $pesEmail;

        $sendResponse = BlueMail::send_mail(array($pesTaskId), "Upcoming Rechecks-None", $emailBody, $pesTaskId);
        return $sendResponse;
    }
}