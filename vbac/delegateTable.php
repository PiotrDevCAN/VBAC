<?php
namespace vbac;

use itdq\DbTable;


class delegateTable extends DbTable {

    static function saveDelegate($selfCnum, $selfEmail, $delegateCnum,$delegateEmail){
        $sql = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . allTables::$DELEGATE;
        $sql.= " (CNUM,EMAIL_ADDRESS, DELEGATE_CNUM, DELEGATE_EMAIL ) ";
        $sql.=" VALUES ";
        $sql.= "('" . db2_escape_string($selfCnum) . "','" . db2_escape_string($selfEmail) . "'";
        $sql.= ",'" . db2_escape_string($delegateCnum) . "','" . db2_escape_string($delegateEmail) . "'";
        $sql.= ")";

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        return true;
    }

    static function deleteDelegate($cnum, $delegateCnum){
        $sql = " DELETE FROM " . $_SESSION['Db2Schema'] . "." . allTables::$DELEGATE;
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";
        $sql.= " AND DELEGATE_CNUM='" . db2_escape_string($delegateCnum) . "' ";

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

    function returnForDataTableS($cnum){
        $sql = " SELECT * ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";


        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $data = array();

        while (($row=db2_fetch_assoc($rs))==true) {
            $display['EMAIL'] = $row['DELEGATE_EMAIL'];

            $deleteButton  = "<button type='button' class='btn btn-default btn-xs btnDeleteDelegate btn-danger' aria-label='Left Align' ";
            $deleteButton .= "data-delegate='" .trim($row['DELEGATE_CNUM']) . "' ";
            $deleteButton .= "data-cnum='" .trim($row['CNUM']) . "' ";
            $deleteButton .= "data-toggle='tooltip' data-placement='top' title='Remove Delegate'";
            $deleteButton .= " > ";
            $deleteButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
            $deleteButton .= " </button> ";



            $display['delete'] = $deleteButton;

            $data[] = array($display['EMAIL'], $display['delete']);
        }

        var_dump($data);

        return $data;

    }

}