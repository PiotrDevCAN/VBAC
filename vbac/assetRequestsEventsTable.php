<?php
namespace vbac;

use itdq\DbTable;
use vbac\allTables;


class assetRequestsEventsTable extends DbTable{

    const EVENT_CREATED             = 'created';
    const EVENT_VBAC_APPROVED       = 'vbac approved';
    const EVENT_VBAC_REJECTED       = 'vbac rejected';
    const EVENT_EXPORTED            = 'exported';
    const EVENT_ORDERIT_APPROVED    = 'orderit approved';
    const EVENT_ORDERIT_REJECTED    = 'orderit rejected';

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