<?php
namespace vbac;

use itdq\DbTable;

class bandMappingTable extends DbTable{

    static function deleteBandMapping($id){
        $sql = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$BUSINESS_TITLE_MAPPING;
        $sql.= " WHERE BUSINESS_TITLE='" . htmlspecialchars($id) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        return true;
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
        while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){
            $row = array_map('trim', $row);
            $rowWithIcons = $this->addIcons($row);
            $data[] = $rowWithIcons;
        }
        return $data;
    }

    function addIcons($row){
        $id = $row['BUSINESS_TITLE'];
        $band = $row['BAND'];

        $editButton  = "<button type='button' class='btn btn-default btn-xs btnEditBandMapping' aria-label='Left Align' ";
        $editButton .= "data-id='" .$id . "' ";
        $editButton .= "data-band='" .$band . "' ";
        $editButton .= "data-toggle='tooltip' data-placement='top' title='Edit Band Mapping'";
        $editButton .= " > ";
        $editButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $editButton .= " </button> ";

        $deleteButton  = "<button type='button' class='btn btn-default btn-xs btnDeleteBandMapping btn-danger' aria-label='Left Align' ";
        $deleteButton .= "data-id='" .$id . "' ";
        $deleteButton .= "data-band='" .$band . "' ";
        $deleteButton .= "data-toggle='tooltip' data-placement='top' title='Remove Band Mapping'";
        $deleteButton .= " > ";
        $deleteButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
        $deleteButton .= " </button> ";

        $idWithIcon = $editButton . $deleteButton . " &nbsp; " . $id;

        $row['BUSINESS_TITLE'] = array('display'=>$idWithIcon,'sort'=>$id);
        return $row;
    }



}