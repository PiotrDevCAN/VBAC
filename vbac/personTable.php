<?php
namespace vbac;

use itdq\DbTable;
use itdq\AuditTable;

class personTable extends DbTable {

    private $preparedRevalidationStmt;
    private $preparedRevalidationLeaverStmt;
    private $preparedUpdateBluepagesFields;


    static function getNextVirtualCnum(){
        $sql  = " SELECT CNUM FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM LIKE '%XXX' or CNUM LIKE '%xxx' or CNUM LIKE '%999 ";
        $sql .= " order by CNUM desc ";
        $sql .= " OPTIMIZE FOR 1 ROW ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $topRow = db2_fetch_array($rs);
        if(isset($topRow[0])){
            $thisCnum = substr($topRow[0],1,6);
            $next = $thisCnum+1;
            $nextVirtualCnum = 'V' . substr('000000' . $next ,5) . 'XXX';
        } else {
            $nextVirtualCnum = 'V00001XXX';
        }


        return $nextVirtualCnum;

    }


    function returnAsArray(){
        $data = array();

        $isFM   = personTable::isManager($_SESSION['ssoEmail']);
        $myCnum = personTable::myCnum();

        $predicate = $isFM ? " FM_CNUM='" . db2_escape_string(trim($myCnum)) . "' " : " 1=1 "; // FM Can only see their own people.

        $sql  = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql .= " WHERE 1=1 AND " . $predicate;

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
        // save some fields before we change the,
        $notesId = trim($row[personRecord::FIELD_NOTES_ID]);
        $cnum = trim($row[personRecord::FIELD_CNUM]);
        $flag = $row[personRecord::FIELD_FM_FLAG];
        $status = empty(trim($row[personRecord::FIELD_PES_STATUS])) ? personRecord::PES_STATUS_NOT_REQUESTED : trim($row[personRecord::FIELD_PES_STATUS]) ;
        // FM_FLAG
        if($_SESSION['isPmo'] || $_SESSION['isCdi']){
            if(strtoupper(substr($flag,0,1))=='N' || empty($flag)){
                $row[personRecord::FIELD_FM_FLAG]  = "<button type='button' class='btn btn-default btn-xs btnSetFmFlag' aria-label='Left Align' ";
                $row[personRecord::FIELD_FM_FLAG] .= "data-cnum='" .$cnum . "' ";
                $row[personRecord::FIELD_FM_FLAG] .= "data-notesid='" .$notesId . "' ";
                $row[personRecord::FIELD_FM_FLAG] .= "data-fmflag='Yes' ";
                $row[personRecord::FIELD_FM_FLAG] .= " > ";
                $row[personRecord::FIELD_FM_FLAG] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row[personRecord::FIELD_FM_FLAG] .= " </button> ";
            } elseif (strtoupper(substr($flag,0,1)=='Y')){
                $row[personRecord::FIELD_FM_FLAG]  = "<button type='button' class='btn btn-default btn-xs btnSetFmFlag' aria-label='Left Align' ";
                $row[personRecord::FIELD_FM_FLAG] .= "data-cnum='" .$cnum . "' ";
                $row[personRecord::FIELD_FM_FLAG] .= "data-notesid='" .$notesId . "' ";
                $row[personRecord::FIELD_FM_FLAG] .= "data-fmflag='No' ";
                $row[personRecord::FIELD_FM_FLAG] .= " > ";
                $row[personRecord::FIELD_FM_FLAG] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row[personRecord::FIELD_FM_FLAG] .= " </button> ";
            }
            $row[personRecord::FIELD_FM_FLAG] .= $flag;
        }


        // Notesid

        $row[personRecord::FIELD_NOTES_ID]  = "<button type='button' class='btn btn-default btn-xs btnEditPerson' aria-label='Left Align' ";
        $row[personRecord::FIELD_NOTES_ID] .= "data-cnum='" .$cnum . "'";
        $row[personRecord::FIELD_NOTES_ID] .= " > ";
        $row[personRecord::FIELD_NOTES_ID] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $row[personRecord::FIELD_NOTES_ID] .= " </button> ";
        $row[personRecord::FIELD_NOTES_ID] .= $notesId;

        switch ($status) {
            case personRecord::PES_STATUS_NOT_REQUESTED:
            case null:
                $row[personRecord::FIELD_PES_STATUS]  = "<button type='button' class='btn btn-default btn-xs btnPesInitiate accessRestrict accessPmo accessFm' ";
                $row[personRecord::FIELD_PES_STATUS]  .= "aria-label='Left Align' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-cnum='" .$cnum . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-pesstatus='$status' ";
                $row[personRecord::FIELD_PES_STATUS] .= " > ";
                $row[personRecord::FIELD_PES_STATUS] .= "<span class='glyphicon glyphicon-plane ' aria-hidden='true'></span>";
                $row[personRecord::FIELD_PES_STATUS] .= "</button>&nbsp;";
                $row[personRecord::FIELD_PES_STATUS] .= $status;
                break;
            case personRecord::PES_STATUS_EXCEPTION:
            case personRecord::PES_STATUS_DECLINED;
            case personRecord::PES_STATUS_FAILED;
            case personRecord::PES_STATUS_INITIATED;
            case personRecord::PES_STATUS_REMOVED;
                $row[personRecord::FIELD_PES_STATUS]  = "<button type='button' class='btn btn-default btn-xs btnPesStatus' aria-label='Left Align' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-cnum='" .$cnum . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-notesid='" . $notesId . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-pesdaterequested='" .trim($row[personRecord::FIELD_PES_DATE_REQUESTED]) . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-pesrequestor='" .trim($row[personRecord::FIELD_PES_REQUESTOR]) . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-pesstatus='" .$status . "' ";
                $row[personRecord::FIELD_PES_STATUS] .= " > ";
                $row[personRecord::FIELD_PES_STATUS] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row[personRecord::FIELD_PES_STATUS] .= "</button>&nbsp;";
                $row[personRecord::FIELD_PES_STATUS] .= $status;
            break;
            case $row[personRecord::FIELD_NOTES_ID]:
                $row[personRecord::FIELD_NOTES_ID]  = "<button type='button' class='btn btn-default btn-xs btnEditPerson' aria-label='Left Align' ";
                $row[personRecord::FIELD_NOTES_ID] .= "data-cnum='" .$cnum. "'";
                $row[personRecord::FIELD_NOTES_ID] .= " > ";
                $row[personRecord::FIELD_NOTES_ID] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row[personRecord::FIELD_NOTES_ID] .= $notesId;
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

    function setFmFlag($cnum,$flag){
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET FM_MANAGER_FLAG='"  . db2_escape_string($flag) . "' ";
        $sql .= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";

        $result = db2_exec($_SESSION['conn'], $sql);

        if(!$result){
            DbTable::displayErrorMessage($result, __CLASS__,__METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Set FM_MANAGER_FLAG to $flag for $cnum",AuditTable::RECORD_TYPE_AUDIT);

        return true;
    }


    static function isManager($emailAddress){
         if(isset($_SESSION['isFm'])) {
            return $_SESSION['isFm'];
        }

        if (empty($emailAddress)) {
            return false;
        }

        $sql = ' SELECT FM_MANAGER_FLAG FROM "' . $_SESSION['Db2Schema'] . '".' . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . db2_escape_string(strtoupper($emailAddress)) . "' ";

        $resultSet = db2_exec($_SESSION['conn'], $sql);

        if(!$resultSet){
            DbTable::displayErrorMessage($resultSet, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($resultSet);
        $flagValue = strtoupper(substr(trim($row['FM_MANAGER_FLAG']),0,1));
        $_SESSION['isFm'] = ($flagValue=='Y');
        return $_SESSION['isFm'];
    }

    static function myCnum(){
        if(isset($_SESSION['myCnum'])) {
           return $_SESSION['myCnum'];
        }

        if (!isset($_SESSION['ssoEmail'])) {
            return false;
        }

        $sql = " SELECT CNUM FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . db2_escape_string(strtoupper(trim($_SESSION['ssoEmail']))) . "' ";

        $resultSet = db2_exec($_SESSION['conn'], $sql);

        if(!$resultSet){
            DbTable::displayErrorMessage($resultSet, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($resultSet);
        $myCnum = strtoupper(trim($row['CNUM']));
        $_SESSION['myCnum'] = $myCnum;
        return $_SESSION['myCnum'];
    }

    static function optionsForPreBoarded(){
        $availPreBoPredicate  = " CNUM LIKE '%xxx' AND PES_STATUS not like '%xxx' AND PES_STATUS not in (";
        $availPreBoPredicate .= " '" . personRecord::PES_STATUS_REMOVED . "' "; // Pre-boarded who haven't been boarded
        $availPreBoPredicate .= ",'" . personRecord::PES_STATUS_FAILED ."' ";
        $availPreBoPredicate .= " )";

        $sql =  " SELECT FIRST_NAME, LAST_NAME, EMAIL_ADDRESS, CNUM  FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE " . $availPreBoPredicate;
        $sql .= " ORDER BY FIRST_NAME, LAST_NAME ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if (!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__ , __METHOD__ , $sql);
            return false;
        }
        $options = array();
        while(($row=db2_fetch_assoc($rs))==true){
            $option = "<option value='" . trim($row['CNUM']) ."'>" . trim($row['FIRST_NAME']) ." " . $row['LAST_NAME']  . " (" . $row['EMAIL_ADDRESS'] .") " . "</option>";
            $options[] = $option;
        }

        return $options;

    }

    static function dataFromPreBoarder($cnum){
        $sql = " SELECT CTB_RTB,TT_BAU, WORK_STREAM, PES_DATE_REQUESTED, PES_DATE_RESPONDED, PES_REQUESTOR,  PES_STATUS, PES_STATUS_DETAILS, FM_CNUM, CONTRACTOR_ID_REQUIRED, CONTRACTOR_ID, LOB, OPEN_SEAT_NUMBER, ROLE_ON_THE_ACCOUNT, START_DATE, PROJECTED_END_DATE  ";
        $sql .= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM='" . db2_escape_string(trim($cnum)) . "' ";
        $sql .= " OPTIMIZE for 1 row ";
        $rs = db2_exec($_SESSION['conn'], $sql);

        if (!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__ , __METHOD__ , $sql);
            return false;
        }
        $data = db2_fetch_assoc($rs);
        return $data;
    }

    private function prepareRevalidationStmt(){
        if(empty($this->preparedRevalidationStmt)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET NOTES_ID=?, EMAIL_ADDRESS = ?,  REVALIDATION_STATUS='" . personRecord::REVALIDATED_FOUND . "' , REVALIDATION_DATE_FIELD = current date ";
            $sql .= " WHERE CNUM=? ";

            $this->preparedRevalidationStmt = db2_prepare($_SESSION['conn'], $sql);

            if(!$this->preparedRevalidationStmt){
                DbTable::displayErrorMessage($this->preparedRevalidationStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedRevalidationStmt;
    }

    private function prepareRevalidationLeaverStmt(){
        if(empty($this->preparedRevalidationLeaverStmt)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET REVALIDATION_STATUS='" . personRecord::REVALIDATED_LEAVER . "', REVALIDATION_DATE_FIELD = current date ";
            $sql .= " WHERE CNUM=?  AND ( REVALIDATION_STATUS is null or REVALIDATION_STATUS != '" . personRecord::REVALIDATED_LEAVER . "' )";

            echo "<br/>$sql";


            $this->preparedRevalidationLeaverStmt = db2_prepare($_SESSION['conn'], $sql);

            if(!$this->preparedRevalidationLeaverStmt){
                DbTable::displayErrorMessage($this->preparedRevalidationLeaverStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedRevalidationLeaverStmt;
    }

    function confirmRevalidation($notesId,$email,$cnum){
        $preparedStmt = $this->prepareRevalidationStmt();
        $data = array(trim($notesId),trim($email),trim($cnum));

        var_dump($data);

        $rs = db2_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, "prepared: revalidationStmt");
            return false;
        }
        return true;
    }


    function flagLeaver($cnum){
        $preparedStmt = $this->prepareRevalidationLeaverStmt();
        $data = array(trim($cnum));

        $rs = db2_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, "prepared: revalidationLeaverStmt");
            return false;
        }
        AuditTable::audit("Revalidation has found leaver: $cnum ",AuditTable::RECORD_TYPE_AUDIT);
        return true;
    }


}