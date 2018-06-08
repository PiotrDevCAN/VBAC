<?php
namespace vbac;

use itdq\DbTable;
use vbac\allTables;


class assetRequestsEventsTable extends DbTable{

    const EVENT_CREATED             = assetRequestRecord::$STATUS_CREATED;
    const EVENT_VBAC_APPROVED       = assetRequestRecord::$STATUS_APPROVED;
    const EVENT_VBAC_REJECTED       = assetRequestRecord::$STATUS_REJECTED;
    const EVENT_EXPORTED            = assetRequestRecord::$STATUS_EXPORTED;
    const EVENT_PROVISIONED         = assetRequestRecord::$STATUS_PROVISIONED;
    const EVENT_DEVARBED            = 'devarbed';
    const EVENT_ORDERIT_RAISED      = assetRequestRecord::$STATUS_RAISED_ORDERIT;
    const EVENT_ORDERIT_APPROVED    = assetRequestRecord::$STATUS_ORDERIT_APPROVED;
    const EVENT_ORDERIT_REJECTED    = assetRequestRecord::$STATUS_ORDERIT_REJECTED;
    const EVENT_ORDERIT_CANCELLED   = assetRequestRecord::$STATUS_ORDERIT_CANCELLED;

    const EVENT_MISC                ='misc';

    const LOG_TYPE_EVENT = 'event';
    const LOG_TYPE_STATUS = 'status';

    private $preparedInsertEventStatement;

    private function prepareInsertStatement(){
        if(!empty($this->preparedInsertEventStatement)){
            return $this->preparedInsertEventStatement;
        }

        $initator = empty($_SESSION['ssoEmail']) ? 'Unknown' : $_SESSION['ssoEmail'];

        $sql = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . allTables::$ASSET_REQUESTS_EVENTS ;
        $sql.= " ( REQUEST_REFERENCE, EVENT, OCCCURED, INITIATED_BY ) ";
        $sql.= " values ";
        $sql.= "( ?, ?, current timestamp, $initator) ";

        $rs = db2_prepare($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $this->preparedInsertEventStatement = $rs;
        return $this->preparedInsertEventStatement;

    }

    function logEventForRequest($event, $requestReference){
        $preparedStmt = $this->preparedInsertEventStatement;
        $data = array($requestReference, $event);
        $rs = db2_execute($preparedStmt,$data);
        if(!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }


}