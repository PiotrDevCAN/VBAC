<?php
namespace vbac;

use itdq\DbTable;

/*
 *
 * CREATE TABLE VBAC.AGILE_TRIBE ( TRIBE_NUMBER CHAR(10) NOT NULL, TRIBE_NAME CHAR(70) NOT NULL, TRIBE_LEADER CHAR(60) ) IN userspace1;
 * ALTER TABLE VBAC.AGILE_TRIBE ADD CONSTRAINT TRIBE_PK PRIMARY KEY (TRIBE_NUMBER );
 */


class AgileTribeTable extends DbTable{

    static function deleteTribe($id){
        $sql = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE;
        $sql.= " WHERE TRIBE_NUMBER = '" . htmlspecialchars($id) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
    }

    static function nextAvailableTribeNumber($version=null) {

        // THIS HAS BEEN CHANGED
        $table = $version=='Original' ? allTables::$AGILE_TRIBE : allTables::$AGILE_TRIBE_OLD;
        // $sql = " SELECT MAX(TRIBE_NUMBER) AS TRIBE_NUMBER FROM ( ";
        // $sql.= "      SELECT MAX(TRIBE_NUMBER) as TRIBE_NUMBER FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE;
        // $sql.= "      UNION ";
        // $sql.= "      SELECT MAX(TRIBE_NUMBER) as TRIBE_NUMBER FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE_OLD;
        // $sql.= " ) ";
        $sql = " SELECT MAX(TRIBE_NUMBER) AS TRIBE_NUMBER FROM ". $GLOBALS['Db2Schema'] . "." . allTables::$AGILE_TRIBE;
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC);
        return !empty($row['TRIBE_NUMBER']) ? (int)($row['TRIBE_NUMBER'])+1 : 1;

    }

    function returnAsArray($withButtons = true){
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $data = false;
        while ($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC)){
            $row = array_map('trim', $row);
            $rowWithIcons = $withButtons ?  $this->addIcons($row) : $row ;
            $data[] = $rowWithIcons;
        }
        return $data;
    }

    function addIcons($row){

        $tribeNumber = $row['TRIBE_NUMBER'];

        $editButton  = "<button type='button' class='btn btn-default btn-xs btnEditTribe' aria-label='Left Align' ";
        $editButton .= "data-tribenumber='" .$tribeNumber . "' ";
        $editButton .= "data-tribename='" .$row['TRIBE_NAME'] . "' ";
        $editButton .= "data-tribeleader='" .$row['TRIBE_LEADER'] . "' ";
        $editButton .= "data-iterationmgr='" .$row['ITERATION_MGR'] . "' ";
        $editButton .= "data-organisation='" .$row['ORGANISATION'] . "' ";
        $editButton .= "data-toggle='tooltip' data-placement='top' title='Edit Tribe'";
        $editButton .= " > ";
        $editButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $editButton .= " </button> ";

        $deleteButton  = "<button type='button' class='btn btn-default btn-xs btnDeleteTribe btn-danger' aria-label='Left Align' ";
        $deleteButton .= "data-tribenumber='" .$tribeNumber . "' ";
        $deleteButton .= "data-tribename='" .$row['TRIBE_NAME'] . "' ";
        $deleteButton .= "data-tribeleader='" .$row['TRIBE_LEADER'] . "' ";
        $deleteButton .= "data-iterationmgr='" .$row['ITERATION_MGR'] . "' ";
        $deleteButton .= "data-organisation='" .$row['ORGANISATION'] . "' ";
        $deleteButton .= "data-toggle='tooltip' data-placement='top' title='Delete Tribe'";
        $deleteButton .= " > ";
        $deleteButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
        $deleteButton .= " </button> ";

        $idWithIcon = $editButton . $deleteButton . " &nbsp; Tribe " . $tribeNumber;

        $row['TRIBE_NUMBER'] = array('display'=>$idWithIcon,'sort'=>$tribeNumber);
        return $row;
    }



}