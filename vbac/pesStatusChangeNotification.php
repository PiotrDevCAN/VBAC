<?php
namespace vbac;

use vbac\emails\pmoOfPesStatusChangeEmail;
use vbac\emails\pesStatusChangeEmail;
use vbac\personRecord;
use vbac\pesEmail;

class pesStatusChangeNotification {

    const EMAIL_SENT = 'Email sent';
    const EMAIL_NOT_SENT = 'No email sent';

    const EMAIL_NOT_APPLICABLE = 'Email not applicable';
    const EMAIL_NOT_APPLICABLE_OTHER = 'Email not applicable(other)';
    const EMAIL_NOT_APPLICABLE_ERROR = 'Email not applicable(error)';

    function send(personRecord $person) {
        $pesStatusChangeEmail = new pesStatusChangeEmail();
        $emailResponseData = $pesStatusChangeEmail->send($person, pesEmail::EMAIL_NOT_PES_SUPRESSABLE);
        list(
            'response' => $emailResponse,
            'to' => $to,
            'message' => $message,
            'pesTaskId' => $pesTaskId
        ) = $emailResponseData;
        $notificationStatus = $emailResponse ? self::EMAIL_SENT : self::EMAIL_NOT_SENT;
        
        return $notificationStatus;
    }

    /* 
    * notification upon RESTART
    */
    function restart(personRecord $person, $status, $revalidationStatus = null) {
        switch ($status) {
            case personRecord::PES_STATUS_REMOVED:
            case personRecord::PES_STATUS_DECLINED:
            case personRecord::PES_STATUS_FAILED:
            case personRecord::PES_STATUS_INITIATED:
            case personRecord::PES_STATUS_REQUESTED:
            case personRecord::PES_STATUS_EXCEPTION:
            case personRecord::PES_STATUS_PROVISIONAL;
            case personRecord::PES_STATUS_RECHECK_REQ;
            case personRecord::PES_STATUS_RECHECK_PROGRESSING;
            case personRecord::PES_STATUS_MOVER;
            case personRecord::PES_STATUS_LEFT_IBM;
            case personRecord::PES_STATUS_REVOKED;
                $notificationStatus = self::EMAIL_NOT_APPLICABLE;
                break;
            case personRecord::PES_STATUS_CLEARED:
            case personRecord::PES_STATUS_CLEARED_PERSONAL:
            case personRecord::PES_STATUS_CLEARED_AMBER:
            case personRecord::PES_STATUS_CANCEL_REQ:
            case personRecord::PES_STATUS_RESTART:
                $notificationStatus = $this->send($person);
                break;
            default:
                $notificationStatus = self::EMAIL_NOT_APPLICABLE_OTHER;
            break;

            return $notificationStatus;
        }
    }

    /*
    * notification upon SAVE
    */
    function save(personRecord $person, $status, $revalidationStatus = null) {
        switch ($status) {
            case personRecord::PES_STATUS_REMOVED:
            case personRecord::PES_STATUS_DECLINED:
            case personRecord::PES_STATUS_FAILED:
            case personRecord::PES_STATUS_LEFT_IBM:
            case personRecord::PES_STATUS_REVOKED:
                $ctbRtb = !empty($personData['CTB_RTB']) ? trim($personData['CTB_RTB']) : null;
                if (endsWith($revalidationStatus, personRecord::REVALIDATED_PREBOARDER)) {
                    $informPmoOfPesStatusChange = new pmoOfPesStatusChangeEmail();
                    $informPmoOfPesStatusChange->send($person, $status, $ctbRtb);
                    $notificationStatus = 'Email sent to PMO. PES Status is: ' . $status;
                } else {
                    $notificationStatus = self::EMAIL_NOT_APPLICABLE;
                }
                break;
            case personRecord::PES_STATUS_INITIATED:                
            case personRecord::PES_STATUS_RECHECK_PROGRESSING:
            case personRecord::PES_STATUS_REQUESTED:
                $notificationStatus = self::EMAIL_NOT_APPLICABLE;
                break;
            case personRecord::PES_STATUS_CLEARED:
            case personRecord::PES_STATUS_CLEARED_PERSONAL:
            case personRecord::PES_STATUS_CLEARED_AMBER:
            case personRecord::PES_STATUS_CANCEL_REQ:
            case personRecord::PES_STATUS_PROVISIONAL: // For Covid
                $notificationStatus = $this->send($person);
                break;
            default:
                $notificationStatus = self::EMAIL_NOT_APPLICABLE_OTHER;
                break;

            return $notificationStatus;
        }
    }
}