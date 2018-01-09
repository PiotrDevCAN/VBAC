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
            case null:
                $row[personRecord::FIELD_PES_STATUS]  = "<button type='button' class='btn btn-default btn-xs btnPesInitiate accessRestrict accessPmo accessFm' ";
                $row[personRecord::FIELD_PES_STATUS]  .= "aria-label='Left Align' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-cnum='" .trim($row[personRecord::FIELD_CNUM]) . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-pesstatus='null' ";
                $row[personRecord::FIELD_PES_STATUS] .= " > ";
                $row[personRecord::FIELD_PES_STATUS] .= "<span class='glyphicon glyphicon-plane ' aria-hidden='true'></span>";
                $row[personRecord::FIELD_PES_STATUS] .= "</button>";
                break;
            case personRecord::PES_STATUS_EXCEPTION:
            case personRecord::PES_STATUS_DECLINED;
            case personRecord::PES_STATUS_FAILED;
            case personRecord::PES_STATUS_INITIATED;
            case personRecord::PES_STATUS_REMOVED;
                $status = trim($row[personRecord::FIELD_PES_STATUS]);
                $row[personRecord::FIELD_PES_STATUS]  = "<button type='button' class='btn btn-default btn-xs btnPesStatus' aria-label='Left Align' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-cnum='" .trim($row[personRecord::FIELD_CNUM]) . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-notesid='" .trim($row[personRecord::FIELD_NOTES_ID]) . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-pesdaterequested='" .trim($row[personRecord::FIELD_PES_DATE_REQUESTED]) . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-pesrequestor='" .trim($row[personRecord::FIELD_PES_REQUESTOR]) . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-pesstatus='" .$status . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " > ";
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
        $result =  self::setPesStatus($cnum,personRecord::PES_STATUS_INITIATED, $requestor);
        return $result;
    }

    function setPesStatus($cnum=null,$status=null,$requestor=null){
        if(!$cnum){
            throw new \Exception('No CNUM provided in ' . __METHOD__);
        }

        $status = empty($status) ? personRecord::PES_STATUS_REQUESTED : $status;

        switch ($status) {
            case personRecord::PES_STATUS_INITIATED:
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


    static function isManager($emailAddress){
        if (empty($emailAddress)) {
            return false;
        }

        $sql = " SELECT FM_MANAGER_FLAG FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . db2_escape_string(strtoupper($emailAddress)) . "' ";

        $resultSet = db2_exec($_SESSION['conn'], $sql);

        if(!$resultSet){
            DbTable::displayErrorMessage($resultSet, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($resultSet);

        $flagValue = strtoupper(substr($row['FM_MANAGER_FLAG'],0));

        return $flagValue=='Y';



    }




}