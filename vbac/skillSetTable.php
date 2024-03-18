<?php
namespace vbac;

use itdq\DbTable;

class skillSetTable extends DbTable{

    static function deleteSkillSet($id){
        $sql = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_SKILLSETS;
        $sql.= " WHERE SKILLSET_ID = '" . htmlspecialchars($id) . "' ";

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
        $id = $row['SKILLSET_ID'];
        $skillSet = $row['SKILLSET'];

        $editButton  = "<button type='button' class='btn btn-default btn-xs btnEditSkillset' aria-label='Left Align' ";
        $editButton .= "data-id='" .$id . "' ";
        $editButton .= "data-skillset='" .$skillSet . "' ";
        $editButton .= "data-toggle='tooltip' data-placement='top' title='Edit Skillset'";
        $editButton .= " > ";
        $editButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $editButton .= " </button> ";

        $deleteButton  = "<button type='button' class='btn btn-default btn-xs btnDeleteSkillset btn-danger' aria-label='Left Align' ";
        $deleteButton .= "data-id='" .$id . "' ";
        $deleteButton .= "data-skillset='" .$skillSet . "' ";
        $deleteButton .= "data-toggle='tooltip' data-placement='top' title='Remove Skillset'";
        $deleteButton .= " > ";
        $deleteButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
        $deleteButton .= " </button> ";

        $idWithIcon = $editButton . $deleteButton . " &nbsp; " . $id;

        $row['SKILLSET_ID'] = array('display'=>$idWithIcon,'sort'=>$id);
        return $row;
    }
}