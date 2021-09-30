<?php
namespace vbac;

use itdq\DbTable;

class WorkLocationTable extends DbTable{

    function returnAsArray(){
        $sql = " SELECT * ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $rs = db2_exec($GLOBALS['conn'], $sql);

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

        $onShore = $row['ONSHORE'];
        switch($onShore) {
            case '0':
                $onShore = 'No';
                break;
            case '1':
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

        $country = $row['COUNTRY'];

        $countryWithIcon  = "<button type='button' class='btn btn-default btn-xs btnEditLocation' aria-label='Left Align' ";
        $countryWithIcon .= "data-country='" .$country . "' ";
        $countryWithIcon .= "data-city='" .$row['CITY'] . "' ";
        $countryWithIcon .= "data-address='" .$row['ADDRESS'] . "' ";
        $countryWithIcon .= "data-onshore='" .$onShore . "' ";
        $countryWithIcon .= "data-cbcinplace='" .$CBCInPlace . "' ";
        $countryWithIcon .= "data-toggle='tooltip' data-placement='top' title='Edit Location'";
        $countryWithIcon .= " > ";
        $countryWithIcon .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $countryWithIcon .= " </button> ";
        $countryWithIcon .= "&nbsp; " . $country;

        $row['COUNTRY'] = array('display'=>$countryWithIcon,'sort'=>$country);
        return $row;
    }



}