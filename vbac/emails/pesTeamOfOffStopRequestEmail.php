<?php
namespace vbac\emails;

use itdq\BlueMail;
use itdq\Loader;
use vbac\allTables;
use vbac\interfaces\notificationEmail;
use vbac\personRecord;

class pesTeamOfOffStopRequestEmail implements notificationEmail {
    
    function send(personRecord $person, $requestor = null){
        
        $cnum = $person->getValue('CNUM');
        $workerId = $person->getValue('WORKER_ID');

        $requestor = empty($requestor) ? $_SESSION['ssoEmail'] : $requestor;

        $loader = new Loader();
        $now = new \DateTime();
        $pesEmail = null;          // Will be overridden when we include_once from emailBodies later.

        $pesTaskId = personRecord::getPesTaskId();

        $cnumPredicate = " CNUM = '" . trim($cnum) . "' AND WORKER_ID = '" . trim($workerId) . "' ";
        $allPesStatus = $loader->loadIndexed('PES_STATUS','CNUM',allTables::$PERSON,$cnumPredicate);
        $allNotesid = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON,$cnumPredicate);

        $pesEmail.= '<h2>A request to STOP PES checking the following individual has been raised.<h2>';
        $pesEmail.= "<h4>Requested by : " . $requestor . "</h4>";
        $pesEmail.= "<h4>Generated by vBac: " . $now->format('jS M Y') . "</h4>";
        $pesEmail.= "<table border='1' style='border-collapse:collapse;'  ><thead style='background-color: #cce6ff; padding:25px;'><tr><th style='padding:25px;'>CNUM</th><th style='padding:25px;'>WORKER ID</th><th style='padding:25px;'>Notes ID</th><th style='padding:25px;'>PES Status</th></tr></thead><tbody>";

        $pesStatus = isset($allPesStatus[$cnum]) ? $allPesStatus[$cnum] : 'unknown';
        $notesId   = isset($allNotesid[$cnum]) ? $allNotesid[$cnum] : 'unknown';
        $pesEmail.="<tr><td style='padding:15px;'>" . $cnum . "</td><td style='padding:15px;'>" . $workerId . "</td><td style='padding:15px;'>" . $notesId  . "</td><td style='padding:15px;'>" . $pesStatus . "</td></tr>";

        $pesEmail.="</tbody></table>";
        $pesEmail.= "<style> th { background:red; padding:15px; } </style>";

        return BlueMail::send_mail(array($pesTaskId), "vbac Stop Requested - $cnum / $workerId : $notesId", $pesEmail, $pesTaskId);
    }
}