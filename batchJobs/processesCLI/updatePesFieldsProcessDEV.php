<?php

use itdq\AuditTable;
use itdq\BlueMail;
use itdq\DbTable;
use itdq\Loader;
use vbac\allTables;
use vbac\notification\timingSummary;
use vbac\personRecord;
use vbac\upesApiData;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!function_exists('tryConnectToUPES')){
    function tryConnectToUPES($conn_string){
        error_log("Attempting Pconnect to DB2 from Pod:" . $_ENV['HOSTNAME'] . ":" . $conn_string);
        $preConnect = microtime(true);
        $connection =  db2_connect( $conn_string, "", "" );
        $postConnect = microtime(true);
        error_log("Db2 Pconnect took:" . (float)($postConnect-$preConnect));
        return $connection;
    }
}

class updatePesFieldsProcess {

    private $updatedVBACPerson = '';
    private $notFoundInVBAC = '';
    private $noPESDataForPerson = '';
    private $noPESDataForTracker = '';

    private $timeMeasurements = array();

    private $personFields = array();
    private $pesTrackerFields = array();

    function __construct() {

    }
}

if (isset($argv[1])) {

    $token = '35310a45-62a7-4de6-b8a3-17f9c6efdd26';
    $accountId = '1330';
    $url = $argv[1] . '?token=' . $token . '&accountid=' . $accountId;
    
    $timeMeasurements = array();

    AuditTable::audit("PES Fields update invoked:",AuditTable::RECORD_TYPE_AUDIT);

    $start = microtime(true);
    $startCurl = microtime(true);
    
        # Get database details from the VCAP_SERVICES environment variable
        #
        # *This can only work if you have used the Bluemix dashboard to
        # create a connection from your dashDB service to your PHP App.
        #
        $ssl_dsn  = 'DATABASE=BLUDB;HOSTNAME=541923aa-a2a2-40a4-9e67-94eb6e88d5f5.bs2io90l08kqb1od8lcg.databases.appdomain.cloud;PORT=30725;PROTOCOL=TCPIP;UID=iae2chzk;PWD=voJchMHqPNqo3mVk;Security=SSL;';

        # Build the connection string
        #
        $driver = "DRIVER={IBM DB2 ODBC DRIVER};";
        $conn_string = $driver . $ssl_dsn; # SSL

        $connToUPES=false;
        $attempts = 0;

        while(!$connToUPES && ++$attempts < 3){
            $connToUPES = tryConnectToUPES($conn_string);
            if (!$connToUPES) {
                error_log("Failed attempt $attempts to connect to DB2");
                error_log("Msg:" . db2_conn_errormsg());
                error_log("Err:" . db2_conn_error());
                sleep(3);
            }
        }
        
        if( $connToUPES ) {

            $schemaUPES = 'UPES_NEWCO';
            $StatementUPES = "SET CURRENT SCHEMA='$schemaUPES';";

            $rs = sqlsrv_query($connToUPES, $StatementUPES);

            if (! $rs) {
                echo "<br/>" . $StatementUPES    . "<br/>";

                echo "<pre>";
                print_r($_SESSION);
                echo "</pre>";

                echo "<BR>" . json_encode(sqlsrv_errors()) . "<BR>";
                echo "<BR>" . json_encode(sqlsrv_errors()) . "<BR>";
                exit("Set current schema failed");
            }
            sqlsrv_commit($connToUPES, TRUE); // This is how it was on the Wintel Box - so the code has no/few commit points.
        } else {
            error_log(__FILE__ . __LINE__ . " Connect to DB2 Failed");
            error_log(__FILE__ . __LINE__ . $conn_string);
            error_log(__FILE__ . __LINE__ . db2_conn_errormsg());
            error_log(__FILE__ . __LINE__ . db2_conn_error());
            throw new Exception('Failed to connect to DB2');
        }

        // direct connection to UPES
        $data = array(
            $accountId 
        );

        $countSql = " SELECT COUNT(*) AS COUNTER ";
        $countSql.= "FROM " . $schemaUPES . ".ACCOUNT_PERSON AS AP "; 
        $countSql.= "LEFT JOIN " . $schemaUPES . ".PERSON AS P ";
        $countSql.= "ON AP.UPES_REF = P.UPES_REF ";
        $countSql.= "LEFT JOIN " . $schemaUPES . ".PES_LEVELS AS PL ";
        $countSql.= "ON AP.PES_LEVEL = PL.PES_LEVEL_REF AND AP.ACCOUNT_ID = PL.ACCOUNT_ID ";
        $countSql.= "WHERE AP.ACCOUNT_ID = ? ";

        $preparedCountStatement = sqlsrv_prepare($connToUPES, $countSql);
        $rs = sqlsrv_execute($preparedCountStatement, $data);
        if (! $rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $counter = 0;
        while($row = sqlsrv_fetch_array($preparedCountStatement)){
            $counter = $row['COUNTER'];
        }

        $sql = " SELECT AP.ACCOUNT_ID, P.CNUM, P.EMAIL_ADDRESS, PROCESSING_STATUS, PROCESSING_STATUS_CHANGED ";
        $sql.= ", PES_DATE_REQUESTED, PES_REQUESTOR, PES_DATE_RESPONDED, PES_STATUS_DETAILS, PES_STATUS";
        $sql.= ", PL.PES_LEVEL, PES_RECHECK_DATE, PES_CLEARED_DATE ";
        $sql.= "FROM " . $schemaUPES . ".ACCOUNT_PERSON AS AP "; 
        $sql.= "LEFT JOIN " . $schemaUPES . ".PERSON AS P ";
        $sql.= "ON AP.UPES_REF = P.UPES_REF ";
        $sql.= "LEFT JOIN " . $schemaUPES . ".PES_LEVELS AS PL ";
        $sql.= "ON AP.PES_LEVEL = PL.PES_LEVEL_REF AND AP.ACCOUNT_ID = PL.ACCOUNT_ID ";
        $sql.= "WHERE AP.ACCOUNT_ID = ? ";
        $sql.= "OPTIMIZE FOR " . $counter . " ROWS ";
        $sql.= "FOR FETCH ONLY WITH UR ";
        $sql.= "FETCH FIRST " . $counter ." ROWS ONLY";

        $preparedStatement = sqlsrv_prepare($connToUPES, $sql);

        $rs = sqlsrv_execute($preparedStatement, $data);
        if (! $rs) {
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();
        $noTrim = false;

        $count = 1;
        while($row = sqlsrv_fetch_array($preparedStatement)){
            if ($noTrim === false) {
                $row = array_map('trim',$row);
            }


            $APIData = new upesApiData();
            $APIData->ACCOUNT_ID = $row['ACCOUNT_ID'];
            $APIData->CNUM = $row['CNUM'];
            $APIData->EMAIL_ADDRESS = $row['EMAIL_ADDRESS']; 
            $APIData->PROCESSING_STATUS = $row['PROCESSING_STATUS'];
            $APIData->PROCESSING_STATUS_CHANGED = $row['PROCESSING_STATUS_CHANGED'];
            $APIData->PES_DATE_REQUESTED = $row['PES_DATE_REQUESTED']; 
            $APIData->PES_REQUESTOR = $row['PES_DATE_REQUESTED'];
            $APIData->PES_DATE_RESPONDED = $row['PES_DATE_RESPONDED']; 
            $APIData->PES_STATUS_DETAILS = $row['PES_STATUS_DETAILS']; 
            $APIData->PES_STATUS = $row['PES_STATUS'];
            $APIData->PES_LEVEL = $row['PES_LEVEL'];
            $APIData->PES_RECHECK_DATE = $row['PES_RECHECK_DATE']; 
            $APIData->PES_CLEARED_DATE = $row['PES_CLEARED_DATE'];

            $data[] = $APIData;
            $count++;
        }

    // Read data from UPES CURL section
    // $ch = curl_init();
    // curl_setopt($ch, CURLOPT_HEADER,         1);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($ch, CURLOPT_HEADER,         FALSE);
    // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  // it doesn't like the self signed certs on Cirrus
    // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    // curl_setopt($ch, CURLOPT_URL, $url);

    // $pesDataAllJson = curl_exec($ch);
    // $err = curl_error($ch);

    // curl_close($ch);

    // if ($err) {
    //     throw new \Exception('cURL Error #:' . $err);
    // } else {
    
        // $pesDataAll = json_decode($pesDataAllJson);
        // if (!$pesDataAll) {
        //     throw new \Exception('Can not parse API response');
        // }

        // $upesAPIData = $pesDataAll->data;
        $upesAPIData = $data;

        $endCurl = microtime(true);
        $timeMeasurements['CURL'] = (float)($endCurl-$startCurl);

        db2_close($connToUPES);

        $personFields = personRecord::$personFields;
        $pesTrackerFields = personRecord::$pesTrackerFields;
    
        sqlsrv_commit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);
    
        $updatesPerformed = 0;
        $commitEvery100Updates = 100;
    
        $startGetVBACEmployees = microtime(true);
    
        $loader = new Loader();
        $employeesEmailsRaw = $loader->load('EMAIL_ADDRESS', allTables::$PERSON, ' AND EMAIL_ADDRESS is not null');
        $employeesEmails = array_map('strtolower',$employeesEmailsRaw);
    
        $endGetVBACEmployees = microtime(true);
        $timeMeasurements['getVBACEmployees'] = (float)($endGetVBACEmployees-$startGetVBACEmployees);
    
        AuditTable::audit("PES Fields update read " . count($upesAPIData) . " people from PES Application.",AuditTable::RECORD_TYPE_AUDIT);
        AuditTable::audit("PES Fields update will check " . count($employeesEmails) . " people currently existing in the vBAC.",AuditTable::RECORD_TYPE_AUDIT);
        
        // create and prepare the update statment to the PERSON table
        /*
        $updatePersonPESApiStatusSql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON ;
        $updatePersonPESApiStatusSql.= " SET PES_API_STATUS =? ";
        $updatePersonPESApiStatusSql.= " WHERE lower(EMAIL_ADDRESS)=? ";
    
        $preparedUpdatePersonPESApiStatusSql = sqlsrv_prepare($GLOBALS['conn'], $updatePersonPESApiStatusSql);
    
        if(!$preparedUpdatePersonPESApiStatusSql){
            echo json_encode(sqlsrv_errors());
            echo json_encode(sqlsrv_errors());
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
    
        foreach ($upesAPIData as $upesData){
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
                    
                    $preparedUpdatePersonSql = sqlsrv_prepare($GLOBALS['conn'], $updatePersonSql);
                    
                    if(!$preparedUpdatePersonSql){
                        echo json_encode(sqlsrv_errors());
                        echo json_encode(sqlsrv_errors());
                        print_r($personData);
                        DbTable::displayErrorMessage($preparedUpdatePersonSql, __FILE__, __FILE__, $updatePersonSql);
                        return;
                    }
                    
                    $rsPerson = sqlsrv_execute($preparedUpdatePersonSql, $personData);    
                    if(!$rsPerson){
                        print_r($personData);
                        print_r($updatePersonSql);
                        DbTable::displayErrorMessage($rsPerson, __FILE__, __FILE__, $updatePersonSql);     
                        sqlsrv_rollback($GLOBALS['conn']);
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
                    
                    $preparedUpdatePesTrackerSql = sqlsrv_prepare($GLOBALS['conn'], $updatePesTrackerSql);
                    
                    if(!$preparedUpdatePesTrackerSql){
                        echo json_encode(sqlsrv_errors());
                        echo json_encode(sqlsrv_errors());
                        print_r($pesTrackerData);
                        DbTable::displayErrorMessage($preparedUpdatePesTrackerSql, __FILE__, __FILE__, $updatePesTrackerSql);
                        return;
                    }
                    
                    $rsPesTracker = sqlsrv_execute($preparedUpdatePesTrackerSql, $pesTrackerData);
                    if(!$rsPesTracker){
                        print_r($pesTrackerData);
                        print_r($updatePesTrackerSql);
                        DbTable::displayErrorMessage($rsPesTracker, __FILE__, __FILE__, $updatePesTrackerSql);
                        sqlsrv_rollback($GLOBALS['conn']);
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
                    sqlsrv_rollback($GLOBALS['conn']);
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
                sqlsrv_rollback($GLOBALS['conn']);
            }
        }
        */
    
        sqlsrv_commit($GLOBALS['conn']);
    
        AuditTable::audit("PES Fields update completed:",AuditTable::RECORD_TYPE_AUDIT);
    
        $end = microtime(true);
        $timeMeasurements['overallTime'] = (float)($end-$start);
        
        $notification = new timingSummary();
        $notification->send_mail($url, $upesAPIData, $employeesEmails);
    
        $to = array($_ENV['devemailid']);
        $cc = array();
        if (strstr($_ENV['environment'], 'vbac')) {
            $cc[] = 'Anthony.Stark@kyndryl.com';
        }
    
        $subject = 'PES Status update timings';

        $message = 'PES API Url: ' . $url;
        $message .= '<BR/>Updated vBAC Environment: ' . $GLOBALS['Db2Schema'];
    
        $message .= '<HR>';
    
        $message .= "<BR/>PES Fields update read " . count($upesAPIData) . " people from PES Application.";
        $message .= "<BR/>PES Fields update will check " . count($employeesEmails) . " people currently existing in the vBAC.";
    
        $message .= '<HR>';
    
        $message .= "<BR/>Quantity of PES_TRACKER and PERSON Records updated: " . $updatedVBACPerson;
        $message .= "<BR/>Quantity of email addresses from PES Application not found in vBAC: " . $notFoundInVBAC;
    
        $message .= "<BR/>Quantity of email addresses with no data for PERSON update: " . $noPESDataForPerson;
        $message .= "<BR/>Quantity of email addresses with no data for PES Tracker update: " . $noPESDataForTracker;
        
        $message .= '<HR>';
        
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
    // }
} else {
    throw new \Exception('PES Application URL was missing.');
}