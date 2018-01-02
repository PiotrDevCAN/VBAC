<?php
namespace vbac;

use itdq\DbTable;

class personTable extends DbTable {

    function returnAsArray(){
        $data = array();


        $sql  = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName ;

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while(($row=db2_fetch_array($rs))==true){
                 $rowWithButtonsAdded = $this->addButtons($row);
                 $data[] = $rowWithButtonsAdded;
            }
        }
        return $data;
    }

    function addButtons($row){
        return $row;
    }



}