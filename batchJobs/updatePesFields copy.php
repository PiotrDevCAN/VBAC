<?php

use vbac\allTables;
use itdq\DbTable;
use itdq\Loader;

// $url = $_ENV['upes_url'] . '/api/pesStatus.php?token=' . $_ENV['upes_api_token'] . '&accountid=1330';

if (isset($url)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER,         1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER,        FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);  // it doesn't like the self signed certs on Cirrus
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $url);

    $pesDataAllJson = curl_exec($ch);
    $pesDataAll = json_decode($pesDataAllJson);

    $personFields = array(
        'PES_DATE_REQUESTED',
        'PES_DATE_RESPONDED',
        'PES_STATUS_DETAILS',
        'PES_STATUS',
        'PES_LEVEL',
        'PES_RECHECK_DATE',
        'PES_CLEARED_DATE'
    );

    $pesTrackerFields = array('PROCESSING_STATUS','PROCESSING_STATUS_CHANGED', 'COMMENT');

    db2_autocommit($GLOBALS['conn'],DB2_AUTOCOMMIT_OFF);

    $updatesPerformed = 0;
    $commitEvery100Updates = 100;

    foreach ($pesDataAll->data as $upesData){
        // create and prepare the update statment to the PERSON table
        
        $updatePersonSql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON ;
        $updatePersonSql.= " SET ";
        $personData = array();
        
        foreach ($personFields as $fieldName) {
            $$fieldName = !empty($upesData->$fieldName) ? $upesData->$fieldName : null;
            $updatePersonSql.= !empty($upesData->$fieldName) ? $fieldName . "=?, " : null;
            !empty($upesData->$fieldName) ? $personData[] = $$fieldName : null;        
        }
        
        $updatePersonSql = substr($updatePersonSql,0,-2) . " WHERE lower(EMAIL_ADDRESS)=? ";
        $personData[] = strtolower($upesData->EMAIL_ADDRESS) ;
        
        $preparedUpdatePersonSql = db2_prepare($GLOBALS['conn'], $updatePersonSql);
        
        if(!$preparedUpdatePersonSql){
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
            print_r($personData);
            DbTable::displayErrorMessage($preparedUpdatePersonSql, __FILE__, __FILE__, $updatePersonSql);
            return;
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
    
        $updatePesTrackerSql = substr($updatePesTrackerSql,0,-2) . " WHERE CNUM=( SELECT CNUM FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " WHERE lower(EMAIL_ADDRESS) = ? FETCH FIRST 1 ROWS ONLY ) ";
        $pesTrackerData[]    = strtolower($upesData->EMAIL_ADDRESS) ;
        
        $preparedUpdatePesTrackerSql = db2_prepare($GLOBALS['conn'], $updatePesTrackerSql);
        
        if(!$preparedUpdatePesTrackerSql){
            echo db2_stmt_error();
            echo db2_stmt_errormsg();
            print_r($pesTrackerData);
            DbTable::displayErrorMessage($preparedUpdatePesTrackerSql, __FILE__, __FILE__, $updatePesTrackerSql);
            return;
        }
        
        $rsPerson = db2_execute($preparedUpdatePersonSql,$personData);    
        if(!$rsPerson){
            DbTable::displayErrorMessage($rsPerson, __FILE__, __FILE__, $updatePersonSql);     
            db2_rollback($GLOBALS['conn']);
        }
        
        $rsPesTracker = db2_execute($preparedUpdatePesTrackerSql,$pesTrackerData);
        if(!$rsPesTracker){
            DbTable::displayErrorMessage($rsPesTracker, __FILE__, __FILE__, $updatePesTrackerSql);
            db2_rollback($GLOBALS['conn']);
        }
        
        echo "<br/> Updated PES_TRACKER and PERSON Records for Email:" . $upesData->EMAIL_ADDRESS;
        
        db2_commit($GLOBALS['conn']);
    }
} else {
    throw new \Exception('PES Application URL was missing.');
}