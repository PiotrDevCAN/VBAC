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
        switch (trim($row[personRecord::FIELD_PES_STATUS])) {
            case personRecord::PES_STATUS_EXCEPTION:
            case personRecord::PES_STATUS_REJECTED:
            case personRecord::PES_STATUS_REQUESTED:
                $status = $row[personRecord::FIELD_PES_STATUS];
                $row[personRecord::FIELD_PES_STATUS]  = "<button type='button' class='btn btn-default btn-xs editPesStatus' aria-label='Left Align' data-cnum='" .$row[personRecord::FIELD_CNUM] . "' >";
                $row[personRecord::FIELD_PES_STATUS] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row[personRecord::FIELD_PES_STATUS] .= "</button>&nbsp;";
                $row[personRecord::FIELD_PES_STATUS] .= $status;
            break;
            default:

            break;
        }
        return $row;
    }

    function setPesRequested($cnum=null, $requestor=null){
        if(!$cnum){
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }
        $result =  self::setPesStatus($cnum,personRecord::PES_STATUS_REQUESTED, $requestor);
        return $result;
    }

    function setPesStatus($cnum=null,$status=null,$requestor=null){
        if(!$cnum){
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }

        $status = empty($status) ? personRecord::PES_STATUS_REQUESTED : $status;

        switch ($status) {
            case personRecord::PES_STATUS_REQUESTED:
                $requestor = empty($requestor) ? 'Unknown' : $requestor;
                $dateField = 'PES_DATE_REQUESTED';
                break;
            default:
                $dateField = 'PES_DATE_RESPONDED';
            break;
        }
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET $dateField = current date, PES_STATUS='" . db2_escape_string($status)  . "' ";
        $sql .= empty($requestor) ? null : ", PES_REQUESTOR='" . db2_escape_string($requestor) . "' ";
        $sql .= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";


        try {
            $result = db2_exec($_SESSION['conn'], $sql);
        } catch (Exception $e) {
            var_dump($e);
        }

       if(!$result){
           DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
           return false;
       }
        return true;

    }




}