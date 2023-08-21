<?php

use itdq\AuditTable;
use itdq\BlueMail;
use itdq\DbTable;
use itdq\Loader;
use vbac\allTables;
use vbac\notification\timingSummary;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// $GLOBALS['Db2Schema'] = 'VBAC';

if(!isset($rootScriptName)){
    $rootScriptName = '';
}
$rootScriptName = str_replace('/var/www/html/batchJobs/', '', $rootScriptName);
switch($rootScriptName) {
    case 'updatePesFieldsFromUpes.php':

        throw new \Exception('This script has been permanently disabled.');

        break;
    case 'updatePesFieldsFromUpesKyndryl.php':

        throw new \Exception('This script has been permanently disabled.');
        exit;

        // $url = $_ENV['upes_url'] . '/api/pesStatus.php?token=' . $_ENV['upes_api_token'] . '&accountid=1330';

        if (isset($url)) {

            $timeMeasurements = array();

            AuditTable::audit("PES Fields update invoked:",AuditTable::RECORD_TYPE_AUDIT);

            $startCurl = microtime(true);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HEADER,         1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER,         FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  // it doesn't like the self signed certs on Cirrus
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_URL, $url);

            $pesDataAllJson = curl_exec($ch);
            $err = curl_error($ch);

            curl_close($ch);

            $pesDataAll = json_decode($pesDataAllJson);

            $endCurl = microtime(true);
            $timeMeasurements['CURL'] = (float)($endCurl-$startCurl);

            $personFields = array(
                'PES_DATE_REQUESTED',
                'PES_DATE_RESPONDED',
                'PES_STATUS_DETAILS',
                'PES_RECHECK_DATE',
                'PES_CLEARED_DATE',
                'PES_STATUS',
                'PES_LEVEL',
                'PES_REQUESTOR'
            );

            $pesTrackerFields = array(
                'PROCESSING_STATUS',
                'PROCESSING_STATUS_CHANGED'
                // 'COMMENT'
            );

            sqlsrv_commit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

            $updatesPerformed = 0;
            $commitEvery100Updates = 100;

            $startGetVBACEmployees = microtime(true);

            $loader = new Loader();
            $employeesEmailsRaw = $loader->load('EMAIL_ADDRESS', allTables::$PERSON, ' AND EMAIL_ADDRESS is not null');
            $employeesEmails = array_map('strtolower',$employeesEmailsRaw);

            $endGetVBACEmployees = microtime(true);
            $timeMeasurements['getVBACEmployees'] = (float)($endGetVBACEmployees-$startGetVBACEmployees);

            AuditTable::audit("PES Fields update read " . count($pesDataAll->data) . " people from PES Application.",AuditTable::RECORD_TYPE_AUDIT);
            AuditTable::audit("PES Fields update will check " . count($employeesEmails) . " people currently existing in the vBAC.",AuditTable::RECORD_TYPE_AUDIT);
            
            // create and prepare the update statment to the PERSON table
            /*
            $updatePersonPESApiStatusSql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON ;
            $updatePersonPESApiStatusSql.= " SET PES_API_STATUS =? ";
            $updatePersonPESApiStatusSql.= " WHERE lower(EMAIL_ADDRESS)=? ";

            $preparedUpdatePersonPESApiStatusSql = db2_prepare($GLOBALS['conn'], $updatePersonPESApiStatusSql);

            if(!$preparedUpdatePersonPESApiStatusSql){
                echo sqlsrv_errors();
                echo sqlsrv_errors();
                // print_r($personData);
                DbTable::displayErrorMessage($preparedUpdatePersonPESApiStatusSql, __FILE__, __FILE__, $updatePersonPESApiStatusSql);
                return;
            }
            */

            $startDataUpdate = microtime(true);

            $updatedVBACPerson = 0;
            $notFoundInVBAC = 0;

            $noPESDataForPerson = 0;
            $noPESDataForTracker = 0;

            foreach ($pesDataAll->data as $upesData){
                $email = strtolower($upesData->EMAIL_ADDRESS);
                if (in_array($email, $employeesEmails)) {

                    // create and prepare the update statment to the PERSON table
                
                    $updatePersonSql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON ;
                    $updatePersonSql.= " SET ";
                    $personData = array();
                    
                    foreach ($personFields as $fieldName) {
                        $$fieldName = !empty($upesData->$fieldName) ? $upesData->$fieldName : null;
                        $updatePersonSql.= !empty($upesData->$fieldName) ? $fieldName . "=?, " : null;
                        !empty($upesData->$fieldName) ? $personData[] = $$fieldName : null;        
                    }
                    
                    if (count($personData) > 0) {

                        $updatePersonSql = substr($updatePersonSql,0,-2) . " WHERE lower(EMAIL_ADDRESS)=? ";
                        $personData[] = $email;
                        
                        $preparedUpdatePersonSql = db2_prepare($GLOBALS['conn'], $updatePersonSql);
                        
                        if(!$preparedUpdatePersonSql){
                            echo sqlsrv_errors();
                            echo sqlsrv_errors();
                            print_r($personData);
                            DbTable::displayErrorMessage($preparedUpdatePersonSql, __FILE__, __FILE__, $updatePersonSql);
                            return;
                        }
                        
                        $rsPerson = sqlsrv_execute($preparedUpdatePersonSql, $personData);    
                        if(!$rsPerson){
                            print_r($personData);
                            print_r($updatePersonSql);
                            DbTable::displayErrorMessage($rsPerson, __FILE__, __FILE__, $updatePersonSql);     
                            db2_rollback($GLOBALS['conn']);
                        }
                    } else {
                        echo "<br/> No data for PERSON update";
                        $noPESDataForPerson++;
                    }
                    
                    // create and prepare the update statment to the PES_TRACKER table
                    
                    $updatePesTrackerSql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$PES_TRACKER ;
                    $updatePesTrackerSql.= " SET ";
                    $pesTrackerData = array();

                    foreach ($pesTrackerFields as $fieldName) {
                        $$fieldName = !empty($upesData->$fieldName) ? $upesData->$fieldName : null;
                        $updatePesTrackerSql.= !empty($upesData->$fieldName) ? $fieldName . "=?, " : null;
                        !empty($upesData->$fieldName) ? $pesTrackerData[] = $$fieldName : null;
                    }
                    
                    if (count($pesTrackerData) > 0) {

                        $updatePesTrackerSql = substr($updatePesTrackerSql,0,-2) . " WHERE CNUM=( SELECT CNUM FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " WHERE lower(EMAIL_ADDRESS) = ? FETCH FIRST 1 ROWS ONLY ) ";
                        $pesTrackerData[] = $email;
                        
                        $preparedUpdatePesTrackerSql = db2_prepare($GLOBALS['conn'], $updatePesTrackerSql);
                        
                        if(!$preparedUpdatePesTrackerSql){
                            echo sqlsrv_errors();
                            echo sqlsrv_errors();
                            print_r($pesTrackerData);
                            DbTable::displayErrorMessage($preparedUpdatePesTrackerSql, __FILE__, __FILE__, $updatePesTrackerSql);
                            return;
                        }
                        
                        $rsPesTracker = sqlsrv_execute($preparedUpdatePesTrackerSql, $pesTrackerData);
                        if(!$rsPesTracker){
                            print_r($pesTrackerData);
                            print_r($updatePesTrackerSql);
                            DbTable::displayErrorMessage($rsPesTracker, __FILE__, __FILE__, $updatePesTrackerSql);
                            db2_rollback($GLOBALS['conn']);
                        }
                    } else {
                        echo "<br/> No data for PES Tracker update";
                        $noPESDataForTracker++;
                    }

                    // set PES_API_STATUS to FOUND
                    /*
                    $personPESApiStatusData = array();
                    $personPESApiStatusData[] = personRecord::PES_API_STATUS_FOUND;
                    $personPESApiStatusData[] = $email;
                    
                    $rsPerson = sqlsrv_execute($preparedUpdatePersonPESApiStatusSql,$personPESApiStatusData);    
                    if(!$rsPerson){
                        DbTable::displayErrorMessage($rsPerson, __FILE__, __FILE__, $updatePersonPESApiStatusSql);     
                        db2_rollback($GLOBALS['conn']);
                    }
                    */
                    
                    unset($employeesEmails[$upesData->EMAIL_ADDRESS]);

                    echo "<br/> Updated PES_TRACKER and PERSON Records for Email:" . $upesData->EMAIL_ADDRESS;
                    $updatedVBACPerson++;

                } else {
            
                    echo "<br/> Email:" . $upesData->EMAIL_ADDRESS . " read from PES Application not found in vBAC.";
                    $notFoundInVBAC++;

                }
            }

            $endDataUpdate = microtime(true);
            $timeMeasurements['dataUpdate'] = (float)($endDataUpdate-$startDataUpdate);

            // set PES_API_STATUS to NOT_FOUND
            /*
            foreach ($employeesEmails as $email){

                $personPESApiStatusData = array();
                $personPESApiStatusData[] = personRecord::PES_API_STATUS_NOT_FOUND;
                $personPESApiStatusData[] = strtolower($email);

                $rsPerson = sqlsrv_execute($preparedUpdatePersonPESApiStatusSql,$personPESApiStatusData);    
                if(!$rsPerson){
                    DbTable::displayErrorMessage($rsPerson, __FILE__, __FILE__, $updatePersonPESApiStatusSql);     
                    db2_rollback($GLOBALS['conn']);
                }
            }
            */

            db2_commit($GLOBALS['conn']);

            AuditTable::audit("PES Fields update completed:",AuditTable::RECORD_TYPE_AUDIT);

            $end = microtime(true);
            $timeMeasurements['overallTime'] = (float)($end-$start);

            $notification = new timingSummary();

            $to = array($_ENV['devemailid']);
            $cc = array();
            if (strstr($_ENV['environment'], 'vbac')) {
                $cc[] = 'Anthony.Stark@kyndryl.com';
            }

            $subject = 'PES Status update timings';
            $message = 'Name of executed script: ' . $rootScriptName;

            $message .= '<HR>';

            $message .= "<BR/>PES Fields update read " . count($pesDataAll->data) . " people from PES Application.";
            $message .= "<BR/>PES Fields update will check " . count($employeesEmails) . " people currently existing in the vBAC.";

            $message .= '<HR>';

            $message .= "<BR/>Quantity of PES_TRACKER and PERSON Records updated: " . $updatedVBACPerson;
            $message .= "<BR/>Quantity of email addresses from PES Application not found in vBAC: " . $notFoundInVBAC;

            $message .= "<BR/>Quantity of email addresses with no data for PERSON update: " . $noPESDataForPerson;
            $message .= "<BR/>Quantity of email addresses with no data for PES Tracker update: " . $noPESDataForTracker;

            $message .= '<HR>';

            // $message .= serialize($timeMeasurements) . ' ' . $rootScriptName;
            $message .= '<BR/>cURL time: ' . $timeMeasurements['CURL'];
            $message .= '<BR/>PES API call time: ' . $timeMeasurements['getVBACEmployees'];
            $message .= '<BR/>vBAC records update time: ' . $timeMeasurements['dataUpdate'];
            $message .= '<BR/>Overall time: ' . $timeMeasurements['overallTime'];
            
            $message .= '<HR>';

            $message .= '<BR/>PERSON fields updated during the last execution: ';
            foreach($personFields as $key => $field) {
                $message .= '<BR/>'.$field;
            }

            $message .= '<HR>';

            $message .= '<BR/> PES Tracker fields updated during the last execution: ';
            foreach($pesTrackerFields as $key => $field) {
                $message .= '<BR/>'.$field;
            }

            $message .= '<HR>';

            $replyto = $_ENV['noreplyemailid'];
            $resonse = BlueMail::send_mail($to, $subject, $message, $replyto, $cc);
        } else {
            throw new \Exception('PES Application URL was missing.');
        }
        break;
    default:
        throw new \Exception('Incorrect way of execution of script.');
        break;
}