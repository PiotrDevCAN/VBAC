<?php
namespace vbac;

use itdq\DbTable;
use itdq\AuditTable;
use itdq\Loader;

class personTable extends DbTable {

    private $preparedRevalidationStmt;
    private $preparedRevalidationLeaverStmt;
    private $preparedLeaverProjectedEndDateStmt;
    private $preparedUpdateBluepagesFields;
    private $preparedUpdateLbgLocationStmt;
    private $preparedUpdateSecurityEducationStmt;

    private $allNotesIdByCnum;
    private $loader;

    private $thirtyDaysHence;

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
    
    static function activePersonPredicate(){
        $activePredicate = " ((( REVALIDATION_STATUS in ('" . personRecord::REVALIDATED_FOUND . "','" . personRecord::REVALIDATED_VENDOR . "') or trim(REVALIDATION_STATUS) is null or REVALIDATION_STATUS like '" . personRecord::REVALIDATED_OFFBOARDING . "%') ";
        $activePredicate.= "   OR ";
        $activePredicate.= " ( trim(REVALIDATION_STATUS) is null ) )";
        $activePredicate.= " AND REVALIDATION_STATUS not like '" . personRecord::REVALIDATED_OFFBOARDING . ":" .personRecord::REVALIDATED_LEAVER . "%' " ;
        $activePredicate.= " AND PES_STATUS in ('". personRecord::PES_STATUS_CLEARED ."','". personRecord::PES_STATUS_CLEARED_PERSONAL ."','". personRecord::PES_STATUS_EXCEPTION ."') ) ";
        return $activePredicate;    
    }


    function returnAsArray(){
//         $this->loader = empty($this->loader) ? new Loader() : $this->loader;
//         $this->allNotesIdByCnum = empty($this->allNotesIdByCnum) ? $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON) : $this->allNotesIdByCnum;

        $this->thirtyDaysHence = new \DateTime();
        $this->thirtyDaysHence->add(new \DateInterval('P31D'));


        $data = array();

        $isFM   = personTable::isManager($_SESSION['ssoEmail']);
        $myCnum = personTable::myCnum();


        $justaUser = !$_SESSION['isCdi']  && !$_SESSION['isPmo'] && !$_SESSION['isPes'] && !$_SESSION['isFm'] ;

        $predicate = " 1=1  ";

        $predicate .= $isFM ? " AND FM_CNUM='" . db2_escape_string(trim($myCnum)) . "' " : "";
        $predicate .= $justaUser ? " AND CNUM='" . db2_escape_string(trim($myCnum)) . "' " : ""; // FM Can only see their own people.

        $sql  = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql .= " WHERE " . $predicate;

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
    
    function returnPersonFinderArray(){
        $activePredicate = $this->activePersonPredicate();
        $data = array();        
        
        $sql = " SELECT CNUM, FIRST_NAME, LAST_NAME, EMAIL_ADDRESS, NOTES_ID, FM_CNUM ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql.= " WHERE 1=1 AND " . $activePredicate;
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while(($row=db2_fetch_assoc($rs))==true){              
                $preparedRow = $this->prepareFields($row);
                $fmCnumField = $preparedRow['FM_CNUM'];                
                $transferButton = "<button type='button' class='btn btn-default btn-xs btnTransfer' aria-label='Left Align' ";
                $transferButton.= "data-cnum='" .trim($row['CNUM']) . "' ";
                $transferButton.= "data-notesid='" .trim($row['NOTES_ID']) . "' ";
                $transferButton.= "data-fromCnum ='" .trim($row['FM_CNUM']) . "' ";
                $transferButton.= "data-fromNotesid ='" .$preparedRow['FM_CNUM'] . "' ";
                $transferButton.= " > ";
                $transferButton.= "<span class='glyphicon glyphicon-transfer ' aria-hidden='true'></span>";
                $transferButton.= " </button> ";                
                $preparedRow['FM_CNUM'] = $transferButton . $fmCnumField; 
                $data[] = $preparedRow;
            }
        }
        return $data;
    }


    function findDirtyData($autoClear=false){

        $sql  = " SELECT * FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName ;
        $sql .= " ORDER BY CNUM ";

        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        } else {
            while(($row=db2_fetch_assoc($rs))==true){
                $jsonEncodable = json_encode($row);
                if(!$jsonEncodable){
                    echo "<hr/><br/>Dirty Date Found in record for : " . $row['CNUM'];
                    foreach ($row as $key => $value) {
                        $jsonEncodableField = json_encode($value);
                        if(!$jsonEncodableField){
                            echo "Column: $key Value: $value";
                            if($autoClear && !$jsonEncodable){
                                $row[$key] = null;
                                $personRecord = new personRecord();
                                $personRecord->setFromArray($row);
                                $this->saveRecord($personRecord);
                            }
                        }
                    }
                }

            }
        }
   }


    function  prepareFields($row){
        $this->loader = empty($this->loader) ? new Loader() : $this->loader;
        $this->allNotesIdByCnum = empty($this->allNotesIdByCnum) ? $this->loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON) : $this->allNotesIdByCnum;
       
        $preparedRow = array_map('trim', $row);
        $fmNotesid = isset($this->allNotesIdByCnum[trim($row['FM_CNUM'])]) ? $this->allNotesIdByCnum[trim($row['FM_CNUM'])]  :  trim($row['FM_CNUM']);
        $preparedRow['FM_CNUM'] = $fmNotesid;
        
        var_dump($fmNotesid);
        
        return $preparedRow;
    }

    function addButtons($row){
        // save some fields before we change the,
        $notesId = trim($row['NOTES_ID']);
        $email   = trim($row['EMAIL_ADDRESS']);
        $cnum = trim($row['CNUM']);
        $flag = isset($row['FM_MANAGER_FLAG']) ? $row['FM_MANAGER_FLAG'] : null ;
        $status = empty($row['PES_STATUS']) ? personRecord::PES_STATUS_NOT_REQUESTED : trim($row['PES_STATUS']) ;
        $projectedEndDateObj = !empty($row['PROJECTED_END_DATE']) ? \DateTime::createFromFormat('Y-m-d', $row['PROJECTED_END_DATE']) : false;
        $potentialForOffboarding = $projectedEndDateObj ? $projectedEndDateObj <= $this->thirtyDaysHence : false; // Thirty day rule.
        $potentialForOffboarding = $potentialForOffboarding || $row['REVALIDATION_STATUS']==personRecord::REVALIDATED_LEAVER ? true : $potentialForOffboarding;  // Any leaver - has potential to be offboarded
        $potentialForOffboarding = substr(trim($row['REVALIDATION_STATUS']),0,10)==personRecord::REVALIDATED_OFFBOARDED ? false : $potentialForOffboarding;
        $potentialForOffboarding = substr(trim($row['REVALIDATION_STATUS']),0,11)==personRecord::REVALIDATED_OFFBOARDING ? false : $potentialForOffboarding;
        $revalidationStatus = trim($row['REVALIDATION_STATUS']);




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

        if($_SESSION['isPes'] || $_SESSION['isPmo'] || $_SESSION['isFm'] || $_SESSION['isCdi']){
            switch (true) {
                case $status == personRecord::PES_STATUS_NOT_REQUESTED:
                    $row['PES_STATUS']  = "<button type='button' class='btn btn-default btn-xs btnPesInitiate accessRestrict accessPmo accessFm' ";
                    $row['PES_STATUS']  .= "aria-label='Left Align' ";
                    $row['PES_STATUS'] .= " data-cnum='" .$cnum . "' ";
                    $row['PES_STATUS'] .= " data-pesstatus='$status' ";
                    $row['PES_STATUS'] .= " > ";
                    $row['PES_STATUS'] .= "<span class='glyPesInitiate glyphicon glyphicon-plane ' aria-hidden='true'></span>";
                    $row['PES_STATUS'] .= "</button>&nbsp;";
                    $row['PES_STATUS'] .= $status;
                    break;
                case $status == personRecord::PES_STATUS_CLEARED_PERSONAL && $_SESSION['isPes'] :
                case $status == personRecord::PES_STATUS_CLEARED && $_SESSION['isPes'] :
                case $status == personRecord::PES_STATUS_EXCEPTION && $_SESSION['isPes'] :
                case $status == personRecord::PES_STATUS_DECLINED && $_SESSION['isPes'] ;
                case $status == personRecord::PES_STATUS_FAILED && $_SESSION['isPes'] ;
                case $status == personRecord::PES_STATUS_INITIATED && $_SESSION['isPes'] ;
                case $status == personRecord::PES_STATUS_REMOVED && $_SESSION['isPes'] ;
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
                default:
                    break;
            }
        }

        if(($_SESSION['isPes'] || $_SESSION['isPmo'] || $_SESSION['isFm'] || $_SESSION['isCdi']) && ($revalidationStatus!=personRecord::REVALIDATED_OFFBOARDED))  {
            $row['NOTES_ID']  = "<button type='button' class='btn btn-default btn-xs btnEditPerson' aria-label='Left Align' ";
            $row['NOTES_ID'] .= "data-cnum='" .$cnum . "'";
            $row['NOTES_ID'] .= " > ";
            $row['NOTES_ID'] .= "<span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>";
            $row['NOTES_ID'] .= " </button> ";
            $row['NOTES_ID'] .= $notesId;
        }

        if( ($_SESSION['isPmo'] || $_SESSION['isCdi']) && (substr(trim($row['REVALIDATION_STATUS']),0,11)==personRecord::REVALIDATED_OFFBOARDING))  {
            $row['REVALIDATION_STATUS']  = "<button type='button' class='btn btn-default btn-xs btnStopOffboarding btn-danger' aria-label='Left Align' ";
            $row['REVALIDATION_STATUS'] .= "data-cnum='" .$cnum . "'";
            $row['REVALIDATION_STATUS'] .= "title='Stop Offboarding'";
            $row['REVALIDATION_STATUS'] .= " > ";
            $row['REVALIDATION_STATUS'] .= "<span class='glyphicon glyphicon-remove-sign ' aria-hidden='true'></span>";
            $row['REVALIDATION_STATUS'] .= " </button> ";
            $row['REVALIDATION_STATUS'] .= "<button type='button' class='btn btn-default btn-xs btnOffboarded btn-danger' aria-label='Left Align' ";
            $row['REVALIDATION_STATUS'] .= "data-cnum='" .$cnum . "'";
            $row['REVALIDATION_STATUS'] .= "title='Complete Offboarding.'";
            $row['REVALIDATION_STATUS'] .= " > ";
            $row['REVALIDATION_STATUS'] .= "<span class='glyphicon glyphicon-log-out ' aria-hidden='true'></span>";
            $row['REVALIDATION_STATUS'] .= " </button> ";
            $row['REVALIDATION_STATUS'] .= $revalidationStatus;
        }

        if( $potentialForOffboarding && ($_SESSION['isPmo'] || $_SESSION['isCdi']) && substr(trim($row['REVALIDATION_STATUS']),0,11)!=personRecord::REVALIDATED_OFFBOARDING )  {
            $row['REVALIDATION_STATUS']  = "<button type='button' class='btn btn-default btn-xs btnOffboarding btn-warning' aria-label='Left Align' ";
            $row['REVALIDATION_STATUS'] .= "data-cnum='" .$cnum . "'";
            $row['REVALIDATION_STATUS'] .= "title='Initiate Offboarding.'";
            $row['REVALIDATION_STATUS'] .= " > ";
            $row['REVALIDATION_STATUS'] .= "<span class='glyphicon glyphicon-log-out ' aria-hidden='true'></span>";
            $row['REVALIDATION_STATUS'] .= " </button> ";
            $row['REVALIDATION_STATUS'] .= $revalidationStatus;
         }

         if( ($_SESSION['isPmo'] || $_SESSION['isCdi']) && substr(trim($row['REVALIDATION_STATUS']),0,10)==personRecord::REVALIDATED_OFFBOARDED )  {
             $row['REVALIDATION_STATUS']  = "<button type='button' class='btn btn-default btn-xs btnDeoffBoarding btn-danger' aria-label='Left Align' ";
             $row['REVALIDATION_STATUS'] .= "data-cnum='" .$cnum . "'";
             $row['REVALIDATION_STATUS'] .= "title='Bring back from Offboarding.'";
             $row['REVALIDATION_STATUS'] .= " > ";
             $row['REVALIDATION_STATUS'] .= "<span class='glyphicon glyphicon-log-in ' aria-hidden='true'></span>";
             $row['REVALIDATION_STATUS'] .= " </button> ";
             $row['REVALIDATION_STATUS'] .= $revalidationStatus;
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

        $status = empty($status) ? personRecord::PES_STATUS_NOT_REQUESTED : $status;

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
        } catch (\Exception $e) {
            var_dump($e);
        }

       if(!$result){
           DbTable::displayErrorMessage($result, __CLASS__, __METHOD__, $sql);
           return false;
       }
        return true;

    }

    function saveCtid($cnum,$ctid){
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET CT_ID='"  . db2_escape_string($ctid) . "' ";
        $sql .= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";

        $result = db2_exec($_SESSION['conn'], $sql);

        if(!$result){
            DbTable::displayErrorMessage($result, __CLASS__,__METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Set CT_ID to $ctid for $cnum",AuditTable::RECORD_TYPE_AUDIT);

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
    
    function transferIndividual($cnum,$toFmCnum){
        $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql .= " SET FM_CNUM='"  . db2_escape_string($toFmCnum) . "' ";
        $sql .= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";
        
        $result = db2_exec($_SESSION['conn'], $sql);

        
        if(!$result){
            DbTable::displayErrorMessage($result, __CLASS__,__METHOD__, $sql);
            return false;
        }
        AuditTable::audit("Set FM_CNUM to $toFmCnum for $cnum",AuditTable::RECORD_TYPE_AUDIT);           
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
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . db2_escape_string(strtoupper(trim($emailAddress))) . "' ";

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

    static function myManagersCnum(){
        if(isset($_SESSION['myManagersCnum'])) {
            return $_SESSION['myManagersCnum'];
        }

        if (!isset($_SESSION['ssoEmail'])) {
            return false;
        }

        $sql = " SELECT FM_CNUM FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . db2_escape_string(strtoupper(trim($_SESSION['ssoEmail']))) . "' ";

        $resultSet = db2_exec($_SESSION['conn'], $sql);

        if(!$resultSet){
            DbTable::displayErrorMessage($resultSet, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($resultSet);
        $myManagersCnum = strtoupper(trim($row['FM_CNUM']));
        $_SESSION['myManagersCnum'] = $myManagersCnum;
        return $_SESSION['myManagersCnum'];
    }




    static function getCnumFromEmail($emailAddress){
        $sql = " SELECT CNUM FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(EMAIL_ADDRESS) = '" . db2_escape_string(strtoupper(trim($emailAddress))) . "' ";

        $resultSet = db2_exec($_SESSION['conn'], $sql);
        if(!$resultSet){
            DbTable::displayErrorMessage($resultSet, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($resultSet);
        $cnum = strtoupper(trim($row['CNUM']));
        return $cnum;
    }

    static function getEmailFromCnum($cnum){
        $sql = " SELECT EMAIL_ADDRESS FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM = '" . db2_escape_string(strtoupper(trim($cnum))) . "' ";

        $resultSet = db2_exec($_SESSION['conn'], $sql);
        if(!$resultSet){
            DbTable::displayErrorMessage($resultSet, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($resultSet);
        $email = trim($row['EMAIL_ADDRESS']);
        return $email;
    }


    static function getCnumFromNotesid($notesid){
        $sql = " SELECT CNUM FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE UPPER(NOTES_ID) = '" . db2_escape_string(strtoupper(trim($notesid))) . "' ";

        $resultSet = db2_exec($_SESSION['conn'], $sql);

        if(!$resultSet){
            DbTable::displayErrorMessage($resultSet, __CLASS__, __METHOD__, $sql);
            return false;
        }

        $row = db2_fetch_assoc($resultSet);
        $cnum = strtoupper(trim($row['CNUM']));
        return $cnum;
    }
    
    
    static function getNotesidFromCnum($cnum){
        $sql = " SELECT NOTES_ID FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON;
        $sql .= " WHERE CNUM = '" . db2_escape_string(strtoupper(trim($cnum))) . "' ";
        
        $resultSet = db2_exec($_SESSION['conn'], $sql);
        
        if(!$resultSet){
            DbTable::displayErrorMessage($resultSet, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $row = db2_fetch_assoc($resultSet);
        $notesid = trim($row['NOTES_ID']);
        return $notesid;
    }


    static function optionsForPreBoarded($preBoarded=null){

        if(empty($preBoarded)){
            $availPreBoPredicate  = " ( CNUM LIKE '%xxx' or CNUM LIKE '%XXX' or CNUM LIKE '%999' ) ";
            $availPreBoPredicate .= " AND ( REVALIDATION_STATUS in ('" . personRecord::REVALIDATED_PREBOARDER . "')) ";
            $availPreBoPredicate .= " AND ((PES_STATUS_DETAILS not like 'Boarded as%' )  or ( PES_STATUS_DETAILS is null)) ";
            $availPreBoPredicate .= " AND PES_STATUS not in (";
            $availPreBoPredicate .= " '" . personRecord::PES_STATUS_FAILED . "' "; // Pre-boarded who haven't been boarded
            $availPreBoPredicate .= ",'" . personRecord::PES_STATUS_REMOVED ."' ";
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
        $sql .= " , CT_ID_REQUIRED, CT_ID, LOB, OPEN_SEAT_NUMBER, ROLE_ON_THE_ACCOUNT ";
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

    private function prepareLeaverProjectedEndDateStmt(){
        if(empty($this->preparedLeaverProjectedEndDateStmt)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET PROJECTED_END_DATE = current date ";
            $sql .= " WHERE CNUM=? AND PROJECTED_END_DATE is null ";

            $this->preparedLeaverProjectedEndDateStmt = db2_prepare($_SESSION['conn'], $sql);

            if(!$this->preparedLeaverProjectedEndDateStmt){
                DbTable::displayErrorMessage($this->preparedLeaverProjectedEndDateStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedLeaverProjectedEndDateStmt;
    }

    private function prepareRevalidationLeaverStmt(){
        if(empty($this->preparedRevalidationLeaverStmt)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET REVALIDATION_STATUS='" . personRecord::REVALIDATED_LEAVER . "' , REVALIDATION_DATE_FIELD = current date ";
            $sql .= " WHERE CNUM=? ";

            $this->preparedRevalidationLeaverStmt = db2_prepare($_SESSION['conn'], $sql);

            if(!$this->preparedRevalidationLeaverStmt){
                DbTable::displayErrorMessage($this->preparedRevalidationStmt, __CLASS__, __METHOD__, $sql);
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

        $preparedStmt = $this->prepareLeaverProjectedEndDateStmt();
        $data = array(trim($cnum));
        $rs = db2_execute($preparedStmt,$data);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, "prepared: leaverProjectedEndDateStmt");
            return false;
        }

        AuditTable::audit("Revalidation has found leaver: $cnum      ",AuditTable::RECORD_TYPE_AUDIT);
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

    function flagOffboarding ($cnum){
        if(!empty($cnum)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET REVALIDATION_STATUS= CONCAT(CONCAT(TRIM('" . personRecord::REVALIDATED_OFFBOARDING . "'),':'),REVALIDATION_STATUS),  REVALIDATION_DATE_FIELD = current date ";
            $sql .= " WHERE CNUM = '" . db2_escape_string($cnum) . "'";

            $rs = db2_exec($_SESSION['conn'],$sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }
            AuditTable::audit("CNUM: $cnum  has been flagged as :" . personRecord::REVALIDATED_OFFBOARDING,AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }

    }

    function flagOffboarded ($cnum){
        if(!empty($cnum)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET REVALIDATION_STATUS=CONCAT(CONCAT('" . personRecord::REVALIDATED_OFFBOARDED . "',':'),SUBSTR(REVALIDATION_STATUS,13)), REVALIDATION_DATE_FIELD = current date, OFFBOARDED_DATE = current date ";
            $sql .= " WHERE CNUM = '" . db2_escape_string($cnum) . "'";

            $rs = db2_exec($_SESSION['conn'],$sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }
            AuditTable::audit("CNUM: $cnum  has been flagged as :" . personRecord::REVALIDATED_OFFBOARDED,AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }

    }

    function stopOffboarded ($cnum){
        if(!empty($cnum)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET REVALIDATION_STATUS= SUBSTR(REVALIDATION_STATUS,13), REVALIDATION_DATE_FIELD = current date, OFFBOARDED_DATE = null  ";
            $sql .= " WHERE CNUM = '" . db2_escape_string($cnum) . "'";

            $rs = db2_exec($_SESSION['conn'],$sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }
            AuditTable::audit("CNUM: $cnum  has been been STOPPED from Offboarding",AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }

    }

    function deOffboarded ($cnum){
        if(!empty($cnum)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET REVALIDATION_STATUS= TRIM(SUBSTR(REVALIDATION_STATUS,12)), REVALIDATION_DATE_FIELD = current date, OFFBOARDED_DATE = null  ";
            $sql .= " WHERE CNUM = '" . db2_escape_string($cnum) . "'";

            $rs = db2_exec($_SESSION['conn'],$sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }
            AuditTable::audit("CNUM: $cnum  has been been REVERSED from Offboarded",AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }

    }




    private function prepareUpdateLbgLocationStmt(){
        if(empty($this->preparedUpdateLbgLocationStmt)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET LBG_LOCATION=? ";
            $sql .= " WHERE CNUM=?  ";

            $this->preparedUpdateLbgLocationStmt = db2_prepare($_SESSION['conn'], $sql);

            if(!$this->preparedUpdateLbgLocationStmt){
                DbTable::displayErrorMessage($this->preparedRevalidationLeaverStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedUpdateLbgLocationStmt;
    }



    function updateLbgLocationForCnum ($lbgLocation, $cnum){
        if(!empty($cnum) && !empty($lbgLocation)){
            $preparedStmt = $this->prepareUpdateLbgLocationStmt();
            $data = array($lbgLocation,$cnum);
            $rs = db2_execute($preparedStmt,$data);
            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }
            AuditTable::audit("CNUM: $cnum  has been recorded as working from Aurora Location :" . $lbgLocation ,AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }
        return false;
    }

    static function getLbgLocationForCnum ($cnum){
        if(!empty($cnum)){
            $sql = " SELECT LBG_LOCATION FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " WHERE CNUM='" . db2_escape_string($cnum) . "' ";

            $rs = db2_exec($_SESSION['conn'], $sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }

            $locationRow = db2_fetch_assoc($rs);

            $sql = " SELECT FM_CNUM FROM " .  $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " WHERE CNUM='" . db2_escape_string($cnum) . "' ";

            $rs = db2_exec($_SESSION['conn'], $sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }

            $fmrow = db2_fetch_assoc($rs);


            $row = db2_fetch_assoc($rs);
            $location = !empty($locationRow['LBG_LOCATION']) ? trim($locationRow['LBG_LOCATION']) : false;
            $fmCnum = !empty($fmrow['FM_CNUM']) ? trim($fmrow['FM_CNUM']) : false;
            return array('location'=>$location,'fmCnum'=>$fmCnum);
        }
        return false;
    }

    private function prepareUpdateSecurityEducationStmt(){
        if(empty($this->preparedUpdateSecurityEducationStmt)){
            $sql  = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET SECURITY_EDUCATION=? ";
            $sql .= " WHERE CNUM=?  ";

            $this->preparedUpdateSecurityEducationStmt = db2_prepare($_SESSION['conn'], $sql);

            if(!$this->preparedUpdateSecurityEducationStmt){
                DbTable::displayErrorMessage($this->preparedUpdateSecurityEducationStmt, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return $this->preparedUpdateSecurityEducationStmt;
    }


    function updateSecurityEducationForCnum ($securityEducation, $cnum){
        if(!empty($cnum) && !empty($securityEducation)){
            $preparedStmt = $this->prepareUpdateSecurityEducationStmt();
            $data = array($securityEducation,$cnum);
            $rs = db2_execute($preparedStmt,$data);
            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }
            AuditTable::audit("CNUM: $cnum  Security Education :" . $securityEducation ,AuditTable::RECORD_TYPE_AUDIT);
            return true;
        }
        return false;
    }

    static function getSecurityEducationForCnum ($cnum){
        if(!empty($cnum)){
            $sql = " SELECT SECURITY_EDUCATION FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " WHERE CNUM='" . db2_escape_string($cnum) . "' ";

            $rs = db2_exec($_SESSION['conn'], $sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared statment');
                return false;
            }

            $row = db2_fetch_assoc($rs);
            $education = !empty($row['SECURITY_EDUCATION']) ? trim($row['SECURITY_EDUCATION']) : personRecord::SECURITY_EDUCATION_NOT_COMPLETED;
            return $education;
        }
        return false;
    }

    function assetUpdate($cnum,$assetTitle,$primaryUid){
        $columnName = DbTable::toColumnName($assetTitle);
        if(!empty($this->columns[$columnName])){
            $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
            $sql .= " SET " . $columnName . "='" . db2_escape_string(trim($primaryUid)) . "' ";
            $sql .= " WHERE CNUM='" . db2_escape_string(trim($cnum)) . "' ";


            $rs = db2_exec($_SESSION['conn'], $sql);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
                return false;
            }
        }
        return true;
    }





}