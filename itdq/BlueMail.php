<?php
namespace itdq;

use itdq\AllItdqTables;
use itdq\AuditTable;
use itdq\DbTable;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 *
 * @author GB001399
 *
 */
class BlueMail
{

    static function send_mail(array $to, $subject, $message, $replyto, array $cc=array(), array $bcc=array()
        , $asynchronous = true
        , array $attachments=array()
        , $isPesSuppressable = false)
    {
        
        $suppressingPesEmails = $_ENV['suppressPesEmails'] ? true : false;
        
        $suppressingPesEmails ? error_log('suppression of PES emails : ON') : error_log('suppression of PES emails : OFF'); 
        $isPesSuppressable ? error_log('PES Email eligible for suppression Subject:' . $subject) : null;
        
        if($suppressingPesEmails && $isPesSuppressable) {
            // We're surpressing the PES Related Emails - so do nothing at this point.
            $response = array(
                'response'=>"Message has not been sent.  Pes releated emails are suppressed"
            );
            $status = "PES related emails are suppressed. Not sent";
            error_log('Wont send email Subject:' . $subject);
            return array(
                'sendResponse' => $response, 
                'Status'=>$status
            );
        }
     
        error_log('Will send email Subject:' . $subject);
        
        $emailLogRecordID = null;

        $cleanedTo = $to;
        $cleanedCc = array_diff($cc, $cleanedTo, $bcc); // We can't CC/BCC someone already in the TO list.
        $cleanedBcc = array_diff($bcc, $cleanedTo, $cleanedCc);
        
        $status = '';
        $resp = true;

        $mail = new PHPMailer();

        foreach ($cleanedTo as $emailAddress){
            if(!empty(trim($emailAddress))){
                $resp = $resp ? $mail->addAddress($emailAddress) : $resp;
            }
        }
        
        foreach ($cleanedCc as $emailAddress){
            if(!empty(trim($emailAddress))){
                $resp = $resp ? $mail->addCC($emailAddress) : $resp;
            }
        }
        
        foreach ($cleanedBcc as $emailAddress){
            if(!empty(trim($emailAddress))){
                $resp = $resp ? $mail->addBCC($emailAddress) : $resp;
            }
        }
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        if($resp && $attachments){
            foreach ($attachments as $attachment){
                if (isset($attachment['path'])) {
                    $exists = (file_exists($attachment['path'])) ? "Yes" : "No" ;
                    error_log("Attachment " . $attachment['path'] . " exists:" . print_r($exists,true) );
                    // error_log(print_r(scandir("../emailAttachments"),true));
                } else {
                    $attachment['path'] = '';
                }
                if (!empty($attachment['path'])) {
                    // send physically existing file
                    $resp = $resp ? $mail->addAttachment($attachment['path'],$attachment['filename'],'base64',$attachment['content_type']) : $resp;
                } else {
                    // send temporary generated file
                    $resp = $resp ? $mail->addStringAttachment($attachment['data'],$attachment['filename'],'base64',$attachment['content_type']) : $resp;
                }
                if(!$resp){
                    $status = "Errored";
                    $response = array('response'=>"Message has not been sent.  Attachment ".$attachment['filename']." not found");
                }
            }
        }
        if ($resp) {
            switch (trim($_ENV['email'])) {
                case 'dev':
                case 'user':
                    // We're in DEV mode for emails - override the recipients.
                    // But if we're in "batch" mode, the ssoEmail doesn't contain a valid email address, so send it to devemailid or me.
                    if (filter_var($_SESSION['ssoEmail'], FILTER_VALIDATE_EMAIL)) {
                        $localEmail = $_SESSION['ssoEmail'];
                    } else {
                        $localEmail = ! empty($_ENV['devemailid']) ? $_ENV['devemailid'] : 'piotr.tajanowicz@ocean.ibm.com';
                    }

                    $recipient = $_ENV['email'] == 'user' ? $localEmail : $_ENV['devemailid'];
                    $mail->clearAllRecipients();
                    $mail->addAddress($recipient);
                    $mail->clearCCs();
                    $mail->clearBCCs();
                    $mail->Subject = "**" . $_ENV['environment'] . "**" . $subject;

                // no BREAK - need to drop through to proper email.
                case 'on':
                    if (isset(AllItdqTables::$EMAIL_LOG)) {
                        $emailLogRecordID = self::prelog($to, $subject, $message, null, $cc, $bcc);
                    }

                    // $mail->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output ; SMTP::DEBUG_OFF
                    // $mail->isSMTP(); // Send using SMTP
                    // $mail->Host = 'na.relay.ibm.com'; // Set the SMTP server to send through
                    // $mail->SMTPAuth = false;
                    // $mail->SMTPAutoTLS = false;
                    // $mail->Port = 25;

                    $mail->SMTPDebug = SMTP::DEBUG_OFF; // Enable verbose debug output ; SMTP::DEBUG_OFF
                    $mail->isSMTP(); // Send using SMTP
                    $mail->Host = 'authnz.proofpoint.com'; // Set the SMTP server to send through
                    $mail->SMTPAuth = true;
                    $mail->SMTPAutoTLS = true;
                    $mail->Port = 465; // 25, 465, or 587
                    $mail->Username = "55745349-a422-4d7c-aa40-5c52dab03574";             
                    $mail->Password = "mG46S=caDv+G"; 
    
                    $replyto = 'UKI.Business.Intelligence@kyndryl.com';

                    $mail->setFrom($replyto);
                    $mail->isHTML(true);

                    $mail->Body = $message;

                    if (! $mail->send()) {
                        $response = array(
                            'response' => 'Mailer error: ' . $mail->ErrorInfo
                        );
                        $status = 'error sending';
                        throw new \Exception('Error trying to send email :' . $subject);
                    } else {
                        $response = array(
                            'response' => 'Message has been sent.'
                        );
                        $status = 'sent';
                    }

                    $responseObject = json_encode($response);
                    if ($emailLogRecordID) {
                        self::updatelog($emailLogRecordID, $responseObject);
                    }
                    break;

                default:

                    var_dump($_ENV['email']);

                    $response = array(
                        'response' => "email disabled in this environment, did not initiate send"
                    );
                    $responseObject = json_encode($response);
                    $status = 'email feature disabled, nothing sent';

                    if ($emailLogRecordID) {
                        self::updatelog($emailLogRecordID, $responseObject);
                    }
                    break;
            }
        }
        return array(
            'sendResponse' => $response, 
            'Status' => $status
        );
    }

    static function checkStatus(array $statusObjects){
        return true;
    }

    static function prelog(array $to, $subject, $message, $data_json, $cc=null, $bcc=null)
    {
        $auditString = "Invoked:<b>" . __METHOD__ . "</b>To:" . serialize($to) . "</br>";
        $auditString.= !empty($cc) ? "CC:" . htmlspecialchars(serialize($cc)) ."<br/>" : null;
        $auditString.= !empty($cc) ? "BCC:" . htmlspecialchars(serialize($bcc)) . "<br/>" : null;
        $auditString.= "Subject:" . htmlspecialchars($subject) . "-" . $subject . "</br>";
        $auditString.= "Message:" . htmlspecialchars(substr($message,0,200)) . "</br>";
//         $auditString.= "DataJson:" . htmlspecialchars(substr(serialize($data_json),0,20));

        AuditTable::audit($auditString,AuditTable::RECORD_TYPE_DETAILS);

        $sql = " INSERT INTO " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
        $sql.= " (TO, SUBJECT, MESSAGE, DATA_JSON ";
        $sql.= !empty($cc) ? " ,CC " : null ;
        $sql.= !empty($bcc) ? " ,BCC " : null ;
        $sql.= " ) VALUES ( ";
        $sql.= " ?,?,?,? ";
        $sql.= !empty($cc) ? " ,? " : null ;
        $sql.= !empty($bcc) ? " ,? " : null ;
        $sql.= " ); ";

        $preparedStatement = db2_prepare($GLOBALS['conn'], $sql);

        $table = new EmailLogTable(AllItdqTables::$EMAIL_LOG);
        $subject = $table->truncateValueToFitColumn($subject, 'SUBJECT');
        $message = $table->truncateValueToFitColumn($message, 'MESSAGE');
        $data_json = $table->truncateValueToFitColumn($data_json, 'DATA_JSON');

        $data = array(serialize($to), $subject, $message, $data_json);

        !empty($cc)  ? $data[] = serialize($cc) : null;
        !empty($bcc) ? $data[] = serialize($bcc) : null;
        $rs = sqlsrv_execute($preparedStatement,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__,__METHOD__,$sql);
            $emailRecordId = false;
        } else {
            db2_commit($GLOBALS['conn']);
            $emailRecordId = db2_last_insert_id($GLOBALS['conn']);
            self::clearLog();
        }

        return $emailRecordId;
    }

    static function updatelog($recordId, $result)
    {
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
        $sql .= " SET RESPONSE = '" . htmlspecialchars($result) . "'" ;
        $sql .= " WHERE RECORD_ID= " . htmlspecialchars($recordId) . "; ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__,__METHOD__,$sql);
            return false;
        }
        db2_commit($GLOBALS['conn']);
        return true;
    }

    static function logStatus($recordId, $status)
    {
        $sql  = " UPDATE " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
        $sql .= " SET LAST_STATUS = '" . htmlspecialchars($status) . "', STATUS_TIMESTAMP = CURRENT TIMESTAMP " ;
        $sql .= " WHERE RECORD_ID= " . htmlspecialchars($recordId) . "; ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__,__METHOD__,$sql);
            return false;
        }
        db2_commit($GLOBALS['conn']);
        return true;
    }


    static function clearLog($retainPeriod = ' 3 months')
    {
       $sql  = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
       $sql .= ' WHERE SENT_TIMESTAMP < (CURRENT TIMESTAMP - ' . $retainPeriod . "); ";
       sqlsrv_query($GLOBALS['conn'], $sql);
    }

    static function getEmailDetails($recordID){
        $sql  = " SELECT * FROM " . $GLOBALS['Db2Schema'] . "." . AllItdqTables::$EMAIL_LOG;
        $sql .= ' WHERE RECORD_ID = ' . htmlspecialchars($recordID);
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception('Unable to read record details for email ' . $recordID);
        } else {
            $details = sqlsrv_fetch_array($rs);
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
//         $emailDetails = self::getEmailDetails($recordId);

//         $emailLogRecordID = self::prelog(unserialize($emailDetails['TO']), $emailDetails['SUBJECT'], $emailDetails['MESSAGE'], $emailDetails['DATA_JSON']);

//         $vcapServices = json_decode($_SERVER['VCAP_SERVICES']);

//         $ch = curl_init();
//         curl_setopt($ch, CURLOPT_HEADER,         1);
//         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
//         curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//         curl_setopt($ch, CURLOPT_TIMEOUT,        240);
//         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 240);
//         curl_setopt($ch, CURLOPT_HTTPAUTH,  CURLAUTH_BASIC);
//         curl_setopt($ch, CURLOPT_HEADER,    FALSE);

//         $userpwd = $vcapServices->bluemailservice[0]->credentials->username . ':' . $vcapServices->bluemailservice[0]->credentials->password;
//         curl_setopt($ch, CURLOPT_USERPWD,        $userpwd);
//         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
//         curl_setopt($ch, CURLOPT_URL, $resendUrl);

//         $resp = curl_exec($ch);
//         self::updatelog($emailLogRecordID, $resp);
//         self::getStatus($recordId, $emailDetails['PREV_STATUS']);
//         return $resp;
    }

    private static function validateIbmEmail($emailAddress){
        $domain = strtolower(substr($emailAddress,-7));
        $hasTheAt = stripos($emailAddress, '@');
        return $domain=='ibm.com' && $hasTheAt;
    }

    static function validateIbmEmailArray($arrayOfEmailAddress){
        foreach ($arrayOfEmailAddress as $key => $emailAddress){
            if(!self::validateIbmEmail(trim($emailAddress))){
                unset($arrayOfEmailAddress[$key]);
            }
        }
        return $arrayOfEmailAddress;
    }
}