<?php
namespace itdq;

use itdq\AllItdqTables;

/**
 *
 * @author GB001399
 *
 */
class BlueMail
{

    function requestPesChecking(){

    }

    static function send_mail(array $to, $subject, $message, $replyto, array $cc=array(), array $bcc=array()
        , $asynchronous = true)
    {
        $emailLogRecordID = null;

        $recipients = array();
        foreach ($to as $emailAddress){
            $recipients[] = array('recipient'=>$emailAddress);
        }

        $ccRecipients = array();
        foreach ($cc as $emailAddress){
            $ccRecipients[] = array('recipient'=>$emailAddress);
        }

        $bccRecipients = array();
        foreach ($bcc as $emailAddress){
            $bccRecipients[] = array('recipient'=>$emailAddress);
        }



        $vcapServices = json_decode($_SERVER['VCAP_SERVICES']);
        $data = array('contact'=> $replyto,
            'recipients'=>$recipients,
            'subject'=>$subject,
            'message'=>$message);

        if($ccRecipients){
            $data['cc'] = $ccRecipients;
        }

        if($bccRecipients){
            $data['bcc'] = $bccRecipients;
        }

        $data_json = json_encode($data);

        if(isset(AllItdqTables::$EMAIL_LOG)){
            $emailLogRecordID = self::prelog($to, $subject, $message, $data_json);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER,         1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,        240);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 240);
        curl_setopt($ch, CURLOPT_HTTPAUTH,  CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HEADER,    FALSE);

        $userpwd = $vcapServices->bluemailservice[0]->credentials->username . ':' . $vcapServices->bluemailservice[0]->credentials->password;
        curl_setopt($ch, CURLOPT_USERPWD,        $userpwd);

        curl_setopt($ch, CURLOPT_URL, $vcapServices->bluemailservice[0]->credentials->emailUrl);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data_json)));
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);

        $resp = curl_exec($ch);

        if($emailLogRecordID){
            self::updatelog($emailLogRecordID, $resp);
        }

        $responseObject = json_decode($resp);

        $statusUrl = $responseObject->link[0]->href;
        $status = self::getStatus($emailLogRecordID, $statusUrl);
        $statusObject = json_decode($status);

        if(!$asynchronous){
            sleep(5);
            $prevStatus = $status;
            $status = self::getStatus($emailLogRecordID, $statusUrl, $prevStatus);
            $statusObject = json_decode($status);
            $iteration =0;
            $attempts = 0;

            $statusObjects=array();
            $statusObjects[] = $statusObject;

            while (!self::checkStatus($statusObjects) && !$statusObject->locked){
                if($attempts++ > 20){
                    throw new \Exception($attempts . " unsuccessful attempts to send email. Abandoning process");
                }
                $iteration = ++$iteration <10 ? $iteration : 9; // wait a little longer each time up till 50 seconds wait. so if they are busy we're not hitting too frequently
                $response = self::resend($emailLogRecordID,$responseObject->link[1]->href);
                $prevStatus = $status;

                $responseObject = json_decode($response);
                $statusUrl = $responseObject->link[0]->href;
                $status = self::getStatus($emailLogRecordID, $statusUrl);
                $statusObject = json_decode($status);
                $statusObjects[]=$statusObject;
                sleep(5 + $iteration*5);
            }
        }
        return array('sendResponse' => $responseObject, 'Status'=>$statusObject);
    }

    static function checkStatus(array $statusObjects){
        $status = false;
        foreach ($statusObjects as $statusObject){
            print_r($statusObject);
            if($statusObject->status){
                $status = true;
                break;
            }
        }
        return $status;
    }




    static function prelog(array $to, $subject, $message, $data_json)
    {
        $sql  = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
        $sql .= " (TO, SUBJECT, MESSAGE, DATA_JSON ) VALUES ( '" . db2_escape_string(serialize($to)) ."','" . db2_escape_string($subject) . "'";
        $sql .= " ,'" . db2_escape_string($message) . "','" . db2_escape_string($data_json) . "'); ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__,__METHOD__,$sql);
            $emailRecordId = false;
        } else {
            db2_commit($_SESSION['conn']);
            $emailRecordId = db2_last_insert_id($_SESSION['conn']);
            self::clearLog();
        }

        return $emailRecordId;

    }

    static function updatelog($recordId, $result)
    {
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
        $sql .= " SET RESPONSE = '" . db2_escape_string($result) . "'" ;
        $sql .= " WHERE RECORD_ID= " . db2_escape_string($recordId) . "; ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__,__METHOD__,$sql);
            return false;
        }
        db2_commit($_SESSION['conn']);
        return true;
    }

    static function logStatus($recordId, $status)
    {
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
        $sql .= " SET LAST_STATUS = '" . db2_escape_string($status) . "', STATUS_TIMESTAMP = CURRENT TIMESTAMP " ;
        $sql .= " WHERE RECORD_ID= " . db2_escape_string($recordId) . "; ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__,__METHOD__,$sql);
            return false;
        }
        db2_commit($_SESSION['conn']);
        return true;
    }


    static function clearLog($retainPeriod = ' 1 month')
    {
       $sql  = " DELETE FROM " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
       $sql .= ' WHERE SENT_TIMESTAMP < (CURRENT TIMESTAMP - ' . $retainPeriod . "); ";
       db2_exec($_SESSION['conn'], $sql);
    }

    static function getEmailDetails($recordID){
        $sql  = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
        $sql .= ' WHERE RECORD_ID = ' . db2_escape_string($recordID);
        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception('Unable to read record details for email ' . $recordID);
        } else {
            $details = db2_fetch_assoc($rs);
            return $details;
        }
    }

    static function getStatus($recordId, $statusUrl, $prevStatus='first')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $statusUrl);

        $currentStatus = curl_exec($ch);
        $status = substr($currentStatus,0,-1)  . ',"prevStatus":"' . htmlspecialchars($prevStatus) . '"}';

        self::logStatus($recordId, $status);
        return $status;


    }

    static function resend($recordId, $resendUrl)
    {
        $emailDetails = self::getEmailDetails($recordId);

        $emailLogRecordID = self::prelog(unserialize($emailDetails['TO']), $emailDetails['SUBJECT'], $emailDetails['MESSAGE'], $emailDetails['DATA_JSON']);

        $vcapServices = json_decode($_SERVER['VCAP_SERVICES']);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER,         1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT,        240);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 240);
        curl_setopt($ch, CURLOPT_HTTPAUTH,  CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_HEADER,    FALSE);

        $userpwd = $vcapServices->bluemailservice[0]->credentials->username . ':' . $vcapServices->bluemailservice[0]->credentials->password;
        curl_setopt($ch, CURLOPT_USERPWD,        $userpwd);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_URL, $resendUrl);

        $resp = curl_exec($ch);
        self::updatelog($emailLogRecordID, $resp);
        self::getStatus($recordId, $emailDetails['PREV_STATUS']);
        return $resp;
    }


}