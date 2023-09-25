<?php
namespace vbac;

use itdq\DbTable;

/*
 *
 * CREATE TABLE VBAC.AGILE_TRIBE ( TRIBE_NUMBER CHAR(10) NOT NULL, TRIBE_NAME CHAR(70) NOT NULL, TRIBE_LEADER CHAR(60) ) IN userspace1;
 * ALTER TABLE VBAC.AGILE_TRIBE ADD CONSTRAINT TRIBE_PK PRIMARY KEY (TRIBE_NUMBER );
 */


class AgileTribeTable extends DbTable{

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

    function returnAsArray(){
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
            $rowWithIcons = $this->addIcons($row);
            $data[] = $rowWithIcons;
        }
        return $data;
    }

    function addIcons($row){

        $tribeNumber = $row['TRIBE_NUMBER'];

        $tribeNumberWithIcon  = "<button type='button' class='btn btn-default btn-xs btnEditTribe' aria-label='Left Align' ";
        $tribeNumberWithIcon .= "data-tribenumber='" .$tribeNumber . "' ";
        $tribeNumberWithIcon .= "data-tribename='" .$row['TRIBE_NAME'] . "' ";
        $tribeNumberWithIcon .= "data-tribeleader='" .$row['TRIBE_LEADER'] . "' ";
        $tribeNumberWithIcon .= "data-iterationmgr='" .$row['ITERATION_MGR'] . "' ";
        $tribeNumberWithIcon .= "data-organisation='" .$row['ORGANISATION'] . "' ";
        $tribeNumberWithIcon .= " data-toggle='tooltip' data-placement='top' title='Edit Tribe'";
        $tribeNumberWithIcon .= " > ";
        $tribeNumberWithIcon .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $tribeNumberWithIcon .= " </button> ";
        $tribeNumberWithIcon .= "&nbsp; Tribe " . $tribeNumber;

        $row['TRIBE_NUMBER'] = array('display'=>$tribeNumberWithIcon,'sort'=>$tribeNumber);
        return $row;
    }



}