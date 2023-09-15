<?php
namespace vbac;

use itdq\DbTable;


class dlpTable extends DbTable {
    
    function licencedAlready($cnum,$hostname){
        $sql = " SELECT COUNT(*) as LICENCES ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$DLP;
        $sql.= " WHERE CNUM='" . htmlspecialchars($cnum) ."' ";
        $sql.= " AND HOSTNAME='" . strtoupper(htmlspecialchars($hostname)) . "' ";
        $sql.= " AND TRANSFERRED_TO_HOSTNAME is null ";
       
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        $row = sqlsrv_fetch_array($rs);
        
        return $row['LICENCES'] != 0;
    }
    
    function recordTransfer($cnum,$fromHostname, $toHostname){
        $sql = " UPDATE ";
        $sql.=  $GLOBALS['Db2Schema'] . "." . allTables::$DLP;
        $sql.= "  SET TRANSFERRED_TO_HOSTNAME='" . strtoupper(htmlspecialchars($toHostname)) . "' ";
        $sql.= " ,TRANSFERRED_DATE = CURRENT_TIMESTAMP ";
        $sql.= " ,TRANSFERRED_EMAIL='" . htmlspecialchars($_SESSION['ssoEmail']) . "' ";
        $sql.= " ,STATUS='" . dlpRecord::STATUS_TRANSFERRED . "' ";
        $sql.= " WHERE ";
        $sql.= " CNUM='" . htmlspecialchars(trim($cnum)) . "' ";
        $sql.= " AND HOSTNAME='" . htmlspecialchars(strtoupper(trim($fromHostname))) . "' ";
        $sql.= " AND TRANSFERRED_TO_HOSTNAME is null ";
       
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        };
        
        return true;
    }
    
    function recordLicence($cnum,$hostname,$approvingEmail){
        $sql = " INSERT INTO ";
        $sql.=  $GLOBALS['Db2Schema'] . "." . allTables::$DLP;
        $sql.= "( CNUM, HOSTNAME, APPROVER_EMAIL, CREATION_DATE, EXCEPTION_CODE, STATUS ) ";
        $sql.= " values ";
        $sql.= " ('" . htmlspecialchars($cnum) . "','" . strtoupper(htmlspecialchars($hostname)) . "'";
        $sql.= ",'" . htmlspecialchars($approvingEmail) . "', CURRENT_TIMESTAMP, '266'";
        $sql.= ",'" . dlpRecord::STATUS_PENDING . "' ";
        $sql.= " ) ";
       
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        };
        
        return true;
    }
    
    function getForPortal($predicate=null, $withButtons=true, $resultSetOnly = false){
        $sql = "select distinct D.cnum ";
        $sql.= ", case when P.notes_id is not null then P.notes_id else A.CNUM end as Licensee ";
        $sql.= ", D.HOSTNAME ";
        $sql.= ", case when A.notes_id is not null then A.notes_id else D.approver_email end as approver ";
        // $sql.= ", case when D.approved_date is not null then varchar_format(D.APPROVED_DATE,'YYYY-MM-DD') else D.status end as approved ";
        $sql.= ", case when D.approved_date is not null then D.APPROVED_DATE else D.status end as approved ";
        $sql.= ", case when F.notes_id is not null then F.NOTES_ID else 'Unknown to vbac' end as FM ";
        $sql.= ", D.CREATION_DATE as CREATED ";
        $sql.= ", D.EXCEPTION_CODE as CODE";
        $sql.= ", T.hostname as OLD_HOSTNAME ";
        $sql.= ", T.TRANSFERRED_DATE as TRANSFERRED ";
        $sql.= ", T.TRANSFERRED_TO_HOSTNAME ";
        $sql.= ", case when N.NOTES_ID is not null then N.NOTES_ID else T.TRANSFERRED_EMAIL end  as TRANSFERRER ";
        $sql.= ", D.STATUS ";
        $sql.= " from ". $GLOBALS['Db2Schema'] . "." . allTables::$DLP . " as D ";
        $sql.= " left join ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= " on D.cnum = P.cnum ";
        $sql.= " left join ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql.= " on P.FM_CNUM = F.CNUM ";
        $sql.= " left join ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as A ";
        $sql.= " on lower(approver_email) = lower(a.email_address) ";
        $sql.= " left join ". $GLOBALS['Db2Schema'] . "." . allTables::$DLP . "  as T ";
        $sql.= " on D.hostname = T.TRANSFERRED_TO_HOSTNAME ";
        $sql.= " left join ". $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as N ";
        $sql.= " on T.transferred_email = N.EMAIL_ADDRESS ";
        $sql.= " left join ". $GLOBALS['Db2Schema'] . "." . allTables::$DELEGATE . " as G ";
        $sql.= " on F.CNUM = G.CNUM  ";
        
        $sql.= " where 1=1 ";
        $sql.= !empty($predicate) ? $predicate : null;
        $sql.= " ; ";
        
        $rs = sqlsrv_query($GLOBALS['conn'],$sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        if($resultSetOnly){
            return $rs;
        }
        
        $report = array();
        while (($row=sqlsrv_fetch_array($rs))==true) {
            $report[] = $withButtons ? $this->addButtons(array_map('trim', $row)) : array_map('trim', $row);
        }
        
        return array('data'=>$report,'sql'=>$sql);
    }
    
    function addButtons($row){
        
        $cnum = trim($row['CNUM']);
        $hostname = trim($row['HOSTNAME']); 
        $transferred = trim($row['TRANSFERRED_TO_HOSTNAME']);
            
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
        $rejectButton .= "<span class='glyphicon glyphicon-trash ' aria-hidden='true'></span>";
        $rejectButton .= " </button> ";
        
        $deleteButton  = "<button type='button' class='btn btn-default btn-xs btnDlpLicenseDelete ' aria-label='Left Align' ";
        $deleteButton .= "data-cnum='" .$cnum . "' ";
        $deleteButton .= "data-hostname='" .$hostname . "' ";
        $deleteButton .= "data-transferred='" . $transferred . "' ";
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
        $sql.= $GLOBALS['Db2Schema'] . "." . allTables::$DLP;
        $sql.= " SET ";
        $sql.= " STATUS='";
        $sql.= $approveReject==dlpRecord::STATUS_APPROVED ? dlpRecord::STATUS_APPROVED : dlpRecord::STATUS_REJECTED;
        $sql.= " ' ";
        $sql.= ", APPROVED_DATE = CAST( CURRENT_TIMESTAMP AS Date ) ";
        $sql.= ", APPROVER_EMAIL='" . $_SESSION['ssoEmail'] . "' ";
        $sql.= " WHERE ";
        $sql.= " CNUM='" . htmlspecialchars(trim($cnum)) . "' ";
        $sql.= " AND HOSTNAME='" . htmlspecialchars(trim($hostname)) . "' ";
        $sql.= " AND TRANSFERRED_TO_HOSTNAME is null ";
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }
    
    function delete($cnum, $hostname, $transferred=null){ 
        $sql = " DELETE FROM ";
        $sql.= $GLOBALS['Db2Schema'] . "." . allTables::$DLP;
        $sql.= " WHERE ";
        $sql.= " CNUM='" . htmlspecialchars(trim($cnum)) . "' ";
        $sql.= " AND HOSTNAME='" . htmlspecialchars(trim($hostname)) . "' ";
        $sql.= empty(($transferred)) ? " AND TRANSFERRED_TO_HOSTNAME is null " : " AND TRANSFERRED_TO_HOSTNAME='" . htmlspecialchars(trim(strtoupper($transferred))) . "' ";
        
        $rs = sqlsrv_query($GLOBALS['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            return false;
        }
        
        return true;
    }
};