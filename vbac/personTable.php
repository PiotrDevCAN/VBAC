<?php
namespace vbac;

use itdq\DbTable;
use itdq\AuditTable;
use itdq\Loader;

class personTable extends DbTable {

    private $preparedRevalidationStmt;
    private $preparedRevalidationLeaverStmt;
    private $preparedUpdateBluepagesFields;

    private $allNotesIdByCnum;


    static function getNextVirtualCnum(){
        $sql  = " SELECT CNUM FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM LIKE '%XXX' or CNUM LIKE '%xxx' or CNUM LIKE '%999' ";
        $sql .= " order by CNUM desc ";
        $sql .= " OPTIMIZE FOR 1 ROW ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $topRow = db2_fetch_array($rs);
        if(isset($topRow[0])){
            $thisCnum = substr($topRow[0],1,5);
            $next = $thisCnum+1;
            $nextVirtualCnum = 'V' . substr('000000' . $next ,-5) . 'XXX';
        } else {
            $nextVirtualCnum = 'V00001XXX';
        }


        return $nextVirtualCnum;

    }


    function returnAsArray(){
        $loader = new Loader();
        $this->allNotesIdByCnum = $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON);


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
            while(($row=db2_fetch_assoc($rs))==true){
                 // Only editable, if they're not a "pre-Boarder" who has now been boarded.
                $preparedRow = $this->prepareFields($row);
                $rowWithButtonsAdded =(substr($row['PES_STATUS_DETAILS'],0,7)=='Boarded') ? $preparedRow : $this->addButtons($preparedRow);
                $data[] = $rowWithButtonsAdded;
            }
        }
        return $data;
    }

    function  prepareFields($row){
        $preparedRow = array_filter(array_map('trim', $row));
        $fmNotesid = isset($this->allNotesIdByCnum[trim($row['FM_CNUM'])]) ? $this->allNotesIdByCnum[trim($row['FM_CNUM'])]  :  trim($row['FM_CNUM']);
        $preparedRow['FM_CNUM'] = $fmNotesid;
        return $preparedRow;
    }

    function addButtons($row){
        // save some fields before we change the,
        $notesId = trim($row['NOTES_ID']);
        $email   = trim($row['EMAIL_ADDRESS']);
        $cnum = trim($row['CNUM']);
        $flag = $row['FM_MANAGER_FLAG'];
        $status = empty(trim($row['PES_STATUS'])) ? personRecord::PES_STATUS_NOT_REQUESTED : trim($row['PES_STATUS']) ;
        // FM_MANAGER_FLAG
        if($_SESSION['isPmo'] || $_SESSION['isCdi']){
            if(strtoupper(substr($flag,0,1))=='N' || empty($flag)){
                $row['FM_MANAGER_FLAG']  = "<button type='button' class='btn btn-default btn-xs btnSetFmFlag' aria-label='Left Align' ";
                $row['FM_MANAGER_FLAG'] .= "data-cnum='" .$cnum . "' ";
                $row['FM_MANAGER_FLAG'] .= "data-notesid='" .$notesId . "' ";
                $row['FM_MANAGER_FLAG'] .= "data-fmflag='Yes' ";
                $row['FM_MANAGER_FLAG'] .= " > ";
                $row['FM_MANAGER_FLAG'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row['FM_MANAGER_FLAG'] .= " </button> ";
            } elseif (strtoupper(substr($flag,0,1)=='Y')){
                $row['FM_MANAGER_FLAG']  = "<button type='button' class='btn btn-default btn-xs btnSetFmFlag' aria-label='Left Align' ";
                $row['FM_MANAGER_FLAG'] .= "data-cnum='" .$cnum . "' ";
                $row['FM_MANAGER_FLAG'] .= "data-notesid='" .$notesId . "' ";
                $row['FM_MANAGER_FLAG'] .= "data-fmflag='No' ";
                $row['FM_MANAGER_FLAG'] .= " > ";
                $row['FM_MANAGER_FLAG'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                $row['FM_MANAGER_FLAG'] .= " </button> ";
            }
            $row['FM_MANAGER_FLAG'] .= $flag;
        }


        // Notesid

        $row['NOTES_ID']  = "<button type='button' class='btn btn-default btn-xs btnEditPerson' aria-label='Left Align' ";
        $row['NOTES_ID'] .= "data-cnum='" .$cnum . "'";
        $row['NOTES_ID'] .= " > ";
        $row['NOTES_ID'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
        $row['NOTES_ID'] .= " </button> ";
        $row['NOTES_ID'] .= $notesId;

        switch ($status) {
             case personRecord::PES_STATUS_NOT_REQUESTED:
             case null:
                 $row['PES_STATUS']  = "<button type='button' class='btn btn-default btn-xs btnPesInitiate accessRestrict accessPmo accessFm' ";
                 $row['PES_STATUS']  .= "aria-label='Left Align' ";
                 $row['PES_STATUS'] .= " data-cnum='" .$cnum . "' ";
                 $row['PES_STATUS'] .= " data-pesstatus='$status' ";
                 $row['PES_STATUS'] .= " > ";
                 $row['PES_STATUS'] .= "<span class='glyphicon glyphicon-plane ' aria-hidden='true'></span>";
                 $row['PES_STATUS'] .= "</button>&nbsp;";
                 $row['PES_STATUS'] .= $status;
                 break;
             case personRecord::PES_STATUS_CLEARED:
             case personRecord::PES_STATUS_EXCEPTION:
             case personRecord::PES_STATUS_DECLINED;
             case personRecord::PES_STATUS_FAILED;
             case personRecord::PES_STATUS_INITIATED;
             case personRecord::PES_STATUS_REMOVED;
                 $row['PES_STATUS']  = "<button type='button' class='btn btn-default btn-xs btnPesStatus' aria-label='Left Align' ";
                 $row['PES_STATUS'] .= " data-cnum='" .$cnum . "' ";
                 $row['PES_STATUS'] .= " data-notesid='" . $notesId . "' ";
                 $row['PES_STATUS'] .= " data-email='" . $email . "' ";
                 $row['PES_STATUS'] .= " data-pesdaterequested='" .trim($row['PES_DATE_REQUESTED']) . "' ";
                 $row['PES_STATUS'] .= " data-pesrequestor='" .trim($row['PES_REQUESTOR']) . "' ";
                 $row['PES_STATUS'] .= " data-pesstatus='" .$status . "' ";
                 $row['PES_STATUS'] .= " > ";
                 $row['PES_STATUS'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                 $row['PES_STATUS'] .= "</button>&nbsp;";
                 $row['PES_STATUS'] .= $status;
                 break;
            case $row['NOTES_ID']:
                 $row['NOTES_ID']  = "<button type='button' class='btn btn-default btn-xs btnEditPerson' aria-label='Left Align' ";
                 $row['NOTES_ID'] .= "data-cnum='" .$cnum. "'";
                 $row['NOTES_ID'] .= " > ";
                 $row['NOTES_ID'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
                 $row['NOTES_ID'] .= $notesId;
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

    static function optionsForPreBoarded($preBoarded=null){

        if(empty($preBoarded)){
            $availPreBoPredicate  = " ( CNUM LIKE '%xxx' or CNUM LIKE '%XXX' or CNUM LIKE '%999' ) ";
            $availPreBoPredicate .= " AND ((PES_STATUS_DETAILS not like 'Boarded as%' )  or ( PES_STATUS_DETAILS is null)) ";
            $availPreBoPredicate .= " AND PES_STATUS in (";
            $availPreBoPredicate .= " '" . personRecord::PES_STATUS_CLEARED . "' "; // Pre-boarded who haven't been boarded
            $availPreBoPredicate .= ",'" . personRecord::PES_STATUS_EXCEPTION ."' ";
            $availPreBoPredicate .= " )";
        } else {
            $availPreBoPredicate  = " ( CNUM = '" . db2_escape_string($preBoarded) . "' ) ";
        }

        $sql =  " SELECT distinct FIRST_NAME, LAST_NAME, EMAIL_ADDRESS, CNUM  FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE " . $availPreBoPredicate;
        $sql .= " ORDER BY FIRST_NAME, LAST_NAME ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if (!$rs){
            DbTable::displayErrorMessage($rs,__CLASS__ , __METHOD__ , $sql);
            return false;
        }
        $options = array();
        while(($row=db2_fetch_assoc($rs))==true){
            $option  = "<option value='" . trim($row['CNUM']) ."'";
            $option .= trim($row['CNUM']) == trim($preBoarded) ? ' selected ' : null;
            $option .= " >" . trim($row['FIRST_NAME']) ." " . trim($row['LAST_NAME'])  . " (" . trim($row['EMAIL_ADDRESS']) .") ";
            $option .=  "</option>";
            $options[] = $option;
        }
        return $options;

    }

    static function dataFromPreBoarder($cnum){
        $sql = " SELECT CTB_RTB,TT_BAU, WORK_STREAM, PES_DATE_REQUESTED, PES_DATE_RESPONDED, PES_REQUESTOR,  PES_STATUS, PES_STATUS_DETAILS, FM_CNUM ";
        $sql .= " , CONTRACTOR_ID_REQUIRED, CONTRACTOR_ID, LOB, OPEN_SEAT_NUMBER, ROLE_ON_THE_ACCOUNT ";
        $sql .= " , START_DATE, PROJECTED_END_DATE, CIO_ALIGNMENT  ";
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

    function flagPreboarders (){
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET REVALIDATION_STATUS='" . personRecord::REVALIDATED_PREBOARDER . "', REVALIDATION_DATE_FIELD = current date ";
        $sql .= " WHERE (CNUM like '%999' or CNUM like '%xxx' or CNUM like '%XXX' )  AND ( REVALIDATION_STATUS is null )";

        $rs = db2_exec($_SESSION['conn'],$sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Revalidation has flagged Pre-Boarders",AuditTable::RECORD_TYPE_AUDIT);
        return true;
    }




}