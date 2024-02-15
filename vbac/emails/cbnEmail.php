<?php
namespace vbac\emails;

use itdq\BlueMail;
use vbac\allTables;
use vbac\interfaces\notificationEmail;
use vbac\personRecord;
use vbac\personTable;

class cbnEmail implements notificationEmail {

    private static $cbnEmailBody = "You are recorded in the <a href='&&host&&'>vBAC</a> tool, as a Functional Manager for one or more people.<h3>Please review the people assigned to you for continued business need and/or to correct any inaccuracies. <a href='&&host&&/pa_pmo.php'>Link here</a></h3>"
      . "<p>Select the <b><em>Mgrs CBN Report</em></b> and use the <b><em>Hide Offboarded/ing</em></b> option, both are buttons on the Person Portal page.</p>"
      . "<ul><li>If your reportee has moved to a new functional manager or changed roles, you can amend their details using the <b>Edit Icon</b> in the <em>Notes ID</em> column to do this. All mandatory information must be completed to save the person record. </li>"
      . "<li>If you have people who no longer work on the account  please initiate offboarding by amending their <b>Projected End Date</b>.  Use the <b>Edit Icon</b> in the <em>Notes ID</em> column to do this</li>"
      . "<li> If you are missing people who should report to you<br/>Ensure they have been boarded to the account using the vBAC <a href='&&host&&/pa_personFinder.php'>People Finder</a> screen<br/>You can transfer someone to yourself from another manager by clicking the <b>Transfer Icon</b> in the <em>FM Column</em></li>"
      . "<li>If the person needs to be boarded, then please use the <a href='&&host&&/pb_onboard.php'>Boarding</a> screen</li></ul>";

    private static $cbnEmailPattern = array('/&&host&&/');

    function send(personRecord $person){
        
        $sendResponse = false;
        $personTable = new personTable(allTables::$PERSON);
        
        $allFm = $personTable->activeFmEmailAddressesByCNUM();
        $emailableFmLists = array_chunk($allFm, 49);
        $replacements = array("https://" . $_SERVER['HTTP_HOST']);
        $to = personRecord::$pmoTaskId;
        $title = 'CBN Initiation Request';
        $emailMessage = preg_replace(self::$cbnEmailPattern, $replacements, self::$cbnEmailBody);
        foreach ($emailableFmLists as $groupOfFmEmail){
            $cc = array();
            $bcc = $groupOfFmEmail;
            $bcc[] = personRecord::$smCdiAuditEmail;  // Always copy the slack channel.
            set_time_limit(60);
            $sendResponse = BlueMail::send_mail($to, $title, $emailMessage, personRecord::$vbacNoReplyId, $cc, $bcc);
        }
        return $sendResponse;
    }
}