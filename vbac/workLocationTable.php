<?php
namespace vbac;

use itdq\DbTable;

class workLocationTable extends DbTable{

    static function deleteLocation($id){
        $sql = " DELETE FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$STATIC_LOCATIONS;
        $sql.= " WHERE ID='" . htmlspecialchars($id) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'],$sql);

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
        while(($row=sqlsrv_fetch_array($rs))==true){
            $row = array_map('trim',$row);
            $rowWithIcons = $this->addIcons($row);
            $data[] = $rowWithIcons;
        }
        return $data;
    }

    function addIcons($row){
        $onShore = $row['ONSHORE'];
        switch($onShore) {
            case '0':
            case 0:
                $onShore = 'No';
                break;
            case '1':
            case 1:
                $onShore = 'Yes';
                break;
            default:
                break;
        }
        $row['ONSHORE'] = $onShore;

        $CBCInPlace = $row['CBC_IN_PLACE'];
        switch($CBCInPlace) {
            case '':
                $CBCInPlace = 'No';
                break;
            default:
                break;
        }
        $row['CBC_IN_PLACE'] = $CBCInPlace;

        $id = $row['ID'];
        $country = $row['COUNTRY'];

        $editButton  = "<button type='button' class='btn btn-default btn-xs btnEditLocation' aria-label='Left Align' ";
        $editButton .= "data-id='" .$id . "' ";
        $editButton .= "data-country='" .$country . "' ";
        $editButton .= "data-city='" .$row['CITY'] . "' ";
        $editButton .= "data-address='" .$row['ADDRESS'] . "' ";
        $editButton .= "data-onshore='" .$onShore . "' ";
        $editButton .= "data-cbcinplace='" .$CBCInPlace . "' ";
        $editButton .= "data-toggle='tooltip' data-placement='top' title='Edit Location'";
        $editButton .= " > ";
        $editButton .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $editButton .= " </button> ";

        $deleteButton  = "<button type='button' class='btn btn-default btn-xs btnDeleteLocation btn-danger' aria-label='Left Align' ";
        $deleteButton .= "data-id='" .$id . "' ";
        $deleteButton .= "data-toggle='tooltip' data-placement='top' title='Remove Location'";
        $deleteButton .= " > ";
        $deleteButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
        $deleteButton .= " </button> ";

        $countryWithIcon = $editButton . $deleteButton . " &nbsp; " . $country;

        $row['COUNTRY'] = array('display'=>$countryWithIcon,'sort'=>$country);
        return $row;
    }



}