<?php
namespace vbac;

use itdq\DbTable;

class personTable extends DbTable {


    static function getNextVirtualCnum(){
        $sql  = " SELECT CNUM FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM LIKE '%xxx' ";
        $sql .= " order by CNUM desc ";
        $sql .= " OPTIMIZE FOR 1 ROW ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $topRow = db2_fetch_array($rs);
        if(isset($topRow[0])){
            $thisCnum = substr($topRow[0],0,6);
            $next = $thisCnum+1;
            $nextVirtualCnum = substr('000000' . $next ,-6) . 'xxx';
        } else {
            $nextVirtualCnum = '000001xxx';
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
        $status = trim($row[personRecord::FIELD_PES_STATUS]);
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

        switch (trim($row[personRecord::FIELD_PES_STATUS])) {
            case null:
                $row[personRecord::FIELD_PES_STATUS]  = "<button type='button' class='btn btn-default btn-xs btnPesInitiate accessRestrict accessPmo accessFm' ";
                $row[personRecord::FIELD_PES_STATUS]  .= "aria-label='Left Align' ";
                $row[personRecord::FIELD_PES_STATUS] .= " data-cnum='" .$cnum . "' ";
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



}