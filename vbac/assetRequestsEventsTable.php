<?php
namespace vbac;

use itdq\DbTable;
use vbac\allTables;


class assetRequestsEventsTable extends DbTable{

    const EVENT_CREATED             = assetRequestRecord::STATUS_CREATED;
    const EVENT_VBAC_APPROVED       = assetRequestRecord::STATUS_APPROVED;
    const EVENT_PRE_REQ_CREATED     = 'Pre-req Created';
    const EVENT_PRE_REQ_APPROVED    = 'Pre-req Approved';
    const EVENT_VBAC_REJECTED       = assetRequestRecord::STATUS_REJECTED;
    const EVENT_EXPORTED            = assetRequestRecord::STATUS_EXPORTED;
    const EVENT_PROVISIONED         = assetRequestRecord::STATUS_PROVISIONED;
    const EVENT_DEVARBED            = 'devarbed';
    const EVENT_ORDERIT_RAISED      = assetRequestRecord::STATUS_RAISED_ORDERIT;
    const EVENT_ORDERIT_RESPONDED   = 'orderIt Responded';
    const EVENT_ORDERIT_APPROVED    = assetRequestRecord::STATUS_ORDERIT_APPROVED;
    const EVENT_ORDERIT_REJECTED    = assetRequestRecord::STATUS_ORDERIT_REJECTED;
    const EVENT_ORDERIT_CANCELLED   = assetRequestRecord::STATUS_ORDERIT_CANCELLED;

    const EVENT_MISC                ='misc';

    const LOG_TYPE_EVENT = 'event';
    const LOG_TYPE_STATUS = 'status';

    private $preparedInsertEventStatement;

    private function prepareInsertStatement($data){
        if(!empty($this->preparedInsertEventStatement)){
            return $this->preparedInsertEventStatement;
        }

        $initator = empty($_SESSION['ssoEmail']) ? 'Unknown' : $_SESSION['ssoEmail'];

        $sql = " INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS_EVENTS ;
        $sql.= " ( REQUEST_REFERENCE, EVENT, OCCURED, INITIATED_BY ) ";
        $sql.= " values ";
        $sql.= "( ?, ?, CURRENT_TIMESTAMP, '$initator') ";

        $rs = sqlsrv_prepare($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->preparedInsertEventStatement = $rs;
        return $this->preparedInsertEventStatement;

    }

    function logEventForRequest($event, $requestReference){
        $data = array($requestReference, $event);
        $preparedStmt = $this->prepareInsertStatement($data);
        $rs = sqlsrv_execute($preparedStmt);
        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }
    
    static function logEventForRequestWithDate($event, $requestReference, $date){
        $initator = empty($_SESSION['ssoEmail']) ? 'Unknown' : $_SESSION['ssoEmail'];
        
        $sql = " INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$ASSET_REQUESTS_EVENTS ;
        $sql.= " ( REQUEST_REFERENCE, EVENT, OCCURED, INITIATED_BY ) ";
        $sql.= " values ";
        $sql.= "( ?, ?, ?, '$initator') ";
        
        $data = array($requestReference, $event, $date);
        $preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);   
        $rs = sqlsrv_execute($preparedStmt);
        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }


}