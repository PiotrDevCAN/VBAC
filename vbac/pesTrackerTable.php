<?php
namespace vbac;

use itdq\DbTable;
use itdq\Loader;
use vbac\pesTrackerRecord;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use \DateTime;

class pesTrackerTable extends DbTable{
    
    protected $preparedStageUpdateStmts;
    protected $preparedTrackerInsert;
    
    const PES_TRACKER_RECORDS_ACTIVE     = 'Active';
    const PES_TRACKER_RECORDS_NOT_ACTIVE = 'Not Active';
    const PES_TRACKER_RECORDS_ALL        = 'All';
    
    const PES_TRACKER_RETURN_RESULTS_AS_ARRAY      = 'array';
    const PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET = 'resultSet';
    
    
    const PES_TRACKER_STAGE_CONSENT = 'Consent Form';
    const PES_TRACKER_STAGE_WORK    = 'Right to Work';
    const PES_TRACKER_STAGE_ID      = 'Proof of Id';
    const PES_TRACKER_STAGE_RESIDENCY = 'Residency';
    const PES_TRACKER_STAGE_CREDIT  = 'Credit Check';
    const PES_TRACKER_STAGE_SANCTIONS = 'Financial Sanctions';
    const PES_TRACKER_STAGE__CRIMINAL = 'Criminal Records Check';
    const PES_TRACKER_STAGE__ACTIVITY = 'Activity';
    
    
    const PES_TRACKER_STAGES =  array('CONSENT','RIGHT_TO_WORK','PROOF_OF_ID','PROOF_OF_RESIDENCY','CREDIT_CHECK','FINANCIAL_SANCTIONS','CRIMINAL_RECORDS_CHECK','PROOF_OF_ACTIVITY');
    
    
    static function returnPesEventsTable($records='Active',$returnResultsAs='array'){
  
        switch ($records){
            case self::PES_TRACKER_RECORDS_ACTIVE :
                $pesStatusPredicate = "  P.PES_STATUS in('" . personRecord::PES_STATUS_REQUESTED . "','" . personRecord::PES_STATUS_INITIATED. "') ";
                break;
            case self::PES_TRACKER_RECORDS_NOT_ACTIVETR :
                $pesStatusPredicate = " P.PES_STATUS !in ('" . personRecord::PES_STATUS_REQUESTED . "','" . personRecord::PES_STATUS_INITIATED. "') ";
                break;
            case self::PES_TRACKER_RECORDS_ALL :
            default:
                $pesStatusPredicate = '';
                break;
        }        
        
        $sql = " SELECT P.CNUM ";
        $sql.= ", P.EMAIL_ADDRESS  "; 
        $sql.= ", P.NOTES_ID  ";
        $sql.= ", PT.PASSPORT_FIRST_NAME ";
        $sql.= ", PT.PASSPORT_SURNAME ";
        $sql.= ", case when PT.PASSPORT_FIRST_NAME is null then P.FIRST_NAME else PT.PASSPORT_FIRST_NAME end as FIRST_NAME  ";
        $sql.= ", case when PT.PASSPORT_SURNAME is null then P.LAST_NAME else PT.PASSPORT_SURNAME end as LAST_NAME  ";
        $sql.= ", PT.PASSPORT_SURNAME ";        
        $sql.= ", P.FIRST_NAME ";
        $sql.= ", P.LAST_NAME ";
        $sql.= ", P.COUNTRY ";
        $sql.= ", P.LOB ";
        $sql.= ", P.PES_DATE_REQUESTED ";
        $sql.= ", P.PES_REQUESTOR ";
        $sql.= ", PT.JML ";
        $sql.= ", PT.CONSENT ";
        $sql.= ", PT.RIGHT_TO_WORK ";
        $sql.= ", PT.PROOF_OF_ID ";
        $sql.= ", PT.PROOF_OF_RESIDENCY ";
        $sql.= ", PT.CREDIT_CHECK ";
        $sql.= ", PT.FINANCIAL_SANCTIONS ";
        $sql.= ", PT.CRIMINAL_RECORDS_CHECK ";
        $sql.= ", PT.PROOF_OF_ACTIVITY ";
        $sql.= ", PT.PROCESSING_STATUS ";
        $sql.= ", PT.PROCESSING_STATUS_CHANGED ";
        $sql.= ", PT.DATE_LAST_CHASED ";
        $sql.= ", P.PES_STATUS ";
        $sql.= ", PT.COMMENT ";
        
        $sql.= " FROM " . $_SESSION['Db2Schema'] . "." . allTables::$PERSON . " as P ";        
        $sql.= " left join " . $_SESSION['Db2Schema'] . "." . \vbac\allTables::$PES_TRACKER . " as PT ";
        $sql.= " ON P.CNUM = PT.CNUM ";        
        $sql.= " WHERE 1=1 ";
        $sql.= " AND " . $pesStatusPredicate;
        
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception('Error in ' . __METHOD__ . " running $sql");
        }
        
        switch ($returnResultsAs) {
            case self::PES_TRACKER_RETURN_RESULTS_AS_ARRAY:
                $report = array();                
                while(($row=db2_fetch_assoc($rs))==true){
                    $report[] = $row;
                }                
                return $report;
            break;
            case self::PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET:
                return $rs;            
            default:
                return false;
            break;
        }
    }   
    
    
    function displayTable($records='Active'){
        $allRows = self::returnPesEventsTable($records,self::PES_TRACKER_RETURN_RESULTS_AS_ARRAY);
        ?>
        <table id='pesTrackerTable' class='table table-striped table-bordered compact'  style='width:100%'>
		<thead>
		<tr><th>Email Address</th><th>Requestor</th><th>Country</th><th>JML</th>
		<th>Consent Form</th><th>Proof of Right to Work</th><th>Proof of ID</th><th>Proof of Residency</th><th>Credit Check</th>
		<th>Financial Sanctions</th><th>Criminal Records Check</th><th>Proof of Activity</th>
		<th>Process Status</th><th>Date Last Chased</th><th>PES Status</th><th>Comment</th>		
		</tr>
		</thead>
		<tbody>
		<?php 

        foreach ($allRows as $row){
            $today = new \DateTime();
            $date = DateTime::createFromFormat('Y-m-d', $row['PES_DATE_REQUESTED']);
            $age  = !empty($row['PES_DATE_REQUESTED']) ?  $date->diff($today)->format('%R%a days') : null ;
            // $age = !empty($row['PES_DATE_REQUESTED']) ? $interval->format('%R%a days') : null;
            $cnum = $row['CNUM']; 
            
//             $consentValue = !empty($row['CONSENT']) ? trim($row['CONSENT']) : 'TBD';
//             $consentAlertClass = self::getAlertClassForPesStage($consentValue);
            
//             $rightToWorkValue = !empty($row['RIGHT_TO_WORK']) ? trim($row['RIGHT_TO_WORK']) : 'TBD';
//             $rightToWorkAlertClass = self::getAlertClassForPesStage($rightToWorkValue);
            
//             $proofOfIdValue = !empty($row['PROOF_OF_ID']) ? trim($row['PROOF_OF_ID']) : 'TBD';
//             $proofOfIdAlertClass = self::getAlertClassForPesStage($proofOfIdValue);
            
//             $proofOfResidencyValue = !empty($row['PROOF_OF_RESIDENCY']) ? trim($row['PROOF_OF_RESIDENCY']) : 'TBD';
//             $proofOfResidencyAlertClass = self::getAlertClassForPesStage($proofOfResidencyValue);
            
//             $creditCheckValue = !empty($row['CREDIT_CHECK']) ? trim($row['CREDIT_CHECK']) : 'TBD';
//             $creditCheckAlertValue = self::getAlertClassForPesStage($creditCheckValue);

//             $creditCheckValue = !empty($row['CREDIT_CHECK']) ? trim($row['CREDIT_CHECK']) : 'TBD';
//             $creditCheckAlertValue = self::getAlertClassForPesStage($creditCheckValue);
            
            
 
            

            
            
            ?>
            <tr data-cnum='<?=$cnum?>' >
            <td>
			<?=$row['EMAIL_ADDRESS']?>
            <br/><small>
            <i><?=$row['PASSPORT_FIRST_NAME']?><b><?=$row['PASSPORT_SURNAME']?></b></i><br/>
            <?=$row['FIRST_NAME']?><b><?=$row['LAST_NAME']?></b>            
            </small>      
            <br/><?=$row['CNUM']?>      
            </td>
            <td><?=$row['PES_REQUESTOR']?><br/><small><?=$row['PES_DATE_REQUESTED']?><br/><?=$age?></small></td>
            <td><?=trim($row['COUNTRY'])?></td>
            <td><?=$row['JML']?></td>
            
            <?php 
            foreach (self::PES_TRACKER_STAGES as $stage) {
                $stageValue         = !empty($row[$stage]) ? trim($row[$stage]) : 'TBD';
                $stageAlertValue    = self::getAlertClassForPesStage($stageValue);
                ?>
                <td  data-pescolumn='<?=$stage?>	'> 
            	<?=self::getButtonsForPesStage($stageValue, $stageAlertValue);?>
                </td>
                <?php 
            }
        ?>				
            <td><?=$row['PROCESSING_STATUS']?><br/><?=$row['PROCESSING_STATUS_CHANGED']?></td>
            <td><?=$row['DATE_LAST_CHASED']?></td>
            <td><?=personTable::getPesStatusWithButtons($row)?></td>
            <td><textarea rows="3" cols="20"></textarea><br/><small><?=$row['COMMENT']?></small></td>
            </tr>
        <?php 
        }        
        ?>
        </tbody>
		</table>
		<?php 

    }
     
    
    static function getAlertClassForPesStage($pesStageValue=null){
        switch ($pesStageValue) {
            case 'Yes':
                $alertClass = ' alert-success ';
                break;
            case 'Prov':
                $alertClass = ' alert-warning ';
                break;
            case 'N/A':
                $alertClass = ' alert-secondary ';
                break;
            default:
                $alertClass = ' alert-info ';
                break;
        }
        return $alertClass;
    }
    
    
    
    static function getButtonsForPesStage($value, $alertClass){
        ?>
        <div class='alert <?=$alertClass;?> text-center pesStageDisplay' role='alert'><?=$value;?></div>              
        <div class='text-center'>
        <span style='white-space:nowrap' >
        <button class='btn btn-success btn-xs btnPesStageValueChange accessPes accessCdi' data-setpesto='Yes' data-toggle="tooltip" data-placement="top" title="Cleared" ><span class="glyphicon glyphicon-ok-sign" ></span></button> 
  		<button class='btn btn-warning btn-xs btnPesStageValueChange accessPes accessCdi'  data-setpesto='Prov' data-toggle="tooltip"  title="Stage Cleared Provisionally"><span class="glyphicon glyphicon-alert" ></span></button>
	  	<button class='btn btn-default btn-xs btnPesStageValueChange accessPes accessCdi' data-setpesto='N/A' data-toggle="tooltip"  title="Not applicable"><span class="glyphicon glyphicon-remove-sign" ></span></button>
	  	<button class='btn btn-info btn-xs btnPesStageValueChange accessPes accessCdi' data-setpesto='TBD'data-toggle="tooltip"  title="Clear Field"><span class="glyphicon glyphicon-erase" ></span></button>
	  	</span>
	  	</div>
        <?php 
    }
    
    function prepareStageUpdate($stage){
        
        if(isset($this->preparedStageUpdateStmts[strtoupper(db2_escape_string($stage))] )) {
            return $this->preparedStageUpdateStmts[strtoupper(db2_escape_string($stage))];
        }
        
        
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET " . strtoupper(db2_escape_string($stage)) . " =? ";
        $sql.= " WHERE CNUM=? ";
        
        $this->preparedSelectSQL = $sql;
        
        echo $sql;
        
        
         $preparedStmt = db2_prepare($_SESSION['conn'], $sql);
        
         if($preparedStmt){
             $this->preparedStageUpdateStmts[strtoupper(db2_escape_string($stage))] = $preparedStmt;
         }
         
         return $preparedStmt;
    }
    
    function prepareTrackerInsert(){        
        if(isset($this->preparedTrackerInsert )) {
            return $this->preparedTrackerInsert;
        }
        $sql = " INSERT INTO " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " ( CNUM ) VALUES (?) ";        
        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);
        
        if($preparedStmt){
            $this->preparedTrackerInsert = $preparedStmt;            
            return $preparedStmt;
        }

        return false;
        
    }
    
    function createNewTrackerRecord($cnum){
        $preparedStmt = $this->prepareTrackerInsert();
        $data = array($cnum);
        
        $rs = db2_execute($preparedStmt,$data);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception('Unable to create blank Tracker record for ' . $cnum);
        }
        
        return;        
        
    }
    
    
    function setPesStageValue($cnum,$stage,$stageValue){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));
        
        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);            
        }         
        $preparedStmt = $this->prepareStageUpdate($stage);
        $data = array($stageValue,$cnum);       
        
        $rs = db2_execute($preparedStmt,$data);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update PES Stage: $stage to $stageValue for $cnum");
        }
        
       return true;
    } 
    
    function setPesPassportNames($cnum,$passportFirstname=null,$passportSurname=null){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));
        
        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }
        
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PASSPORT_FIRST_NAME=";
        $sql.= !empty($passportFirstname) ? "'" . db2_escape_string($passportFirstname) . "', " : " null, ";
        $sql.= " PASSPORT_SURNAME=";
        $sql.= !empty($passportSurname) ? "'" . db2_escape_string($passportSurname) . "'  " : " null ";
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";
                               
        $rs = db2_exec($_SESSION['conn'],$sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update Passport Names: $passportFirstname  / $passportSurname for $cnum");
        }
        
        return true;
    } 
    
}