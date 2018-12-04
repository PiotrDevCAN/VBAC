<?php
namespace vbac;

use itdq\DbTable;
use itdq\Loader;
use vbac\pesTrackerRecord;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use \DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\xls;

class pesTrackerTable extends DbTable{
    
    use xls;
    
    protected $preparedStageUpdateStmts;
    protected $preparedTrackerInsert;
    protected $preparedGetPesCommentStmt;
    protected $preparedProcessStatusUpdate;
    
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
                $pesStatusPredicate = "  P.PES_STATUS in('" . personRecord::PES_STATUS_REQUESTED . "','" . personRecord::PES_STATUS_INITIATED. "','" . personRecord::PES_STATUS_PROVISIONAL. "') ";
                
                break;
            case self::PES_TRACKER_RECORDS_NOT_ACTIVE :
                $pesStatusPredicate = " P.PES_STATUS not in ('" . personRecord::PES_STATUS_REQUESTED . "','" . personRecord::PES_STATUS_INITIATED. "','" . personRecord::PES_STATUS_PROVISIONAL. "')  ";
                $pesStatusPredicate.= " AND PT.PROCESSING_STATUS_CHANGED > current timestamp - 31 days AND PT.CNUM is not null ";
                break;
            case self::PES_TRACKER_RECORDS_ALL :
                $pesStatusPredicate = " PT.CNUM is not null ";
                break;
            default:
                $pesStatusPredicate = 'pass a parm muppet ';
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
        $sql.= ", P.PES_STATUS_DETAILS ";
        $sql.= ", PT.COMMENT ";
        $sql.= ", PT.PRIORITY ";
        $sql.= ", P.OPEN_SEAT_NUMBER ";
        
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
    
    static function preProcessRowForWriteToXls($row){       
        $breaks = array("<br/>","</br/>");
        $comment = str_ireplace($breaks, "\r\n", $row['COMMENT']);         
        $row['COMMENT'] = strip_tags($comment);
        return $row;
    }
    
    function buildTable($records='Active'){
        $allRows = self::returnPesEventsTable($records,self::PES_TRACKER_RETURN_RESULTS_AS_ARRAY);
        ob_start();
        ?>
        <table id='pesTrackerTable' class='table table-striped table-bordered display compact nowrap '  style='width:100%'>
		<thead>
		<tr><th>Email Address</th><th>Requestor</th><th>Country</th>
		<th>Consent Form</th><th>Proof of Right to Work</th><th>Proof of ID</th><th>Proof of Residency</th><th>Credit Check</th>
		<th>Financial Sanctions</th><th>Criminal Records Check</th><th>Proof of Activity</th>
		<th>Process Status</th><th>PES Status</th><th>Comment</th>		
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
            
            $formattedIdentityField = self::formatEmailFieldOnTracker($row);
            
            ?>
            <tr class='<?=$cnum;?>'>
            <td class='formattedEmailTd'>
            <div class='formattedEmailDiv'><?=$formattedIdentityField;?></div>
            </td>
            <td><?=$row['PES_REQUESTOR']?><br/><small><?=$row['PES_DATE_REQUESTED']?><br/><?=$age?></small></td>
            <td><?=trim($row['COUNTRY'])?></td>
                        
            <?php 
            foreach (self::PES_TRACKER_STAGES as $stage) {
                $stageValue         = !empty($row[$stage]) ? trim($row[$stage]) : 'TBD';
                $stageAlertValue    = self::getAlertClassForPesStage($stageValue);
                ?>
                <td class='nonSearchable'> 
            	<?=self::getButtonsForPesStage($stageValue, $stageAlertValue, $stage, $cnum);?>
                </td>
                <?php 
            }
        ?>				
            <td class='nonSearchable'>
            <div class='alert alert-info text-center pesProcessStatusDisplay' role='alert' ><?=self::formatProcessingStatusCell($row);?></div>              
            <div class='text-center'  data-cnum='<?=$cnum?>'>        
            <span style='white-space:nowrap' >
            <a class="btn btn-xs btn-info  btnProcessStatusChange accessPes accessCdi" 		data-processstatus='PES' data-toggle="tooltip" data-placement="top" title="With PES Team" ><i class="fas fa-users"></i></a>
            <a class="btn btn-xs btn-info  btnProcessStatusChange accessPes accessCdi" 		data-processstatus='User' data-toggle="tooltip" data-placement="top" title="With Applicant" ><i class="fas fa-user"></i></a>
            <a class="btn btn-xs btn-info   btnProcessStatusChange accessPes accessCdi" 	data-processstatus='CRC' data-toggle="tooltip" data-placement="top" title="Awaiting CRC"><i class="fas fa-gavel"></i></a>
            <button class='btn btn-info btn-xs  btnProcessStatusChange accessPes accessCdi' data-processstatus='Unknown' data-toggle="tooltip"  title="Unknown"><span class="glyphicon glyphicon-erase" ></span></button>
            </span>
            <?php 
            $dateLastChased = !empty($row['DATE_LAST_CHASED']) ? DateTime::createFromFormat('Y-m-d', $row['DATE_LAST_CHASED']) : null;
            $dateLastChasedFormatted = !empty($row['DATE_LAST_CHASED']) ? $dateLastChased->format('d M Y') : null;
            $alertClass = !empty($row['DATE_LAST_CHASED']) ? self::getAlertClassForPesChasedDate($row['DATE_LAST_CHASED']) : 'alert-info';
            ?>
            <div class='alert <?=$alertClass;?>'>
            <input class="form-control input-sm pesDateLastChased" value="<?=$dateLastChasedFormatted?>" type="text" placeholder='Last Chased' data-toggle='tooltip' title='PES Date Last Chased' data-cnum='<?=$cnum?>'> 
            </div>           
            </div>
            </td>
            <td class='nonSearchable'><?=personTable::getPesStatusWithButtons($row)?></td>
            <td class='pesCommentsTd'><textarea rows="3" cols="20"  data-cnum='<?=$cnum?>'></textarea><br/>
            <button class='btn btn-default btn-xs btnPesSaveComment accessPes accessCdi' data-setpesto='Yes' data-toggle="tooltip" data-placement="top" title="Save Comment" ><span class="glyphicon glyphicon-save" ></span></button>
            <div class='pesComments'><small><?=$row['COMMENT']?></small></div>
            </td>
            </tr>
        <?php 
        }        
        ?>
        </tbody>
		</table>
		<?php 
		$table = ob_get_clean();
		return $table;
    }
    
    
    
    
    function displayTable($records='Active'){       
        ?>
        <div class='container-fluid' >
        <div class='col-sm-8 col-sm-offset-2'>
          <form class="form-horizontal">
  			<div class="form-group">
    			<label class="control-label col-sm-1" for="pesTrackerTableSearch">Table Search:</label>
    			<div class="col-sm-3" >
      			<input type="text" id="pesTrackerTableSearch" placeholder="Search"  onkeyup=searchTable()  />
      			<br/>

				</div>
    			
    			<label class="control-label col-sm-1" for="pesRecordFilter">Records:</label>
    			<div class="col-sm-3" >    			
    			<div class="btn-group" role="group" aria-label="Record Selection">
  					<button type="button" role='button'  class="btn btn-info btnRecordSelection active" data-pesrecords='Active'  data-toggle='tooltip'  title='Active Records'     >Active</button>
  					<button type="button" role='button'  class="btn btn-info btnRecordSelection" data-pesrecords='Not Active'     data-toggle='tooltip'  title='Recently Closed'  >Recent</button>
  					<button type="button" role='button'  class="btn btn-info btnRecordSelection" data-pesrecords='All'      >All</button>
				</div>
				</div>    			
    			
    			<label class="control-label col-sm-1" for="pesPriorityFilter">Filters:</label>
    			<div class="col-sm-2" >
  				<span style='white-space:nowrap' id='pesPriorityFilter' >
  				<button class='btn btn-sm btn-danger  btnSelectPriority accessPes accessCdi' data-pespriority='1'  data-toggle='tooltip'  title='Filter on High'  type='button' onclick='return false;'><span class='glyphicon glyphicon-king' ></span></button>
            	<button class='btn btn-sm btn-warning btnSelectPriority accessPes accessCdi' data-pespriority='2'  data-toggle='tooltip'  title='Filter on Medium' type='button' onclick='return false;'><span class='glyphicon glyphicon-knight' ></span></button>
            	<button class='btn btn-sm btn-success btnSelectPriority accessPes accessCdi' data-pespriority='3'  data-toggle='tooltip'  title='Filter on Low' type='button' onclick='return false;'><span class='glyphicon glyphicon-pawn' ></span></button>
            	<button class='btn btn-sm btn-info    btnSelectPriority accessPes accessCdi' data-pespriority='0'  data-toggle='tooltip'  title='Filter off' type='button' onclick='return false;'><span class='glyphicon glyphicon-ban-circle' ></span></button>
            	<br/><br/>
            	<a class="btn btn-sm btn-info  btnSelectProcess accessPes accessCdi" 		data-pesprocess='PES' data-toggle="tooltip" data-placement="top" title="With PES Team" ><i class="fas fa-users"></i></a>
                <a class="btn btn-sm btn-info  btnSelectProcess accessPes accessCdi" 		data-pesprocess='User' data-toggle="tooltip" data-placement="top" title="With Applicant" ><i class="fas fa-user"></i></a>
                <a class="btn btn-sm btn-info   btnSelectProcess accessPes accessCdi" 	    data-pesprocess='CRC' data-toggle="tooltip" data-placement="top" title="Awaiting CRC"><i class="fas fa-gavel"></i></a>
                <button class='btn btn-info btn-sm  btnSelectProcess accessPes accessCdi'   data-pesprocess='Unknown' data-toggle="tooltip"  title="Status Unknown" type='button' onclick='return false;'><span class="glyphicon glyphicon-erase" ></span></button>
              	</span>
              	</div>
              	<div class="col-sm-1"  >              
              	<span style='white-space:nowrap' id='pesDownload' >              	
				<a class='btn btn-sm btn-link accessBasedBtn accessPes accessCdi' href='/dn_pesTracker.php'><i class="glyphicon glyphicon-download-alt"></i> PES Tracker</a>
				</span>
            	</div>         
  			</div>  			
		  </form> 
		  </div>	
		</div>
       
		<div id='pesTrackerTableDiv' class='center-block'>		
		</div> 
		<?php 
    }
    
    static function formatEmailFieldOnTracker($row){
        
        $priority = !empty($row['PRIORITY']) ? ucfirst(trim($row['PRIORITY'])) : 'TBD';
        
        switch (trim($row['PRIORITY'])){
            case 'High':
            case 1:
                $alertClass='alert-danger';
                break;
            case 'Medium':
            case 2:
                $alertClass='alert-warning';
                break;
            case 'Low':
            case 3:
                $alertClass='alert-success';
                break;
            default:
                $alertClass='alert-info';
                break;
        }
        
        $formattedField = trim($row['EMAIL_ADDRESS']) . "<br/><small>";
        $formattedField.= "<i>" . trim($row['PASSPORT_FIRST_NAME']) . "&nbsp;<b>" . trim($row['PASSPORT_SURNAME']) . "</b></i><br/>";
        $formattedField.= trim($row['FIRST_NAME']) . "&nbsp;<b>" . trim($row['LAST_NAME']) . "</b></small><br/>" . trim($row['CNUM']);
        $formattedField.= "<div class='alert $alertClass priorityDiv'>Priority:" . $priority . "</div>";
        
        $formattedField.="<span style='white-space:nowrap' >
            <button class='btn btn-xs btn-danger  btnPesPriority accessPes accessCdi' data-pespriority='1' data-cnum='" . $row['CNUM'] ."'  data-toggle='tooltip'  title='High' ><span class='glyphicon glyphicon-king' ></button>
            <button class='btn btn-xs btn-warning  btnPesPriority accessPes accessCdi' data-pespriority='2' data-cnum='" . $row['CNUM'] ."' data-toggle='tooltip'  title='Medium' ><span class='glyphicon glyphicon-knight' ></button>
            <button class='btn btn-xs btn-success  btnPesPriority accessPes accessCdi' data-pespriority='3' data-cnum='" . $row['CNUM'] ."' data-toggle='tooltip'  title='Low'><span class='glyphicon glyphicon-pawn' ></button>
            <button class='btn btn-xs btn-info btnPesPriority accessPes accessCdi' data-pespriority='99' data-cnum='" . $row['CNUM'] ."'data-toggle='tooltip'  title='Unknown'><span class='glyphicon glyphicon-erase' ></button>
            </span>";
        
        
        return $formattedField;
    }   
    
    static function formatProcessingStatusCell($row){
        $processingStatus = empty($row['PROCESSING_STATUS']) ? 'Unknown' : trim($row['PROCESSING_STATUS']) ;
        $today = new \DateTime();
        $date = DateTime::createFromFormat('Y-m-d H:i:s', substr($row['PROCESSING_STATUS_CHANGED'],0,19));
        $age  = !empty($row['PROCESSING_STATUS_CHANGED']) ?  $date->diff($today)->format('%R%a days') : null ;        

        echo $processingStatus;?><br/><small><?=substr(trim($row['PROCESSING_STATUS_CHANGED']),0,10);?><br/><?=$age?></small><?php 
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
    
    static function getAlertClassForPesChasedDate($pesChasedDate){
        $today = new \DateTime();
        $date = DateTime::createFromFormat('Y-m-d', $pesChasedDate);
        $age  = $date->diff($today)->d;           
        
        switch (true) {
            case $age < 7 :
                $alertClass = ' alert-success ';
                break;
            case $age < 14:
                $alertClass = ' alert-warning ';
                break;
            default:
                $alertClass = ' alert-danger ';
                break;
        }
        return $alertClass;
    }
    
    
    
    static function getButtonsForPesStage($value, $alertClass, $stage, $cnum){
        ?>
        <div class='alert <?=$alertClass;?> text-center pesStageDisplay' role='alert' ><?=$value;?></div>              
        <div class='text-center' data-pescolumn='<?=$stage?>' data-cnum='<?=$cnum?>'>
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
       
        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);
        
         if($preparedStmt){
             $this->preparedStageUpdateStmts[strtoupper(db2_escape_string($stage))] = $preparedStmt;
         }
         
         return $preparedStmt;
    }
    
    function prepareProcessStatusUpdate(){
        if(isset($this->preparedProcessStatusUpdate )) {
            return $this->prepareProcessStatusUpdate;
        }
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PROCESSING_STATUS =?, PROCESSING_STATUS_CHANGED = current timestamp ";
        $sql.= " WHERE CNUM=? ";
        
        $this->preparedSelectSQL = $sql;
       
        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);
        
        if($preparedStmt){
            $this->prepareProcessStatusUpdate = $preparedStmt;
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
    
    function setPesProcessStatus($cnum,$processStatus){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));
        
        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }
        $preparedStmt = $this->prepareProcessStatusUpdate();
        $data = array($processStatus,$cnum);
        
        $rs = db2_execute($preparedStmt,$data);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update PES Process Status $processStatus for $cnum");
        }
        
        return true;
    } 
    
    function savePesPriority($cnum,$pesPriority=null){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));
        
        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }
        
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PRIORITY=";
        $sql.= !empty($pesPriority) ? "'" . db2_escape_string($pesPriority) . "' " : " null, ";
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";
        
        $rs = db2_exec($_SESSION['conn'],$sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update Pes Priority: $pesPriority for $cnum");
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
    
    function setPesDateLastChased($cnum,$dateLastChased){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));
        
        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }
        
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET DATE_LAST_CHASED=DATE('" . db2_escape_string($dateLastChased) . "') ";
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";
        
        $rs = db2_exec($_SESSION['conn'],$sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception("Failed to update Date Last Chased to : $dateLastChased for $cnum");
        }
        
        return true;
    }
    
    function savePesComment($cnum,$comment){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));
        
        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }
        
        $existingComment = $this->getPesComment($cnum);
        $now = new \DateTime();
        
        $newComment = trim($comment) . "<br/><small>" . $_SESSION['ssoEmail'] . ":" . $now->format('Y-m-d H:i:s') . "</small><br/>" . $existingComment;
        
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET COMMENT='" . db2_escape_string($newComment) . "' ";
        $sql.= " WHERE CNUM='" . db2_escape_string($cnum) . "' ";
        
        $rs = db2_exec($_SESSION['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception("Failed to update PES Comment for $cnum. Comment was " . $comment);
        }
        
        return $newComment;
    }
    
    function prepareGetPesCommentStmt(){
        if(!empty($this->preparedGetPesCommentStmt)){
            return $this->preparedGetPesCommentStmt;
        }
        
        $sql = " SELECT COMMENT FROM " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " WHERE CNUM=? ";
        
        $preparedStmt = db2_prepare($_SESSION['conn'], $sql);
        
        if($preparedStmt){
            $this->preparedGetPesCommentStmt = $preparedStmt;
            return $preparedStmt;
        }
        
        throw new \Exception('Unable to prepare GetPesComment');
        return false;
        
    }
    
    
    function getPesComment($cnum){
        $preparedStmt = $this->prepareGetPesCommentStmt();
        
        $data = array($cnum);
        
        $rs = db2_execute($preparedStmt,$data);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'Prepared Stmt');
            throw new \Exception('Unable to getPesComment for ' . $cnum);
        }
        
        $row = db2_fetch_assoc($preparedStmt);        
        return $row['COMMENT'];
    }
    
    
    
    function getTracker($records=self::PES_TRACKER_RECORDS_ACTIVE, Spreadsheet $spreadsheet){
        $sheet = 1;
        
        $rs = self::returnPesEventsTable($records, pesTrackerTable::PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET);
        
        if($rs){
            $recordsFound = static::writeResultSetToXls($rs, $spreadsheet);
            
            if($recordsFound){
                static::autoFilter($spreadsheet);
                static::autoSizeColumns($spreadsheet);
                static::setRowColor($spreadsheet,'105abd19',1);
            }
        }
        
        if(!$recordsFound){
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 1, "Warning");
            $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow(1, 2,"No records found");
        }
        // Rename worksheet & create next.

        $spreadsheet->getActiveSheet()->setTitle('Record ' . $records);
        $spreadsheet->createSheet();
        $spreadsheet->setActiveSheetIndex($sheet++);
        
        return true;
    }
    
    
    function changeCnum($fromCnum,$toCnum){
        $sql = " UPDATE " . $_SESSION['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET CNUM='" . db2_escape_string(trim($fromCnum)) . "' ";
        $sql.= " WHERE CNUM='" . db2_escape_string(trim($toCnum)) . "' ";
       
        $rs = db2_exec($_SESSION['conn'], $sql);
        
        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }
        
        return $rs;
        
     }
    
}
