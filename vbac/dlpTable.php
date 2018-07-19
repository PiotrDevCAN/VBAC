<?php
namespace vbac;

use itdq\DbTable;


class dlpTable extends DbTable {
    
    function licencedAlready($cnum,$hostname){
        $sql = " SELECT COUNT(*) as LICENCES ";
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$DLP;
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) ."' ";
        $sql.= " AND HOSTNAME='" . strtoupper(db2_escape_string($hostname)) . "' ";
        $sql.= " AND TRANSFERRED_TO_HOSTNAME is null ";
        
        
        var_dump($sql);
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $row = db2_fetch_assoc($rs);
        
        return $row['LICENCES'] != 0;
    }
    
    function recordTransfer($cnum,$fromHostname, $toHostname){
        $sql = " UPDATE ";
        $sql.=  $_SESSION['Db2Schema'] . "." . allTables::$DLP;
        $sql.= "  SET TRANSFERRED_TO_HOSTNAME='" . strtoupper(db2_escape_string($toHostname)) . "' ";
        $sql.= " ,TRANSFERRED_DATE = CURRENT DATE ";
        $sql.= " ,TRANSFERRED_EMAIL='" . db2_escape_string($_SESSION['ssoEmail']) . "' ";
        $sql.= " ,STATUS='" . dlpRecord::STATUS_TRANSFERRED . "' ";
        $sql.= " WHERE ";
        $sql.= " CNUM='" . db2_escape_string(trim($cnum)) . "' ";
        $sql.= " AND HOSTNAME='" . db2_escape_string(strtoupper(trim($fromHostname))) . "' ";
        $sql.= " AND TRANSFERRED_TO_HOSTNAME is null ";
        
        var_dump($sql);
        
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        };
        
        return true;
    }
    
    function recordLicence($cnum,$hostname,$approvingEmail){
        $sql = " INSERT INTO ";
        $sql.=  $_SESSION['Db2Schema'] . "." . allTables::$DLP;
        $sql.= "( CNUM, HOSTNAME, APPROVER_EMAIL, CREATION_DATE, EXCEPTION_CODE, STATUS ) ";
        $sql.= " values ";
        $sql.= " ('" . db2_escape_string($cnum) . "','" . strtoupper(db2_escape_string($hostname)) . "'";
        $sql.= ",'" . db2_escape_string($approvingEmail) . "', CURRENT DATE, '266'";
        $sql.= ",'" . dlpRecord::STATUS_PENDING . "' ";
        $sql.= " ) ";
        
        var_dump($sql);
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        };
        
        return true;
    }
    

    
    
    function getForPortal($predicate=null, $withButtons=true){
        $sql = "select distinct D.cnum ";
        $sql.= ", case when P.notes_id is not null then P.notes_id else A.CNUM end as Licensee ";
        $sql.= ", D.HOSTNAME ";
        $sql.= ", case when A.notes_id is not null then A.notes_id else D.approver_email end as approver ";
        $sql.= ", case when D.approved_date is not null then varchar_format(D.APPROVED_DATE,'YYYY-MM-DD') else D.status end as approved ";
        $sql.= ", case when F.notes_id is not null then F.NOTES_ID else 'Unknown to vbac' end as FM ";
        $sql.= ", D.CREATION_DATE as CREATED ";
        $sql.= ", D.EXCEPTION_CODE as CODE";
        $sql.= ", T.hostname as OLD_HOSTNAME ";
        $sql.= ", T.TRANSFERRED_DATE as TRANSFERRED ";
        $sql.= ", case when N.NOTES_ID is not null then N.NOTES_ID else T.TRANSFERRED_EMAIL end  as TRANSFERRER ";
        $sql.= ", D.STATUS ";
        $sql.= " from ". $_SESSION['Db2Schema'] . "." . allTables::$DLP . " as D ";
        $sql.= " left join ". $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= " on D.cnum = P.cnum ";
        $sql.= " left join ". $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql.= " on P.FM_CNUM = F.CNUM ";
        $sql.= " left join ". $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as A ";
        $sql.= " on lower(approver_email) = lower(a.email_address) ";
        $sql.= " left join ". $_SESSION['Db2Schema'] . "." . allTables::$DLP . "  as T ";
        $sql.= " on D.hostname = T.TRANSFERRED_TO_HOSTNAME ";
        $sql.= " left join ". $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as N ";
        $sql.= " on T.transferred_email = N.EMAIL_ADDRESS ";
        $sql.= " left join ". $_SESSION['Db2Schema'] . "." . allTables::$DELEGATE . " as G ";
        $sql.= " on F.CNUM = G.CNUM  ";
        
        
        $sql.= " where D.transferred_to_hostname is null ";
        $sql.= !empty($predicate) ? $predicate : null;
        $sql.= " ; ";
        
        $rs = db2_exec($_SESSION['conn'],$sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $report = array();
        while (($row=db2_fetch_assoc($rs))==true) {
            $report[] = $withButtons ? $this->addButtons(array_map('trim', $row)) : array_map('trim', $row);
        }
        
        return !empty($report) ? array('data'=>$report,'sql'=>$sql) : false;
    }
    
    function addButtons($row){
        
        $cnum = trim($row['CNUM']);
        $hostname = trim($row['HOSTNAME']); 
            
        $approveButton  = "<button type='button' class='btn btn-default btn-xs btnDlpLicenseApprove btn-success' aria-label='Left Align' ";
        $approveButton .= "data-cnum='" .$cnum . "' ";
        $approveButton .= "data-hostname='" .$hostname . "' ";
        $approveButton .= " > ";
        $approveButton .= "<span class='glyphicon glyphicon-ok ' aria-hidden='true'></span>";
        $approveButton .= " </button> ";
               
        $rejectButton  = "<button type='button' class='btn btn-default btn-xs btnDlpLicenseReject btn-danger' aria-label='Left Align' ";
        $rejectButton .= "data-cnum='" .$cnum . "' ";
        $rejectButton .= "data-hostname='" .$hostname . "' ";
        $rejectButton .= " > ";
        $rejectButton .= "<span class='glyphicon glyphicon-remove ' aria-hidden='true'></span>";
        $rejectButton .= " </button> ";
        
        $deleteButton  = "<button type='button' class='btn btn-default btn-xs btnDlpLicenseDelete ' aria-label='Left Align' ";
        $deleteButton .= "data-cnum='" .$cnum . "' ";
        $deleteButton .= "data-hostname='" .$hostname . "' ";
        $deleteButton .= "data-transferred='" . trim($row['TRANSFERRED_TO_HOSTNAME']) . "' ";
        $deleteButton .= " > ";
        $deleteButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
        $deleteButton .= " </button> ";
        
        
        $approvedValue = trim($row['APPROVED']);
         
        switch ($row['STATUS']) {
            case dlpRecord::STATUS_PENDING:
                $row['APPROVED'] = $approveButton . $rejectButton . "&nbsp;" . $approvedValue;
                break;
            case dlpRecord::STATUS_REJECTED:
                $row['APPROVED'] = $approveButton .  "&nbsp;<i>" . $approvedValue . "&nbsp;(" . $row['STATUS'] . ")</i>";
                $row['CNUM'] =   "<i>" . $row['CNUM'] . "</i>";
                $row['HOSTNAME'] =   "<i>" . $row['HOSTNAME'] . "</i>";
                $row['APPROVER'] =   "<i>" . $row['APPROVER'] . "</i>";
                $row['LICENSEE'] =   "<i>" . $row['LICENSEE'] . "</i>";
                
                break;           
            default:
                break;
        } 
        
        if($_SESSION['isPmo'] || $_SESSION['isCdi']){
            $licensee = $row['LICENSEE'];
            $row['LICENSEE'] = $deleteButton . "&nbsp;" . $licensee;
        }
       
        return $row;
    }
    
    function approveReject($cnum, $hostname, $approveReject){
        $sql = " UPDATE ";
        $sql.= $_SESSION['Db2Schema'] . "." . allTables::$DLP;
        $sql.= " SET ";
        $sql.= " STATUS='";
        $sql.= $approveReject==dlpRecord::STATUS_APPROVED ? dlpRecord::STATUS_APPROVED : dlpRecord::STATUS_REJECTED;
        $sql.= " ' ";
        $sql.= ", APPROVED_DATE = Current date ";
        $sql.= ", APPROVER_EMAIL='" . $_SESSION['ssoEmail'] . "' ";
        $sql.= " WHERE ";
        $sql.= " CNUM='" . db2_escape_string(trim($cnum)) . "' ";
        $sql.= " AND HOSTNAME='" . db2_escape_string(trim($hostname)) . "' ";
        $sql.= " AND TRANSFERRED_TO_HOSTNAME is null ";
        
        var_dump($approveReject);
        var_dump($sql);
        
        
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }
    
    function delete($cnum, $hostname, $transferred=null){ 
        $sql = " DELETE FROM ";
        $sql.= $_SESSION['Db2Schema'] . "." . allTables::$DLP;
        $sql.= " WHERE ";
        $sql.= " CNUM='" . db2_escape_string(trim($cnum)) . "' ";
        $sql.= " AND HOSTNAME='" . db2_escape_string(trim($hostname)) . "' ";
        $sql.= empty(($transferred)) ? " AND TRANSFERRED_TO_HOSTNAME is null " : " AND TRANSFERRED_TO_HOSTNAME='" . db2_escape_string(trim(strtoupper($transferred))) . "' ";
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }
    
    
    
};