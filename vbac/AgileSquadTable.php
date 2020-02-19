<?php
namespace vbac;

use itdq\DbTable;

/*
 *
 * CREATE TABLE VBAC.AGILE_SQUAD ( SQUAD_NUMBER NUMERIC(5) NOT NULL, SQUAD_TYPE CHAR(60) NOT NULL, TRIBE_NUMBER CHAR(10) NOT NULL, SHIFT CHAR(1) NOT NULL, SQUAD_LEADER CHAR(50) ) IN userspace1;
 * ALTER TABLE VBAC.AGILE_SQUAD ADD CONSTRAINT Squad_PK PRIMARY KEY (SQUAD_NUMBER ) ENFORCED;
 *
 * ALTER TABLE "VBAC"."AGILE_SQUAD" ADD COLUMN "SQUAD_NAME" CHAR(60);

 *
 *
 *
 */


class AgileSquadTable extends DbTable{

    static function nextAvailableSquadNumber($version=null) {

        $table = $version=='Original' ? allTables::$AGILE_SQUAD : allTables::$AGILE_SQUAD_NEW;

        $sql = " SELECT MAX(SQUAD_NUMBER) as SQUAD_NUMBER FROM " . $_SESSION['Db2Schema'] . "." . $table ;

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($rs);
        return !empty($row['SQUAD_NUMBER']) ? (int)($row['SQUAD_NUMBER'])+1 : 1;

    }

    function returnAsArray($version=null){
        $tribeTable = $version=='Original' ? allTables::$AGILE_TRIBE : allTables::$AGILE_TRIBE_NEW;

        $sql = " SELECT S.*, T.ORGANISATION ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName . " as S ";
        $sql.= " LEFT JOIN ". $_SESSION['Db2Schema'] . "." . $tribeTable . " as T ";
        $sql.= " ON S.TRIBE_NUMBER = T.TRIBE_NUMBER ";
        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        $data = false;
        while(($row=db2_fetch_assoc($rs))==true){
            $row = array_map('trim',$row);
            $rowWithIcons = $this->addIcons($row);
            $data[] = $rowWithIcons;
        }
        return $data;
    }

    function addIcons($row){

        $squadNumber = $row['SQUAD_NUMBER'];

        $squadNumberNumberWithIcon  = "<button type='button' class='btn btn-default btn-xs btnEditSquad' aria-label='Left Align' ";
        $squadNumberNumberWithIcon .= "data-squadnumber='" .$squadNumber . "' ";
        $squadNumberNumberWithIcon .= "data-squadname='" .$row['SQUAD_NAME'] . "' ";
        $squadNumberNumberWithIcon .= "data-squadleader='" .$row['SQUAD_LEADER'] . "' ";
        $squadNumberNumberWithIcon .= "data-squadtype='" .$row['SQUAD_TYPE'] . "' ";
        $squadNumberNumberWithIcon .= "data-shift='" .$row['SHIFT'] . "' ";
        $squadNumberNumberWithIcon .= "data-tribenumber='" .$row['TRIBE_NUMBER'] . "' ";
        $squadNumberNumberWithIcon .= "data-organisation='" .$row['ORGANISATION'] . "' ";
        $squadNumberNumberWithIcon .= " data-toggle='tooltip' data-placement='top' title='Edit Tribe'";
        $squadNumberNumberWithIcon .= " > ";
        $squadNumberNumberWithIcon .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $squadNumberNumberWithIcon .= " </button> ";
        $squadNumberNumberWithIcon .= "&nbsp; " . $squadNumber;

        $row['SQUAD_NUMBER'] = array('display'=>$squadNumberNumberWithIcon,'sort'=>$squadNumber);
        return $row;
    }


    static function getSquadDetails($squadNumber){
        $sql = " SELECT S.SQUAD_NUMBER,S.SQUAD_NAME, S.SQUAD_TYPE, S.SQUAD_LEADER, S.TRIBE_NUMBER, T.TRIBE_NAME, T.TRIBE_LEADER ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$AGILE_SQUAD . " AS S ";
        $sql.= " LEFT JOIN " . $_SESSION['Db2Schema'] . "." . allTables::$AGILE_TRIBE . " AS T ";
        $sql.= " ON S.TRIBE_NUMBER = T.TRIBE_NUMBER ";
        $sql.= " WHERE S.SQUAD_NUMBER = " . db2_escape_string($squadNumber);

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($rs);

        return $row;

    }


}