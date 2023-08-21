<?php
namespace vbac;

use itdq\DbTable;


class delegateTable extends DbTable {

    static function saveDelegate($selfCnum, $selfEmail, $delegateCnum,$delegateEmail){
        $sql = " INSERT INTO " . $GLOBALS['Db2Schema'] . "." . allTables::$DELEGATE;
        $sql.= " (CNUM,EMAIL_ADDRESS, DELEGATE_CNUM, DELEGATE_EMAIL ) ";
        $sql.=" VALUES ";
        $sql.= "('" . htmlspecialchars($selfCnum) . "','" . htmlspecialchars($selfEmail) . "'";
        $sql.= ",'" . htmlspecialchars($delegateCnum) . "','" . htmlspecialchars($delegateEmail) . "'";
        $sql.= ")";

        $rs = sqlsrv_query($GLOBALS['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }

    static function deleteDelegate($cnum, $delegateCnum){
        $sql = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$DELEGATE;
        $sql.= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";
        $sql.= " AND DELEGATE_CNUM='" . htmlspecialchars($delegateCnum) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

    function returnForDataTableS($cnum){
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;

        if(!$_SESSION['isPmo'] && !$_SESSION['isCdi']){
            $sql.= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";
        }


        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();

        while (($row=sqlsrv_fetch_array($rs))==true) {
            $display['MANAGER'] = $row['EMAIL_ADDRESS'];
            $display['DELEGATE'] = $row['DELEGATE_EMAIL'];

            $deleteButton  = "<button type='button' class='btn btn-default btn-xs btnDeleteDelegate btn-danger' aria-label='Left Align' ";
            $deleteButton .= "data-delegate='" .trim($row['DELEGATE_CNUM']) . "' ";
            $deleteButton .= "data-cnum='" .trim($row['CNUM']) . "' ";
            $deleteButton .= "data-toggle='tooltip' data-placement='top' title='Remove Delegate'";
            $deleteButton .= " > ";
            $deleteButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
            $deleteButton .= " </button> ";



            $display['delete'] = $deleteButton;

            $data[] = array($display['MANAGER'], $display['DELEGATE'],$display['delete']);
        }

        var_dump($data);

        return $data;

    }

    static function delegatesFromCnum($fmCnum){
        $sql = " SELECT DELEGATE_EMAIL ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$DELEGATE ;
        $sql.= " WHERE CNUM='" . htmlspecialchars(trim($fmCnum)) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $ccAddresses = array();

        while(($row=sqlsrv_fetch_array($rs)==true)){
            $ccAddresses[] = trim($row['DELEGATE_EMAIL']);
        }

        return !empty($ccAddresses) ? $ccAddresses: false;

    }

    static function delegatesFromEmail($fmEmail){
        $sql = " SELECT DELEGATE_EMAIL ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$DELEGATE ;
        $sql.= " WHERE EMAIL_ADDRESS='" . htmlspecialchars(trim($fmEmail)) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $ccAddresses = array();

        while(($row=sqlsrv_fetch_array($rs))==true){
            $ccAddresses[] = trim($row['DELEGATE_EMAIL']);
        }

        return !empty($ccAddresses) ? $ccAddresses: false;

    }

    static function allDelegates(){
        $sql = " SELECT distinct CNUM, DELEGATE_EMAIL ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$DELEGATE ;

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $allDelegates = array();

        while(($row=sqlsrv_fetch_array($rs))==true){

            $allDelegates[$row['CNUM']][] = $row['DELEGATE_EMAIL'];
            // = isset($allDelegates[$row['CNUM']]) ? array_merge($allDelegates[$row['CNUM']],array($row['DELEGATE_EMAIL'])) : $row['DELEGATE_EMAIL'] ;
        }

        return !empty($allDelegates) ? $allDelegates: false;

    }

}