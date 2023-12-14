<?php
namespace vbac\emails;

use itdq\BlueMail;
use vbac\personRecord;
use vbac\personTable;
use vbac\pesEmail;

class informPmoOfPesStatusChangeEmail {
    
    private static $preboarderStatusChangeEmailBody = '<table width="100%" border="0"   cellpadding="0">
      <tr><td align="center">
        <table width="50%">
            <tr><td colspan="2" style="font-size:16px;padding-bottom:10px"">Please Note the <b>PES STATUS</b> for a <b>Pre-Boarder</b> has changed :</td></tr>
            <tr><th style="background-color:silver;font-size:16px">Email Address</th><td style="font-size:20px">&&email&&</td></tr>
            <tr><th style="background-color:silver;font-size:16px">Notes Id</th><td style="font-size:20px">&&notesid&&</td></tr>
            <tr><th style="background-color:SkyBlue;font-size:18px">Status Is</th><td style="font-size:18px">&&StatusIs&&</td></tr>
            <tr><th style="background-color:WhiteSmoke;font-size:16px">Changed By</th><td style="font-size:16px">&&changeor&&</td></tr>
            <tr><th style="background-color:WhiteSmoke;font-size:16px">Changed Date</th><td style="font-size:16px">&&changed&&</td></tr>
        </table>
    </td></tr>
    </table>';

    private static $preboarderStatusChangeEmailPattern = array(
      '/&&email&&/',
      '/&&notesid&&/',
      '/&&StatusIs&&/',
      '/&&changeor&&/',
      '/&&changed&&/'
    );

    function informPmoOfPesStatusChange(personRecord $person, $newPesStatus, $ctbRtb){

      $pesTaskId = personRecord::getPesTaskIdByCIO($ctbRtb);
      $to = array($pesTaskId);
      $cc = array();
      $bcc = array();
      $title = 'Pre-Boarder PES Status Change';
      $noAttachments = array();
      
      $emailAddress = !empty($person->getValue('EMAIL_ADDRESS')) ? $person->getValue('EMAIL_ADDRESS') : "emailAddress  missing from vBAC";
      $notesId = !empty($person->getValue('NOTES_ID')) ? $person->getValue('NOTES_ID') : "NotesId  missing from vBAC";

      $now = new \DateTime();
      $replacements = array($emailAddress,$notesId,$newPesStatus,$_SESSION['ssoEmail'],$now->format('d-m-Y'));
      $emailMessage = preg_replace(self::$preboarderStatusChangeEmailPattern, $replacements, self::$preboarderStatusChangeEmailBody);

      set_time_limit(60);
      $responseData = BlueMail::send_mail($to, $title, $emailMessage, personRecord::$vbacNoReplyId, $cc, $bcc, true, $noAttachments, pesEmail::EMAIL_NOT_PES_SUPRESSABLE );
      list(
        'sendResponse' => $response, 
        'Status' => $status
      ) = $responseData;
   }
}