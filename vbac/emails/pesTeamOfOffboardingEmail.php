<?php
namespace vbac\emails;

use itdq\BlueMail;
use itdq\Loader;
use vbac\allTables;
use vbac\interfaces\notificationEmail;
use vbac\personRecord;

class pesTeamOfOffboardingEmail implements notificationEmail {
    
    function send(personRecord $person, $revalidationStatusWas = null){

        $cnum = $person->getValue('CNUM');
        $workerId = $person->getValue('WORKER_ID');
        $notesId = $person->getValue('NOTES_ID');

        $loader = new Loader();
        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $pesTaskId = personRecord::getPesTaskId();

        $cnumPredicate = " CNUM = '" . trim($cnum) . "' AND WORKER_ID = '" . trim($workerId) . "' ";
        $allPesStatus = $loader->loadIndexed('PES_STATUS','CNUM',allTables::$PERSON,$cnumPredicate);

        $pesEmail.= '<h2>The following person has begun the Offboarding process in vBAC<h2>';
        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th><th style='padding:25px;'>Revalidation Status Was</th></tr></thead><tbody>";

        $pesStatus = isset($allPesStatus[$cnum]) ? $allPesStatus[$cnum] : 'unknown';

        $pesEmail.="<tr><td style='padding:15px;'>" . $cnum . "</td><td style='padding:15px;'>" . $notesId  . "</td><td style='padding:15px;'>" . $pesStatus . "</td><td style='padding:15px;'>" . $revalidationStatusWas . "</td></tr>";

        $pesEmail.="</tbody></table>";
        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";

        return BlueMail::send_mail(array($pesTaskId), "vbac Offboarding - $cnum / $workerId : $notesId (Reval:$revalidationStatusWas)", $pesEmail, $pesTaskId);
    }
}