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

    static function nextAvailableSquadNumber() {
        $sql = " SELECT MAX(SQUAD_NUMBER) as SQUAD_NUMBER FROM " . $_SESSION['Db2Schema'] . "." . allTables::$AGILE_SQUAD ;

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($rs);
        return !empty($row['SQUAD_NUMBER']) ? (int)($row['SQUAD_NUMBER'])+1 : 1;

    }

    function returnAsArray(){
        $sql = " SELECT * ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
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
        $squadNumberNumberWithIcon .= " data-toggle='tooltip' data-placement='top' title='Edit Tribe'";
        $squadNumberNumberWithIcon .= " > ";
        $squadNumberNumberWithIcon .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $squadNumberNumberWithIcon .= " </button> ";
        $squadNumberNumberWithIcon .= "&nbsp; " . $squadNumber;

        $row['SQUAD_NUMBER'] = array('display'=>$squadNumberNumberWithIcon,'sort'=>$squadNumber);
        return $row;
    }


}