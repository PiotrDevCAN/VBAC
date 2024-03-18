<?php
namespace vbac\emails;

use itdq\BlueMail;
use itdq\Loader;
use vbac\allTables;
use vbac\interfaces\notificationEmail;
use vbac\personRecord;
use vbac\personTable;

class pesRequestEmail implements notificationEmail {

    public static $vbacNoReplyId = 'UKI.Business.Intelligence@kyndryl.com';

    private static $pesEmailBody = 'Please initiate PES check for the following individual : Name : &&name&&, Email Address : &&email&&, Worker Id : &&workerid&&, Notes Id : &&notesid&&, Country working in : &&country&&, LoB : &&lob&&, Role on Project : &&role&&, Contract : &&contract&&, Open Seat : &&openSeat&&, Requested By : &&requestor&&, Requested Timestamp : &&requested&&, Functional Mgr (on CC) : &&functionalMgr&&, PES Level : &&level&&';
    
    private static $pesEmailPatterns = array(
        '/&&name&&/',
        '/&&email&&/',
        '/&&notesid&&/',
        '/&&workerid&&/',
        '/&&country&&/',
        '/&&lob&&/',
        '/&&role&&/',
        '/&&contract&&/',
        '/&&openSeat&&/',
        '/&&requestor&&/',
        '/&&requested&&/',
        '/&&functionalMgr&&/',
        '/&&level&&/',
    );

    function send(personRecord $person){
        
        $loader = new Loader();

        $cnum = $person->getValue('CNUM');
        $workerId = $person->getValue('WORKER_ID');
        $fmCNUM = $person->getValue('FM_CNUM');
        if(!empty($fmCNUM)){
            $fmEmailArray = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . htmlspecialchars(trim($fmCNUM)) . "' ");
            $fmEmail = isset($fmEmailArray[trim($fmCNUM)]) ? $fmEmailArray[trim($fmCNUM)] : $fmCNUM;
        } else {
            $fmEmail = 'Unknown';
        }
        $firstName = !empty($person->getValue('FIRST_NAME')) ? $person->getValue('FIRST_NAME') : "firstName";
        $lastName  = !empty($person->getValue('LAST_NAME')) ? $person->getValue('LAST_NAME') : "lastName";
        $emailAddress = !empty($person->getValue('EMAIL_ADDRESS')) ? $person->getValue('EMAIL_ADDRESS') : "emailAddress";
        $notesId = !empty($person->getValue('NOTES_ID')) ? $person->getValue('NOTES_ID') : "notesId";
        $country = !empty($person->getValue('COUNTRY')) ? $person->getValue('COUNTRY') : "country";
        $openSeat = !empty($person->getValue('OPEN_SEAT_NUMBER')) ? $person->getValue('OPEN_SEAT_NUMBER') : "open seat/hiring";
        $lob = !empty($person->getValue('LOB')) ? $person->getValue('LOB') : "lob";
        $role = !empty($person->getValue('ROLE_ON_THE_ACCOUNT')) ? $person->getValue('ROLE_ON_THE_ACCOUNT') : "role";
        $level = !empty($person->getValue('PES_LEVEL')) ? $person->getValue('PES_LEVEL') : personTable::PES_LEVEL_TWO;
        $ctbRtb = !empty($person->getValue('CTB_RTB')) ? trim($person->getValue('CTB_RTB')) : null;
        $pesTaskId = personRecord::getPesTaskIdByCIO($ctbRtb);

        $now = new \DateTime();
        $replacements = array(
            $firstName . " " . $lastName,
            $emailAddress,
            $notesId,
            $workerId,
            $country,
            $lob,
            $role,
            'Ventus',
            $openSeat,
            $_SESSION['ssoEmail'],
            $now->format('Y-m-d H:i:s'),
            $fmEmail,
            $level
        );
        $to = array($pesTaskId);
        $title = 'vBAC PES Request - ' . $cnum ." / " . $workerId . " (" . trim($firstName) . " " . trim($lastName) . ")";
        $message = preg_replace(self::$pesEmailPatterns, $replacements, self::$pesEmailBody);
        
        $sendResponse = BlueMail::send_mail($to, $title, $message, self::$vbacNoReplyId);
        return $sendResponse;
    }
}