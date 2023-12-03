<?php
namespace vbac;

use itdq\DbTable;
use itdq\Loader;
use vbac\pesTrackerRecord;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use \DateTime;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use itdq\xls;
use itdq\AuditTable;

class pesTrackerTable extends DbTable{

    use xls;

    public $lastSql;

    const PES_TRACKER_RECORDS_ACTIVE     = 'Active';
    const PES_TRACKER_RECORDS_ACTIVE_PLUS  = 'Active Plus';
    const PES_TRACKER_RECORDS_NOT_ACTIVE = 'Not Active';
    const PES_TRACKER_RECORDS_ALL        = 'All';
    const PES_TRACKER_RECORDS_ACTIVE_REQUESTED = 'Active Requested';
    const PES_TRACKER_RECORDS_ACTIVE_PROVISIONAL = 'Active Provisional';

    const PES_TRACKER_RETURN_RESULTS_AS_ARRAY      = 'array';
    const PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET = 'resultSet';

    const PES_TRACKER_STAGE_CONSENT = 'Consent Form';
    const PES_TRACKER_STAGE_WORK    = 'Right to Work';
    const PES_TRACKER_STAGE_ID      = 'Proof of Id';
    const PES_TRACKER_STAGE_RESIDENCY = 'Residency';
    const PES_TRACKER_STAGE_CREDIT  = 'Credit Check';
    const PES_TRACKER_STAGE_SANCTIONS = 'Financial Sanctions';
    const PES_TRACKER_STAGE_CRIMINAL = 'Criminal Records Check';
    const PES_TRACKER_STAGE_ACTIVITY = 'Activity';

    const PES_TRACKER_STAGES =  array('CONSENT','RIGHT_TO_WORK','PROOF_OF_ID','PROOF_OF_RESIDENCY','CREDIT_CHECK','FINANCIAL_SANCTIONS','CRIMINAL_RECORDS_CHECK','PROOF_OF_ACTIVITY');

    static function returnPesEventsTable($records='Active',$returnResultsAs='array'){

        switch (trim($records)){
            case self::PES_TRACKER_RECORDS_ACTIVE :
                $pesStatusPredicate = "  P.PES_STATUS in('" . personRecord::PES_STATUS_REQUESTED . "','" . personRecord::PES_STATUS_INITIATED. "','" . personRecord::PES_STATUS_RECHECK_PROGRESSING. "','" . "','" . personRecord::PES_STATUS_RESTART. "','" . personRecord::PES_STATUS_PROVISIONAL. "','" . personRecord::PES_STATUS_RECHECK_REQ . "','" . personRecord::PES_STATUS_MOVER . "') ";
                break;
            case self::PES_TRACKER_RECORDS_ACTIVE_PLUS :
                $pesStatusPredicate = "  P.PES_STATUS in('" . personRecord::PES_STATUS_REQUESTED . "','" . personRecord::PES_STATUS_INITIATED. "','" . personRecord::PES_STATUS_RECHECK_PROGRESSING. "','" . "','" . personRecord::PES_STATUS_RESTART. "','". personRecord::PES_STATUS_PROVISIONAL. "','" . personRecord::PES_STATUS_RECHECK_REQ . "','" . personRecord::PES_STATUS_REMOVED. "','" . personRecord::PES_STATUS_CLEARED. "','" . personRecord::PES_STATUS_MOVER . "') ";
                break;
            case self::PES_TRACKER_RECORDS_ACTIVE_REQUESTED :
                $pesStatusPredicate = "  P.PES_STATUS in('" . personRecord::PES_STATUS_REQUESTED . "','" . personRecord::PES_STATUS_INITIATED. "','" . personRecord::PES_STATUS_RECHECK_PROGRESSING. "','" . "','" . personRecord::PES_STATUS_RESTART. "','" . personRecord::PES_STATUS_RECHECK_REQ . "','" . personRecord::PES_STATUS_MOVER . "') ";
                break;
            case self::PES_TRACKER_RECORDS_ACTIVE_PROVISIONAL :
                $pesStatusPredicate = "  P.PES_STATUS in('" . personRecord::PES_STATUS_PROVISIONAL. "') ";
                break;
            case self::PES_TRACKER_RECORDS_NOT_ACTIVE :
                $pesStatusPredicate = " P.PES_STATUS not in ('" . personRecord::PES_STATUS_REQUESTED . "','" . personRecord::PES_STATUS_INITIATED. "','" . personRecord::PES_STATUS_RECHECK_PROGRESSING. "','" . "','" . personRecord::PES_STATUS_RESTART. "','" . personRecord::PES_STATUS_PROVISIONAL. "','" . personRecord::PES_STATUS_RECHECK_REQ . "','" . personRecord::PES_STATUS_MOVER . "')  ";
                $pesStatusPredicate.= " AND ( PT.PROCESSING_STATUS_CHANGED > DATEADD (day, - 31, CURRENT_TIMESTAMP) OR P.PES_DATE_RESPONDED > DATEADD (day, - 31, CURRENT_TIMESTAMP) ) AND PT.CNUM is not null ";
                break;
            case self::PES_TRACKER_RECORDS_ALL :
                $pesStatusPredicate = " PT.CNUM is not null ";
                break;
            default:
                $pesStatusPredicate = 'pass a parm muppet not ' . $records;
                break;
        }

        $sql = " SELECT P.CNUM ";
        $sql.= ", P.EMAIL_ADDRESS  ";
        $sql.= ", P.KYN_EMAIL_ADDRESS  ";
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
        $sql.= ", P.PES_LEVEL ";
        $sql.= ", PT.COMMENT ";
        $sql.= ", PT.PRIORITY ";
        $sql.= ", P.OPEN_SEAT_NUMBER ";
        $sql.= ", P.REVALIDATION_STATUS ";
        $sql.= ", F.EMAIL_ADDRESS as FLM ";

        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as P ";
        $sql.= " left join " . $GLOBALS['Db2Schema'] . "." . allTables::$PES_TRACKER . " as PT ";
        $sql.= " ON P.CNUM = PT.CNUM ";
        $sql.= " left join " . $GLOBALS['Db2Schema'] . "." . allTables::$PERSON . " as F ";
        $sql.= " ON P.FM_CNUM = F.CNUM ";
        $sql.= " WHERE 1=1 ";
        $sql.= " and (PT.CNUM is not null or ( PT.CNUM is null  AND P.PES_STATUS_DETAILS is null )) "; // it has a tracker record
        $sql.= " and (P.PES_STATUS_DETAILS not like '" . personRecord::PES_STATUS_DETAILS_BOARDED_AS . "%'  or P.PES_STATUS_DETAILS is null ) ";
        $sql.= " AND " . $pesStatusPredicate;

        AuditTable::audit("SQL:<b>" . __FILE__ . __FUNCTION__ . __LINE__ . "</b>sql:" . $sql,AuditTable::RECORD_TYPE_DETAILS);

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception('Error in ' . __METHOD__ . " running $sql");
        }

        switch ($returnResultsAs) {
            case self::PES_TRACKER_RETURN_RESULTS_AS_ARRAY:
                $report = array();
                while(($row = sqlsrv_fetch_array($rs, SQLSRV_FETCH_ASSOC))==true){
                    set_time_limit(5);
                    $trimmedRow = array_map('trim', $row);
                    $report[] = $trimmedRow;
                  }
                return $report;
            break;
            case self::PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET:
                return $rs;
            default:
                return false;
            break;
        }
        set_time_limit(60);
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
        <table id='pesTrackerTable' class='table table-striped table-bordered table-condensed '  style='width:100%'>
		<thead>
		<tr class='' ><th>Email Address</th><th>Requestor</th><th>Country</th>
		<th width="5px">Consent Form</th>
		<th width="5px">Proof or Right to Work</th>
		<th width="5px">Proof of ID</th>
		<th width="5px">Proof of Residence</th>
		<th width="5px">Credit Check</th>
		<th width="5px">Financial Sanctions</th>
		<th width="5px">Criminal Records Check</th>
		<th width="5px">Proof of Activity</th>
		<th>Process Status</th><th>PES Status</th><th>Level</th><th>Comment</th></tr>
		<tr class='searchingRow wrap'><td>Email Address</td><td>Requestor</td><td>Country</td>
		<td>Consent</td>
		<td>Right to Work</td>
		<td>ID</td>
		<td>Residence</td>
		<td>Credit Check</td>
		<td>Financial Sanctions</td>
		<td>Criminal Records Check</td>
		<td>Proof of Activity</td>
		<td>Process Status</td><td>PES Status</td><td>Comment</td></tr>
		</thead>
		<tbody>
		<?php

        $today = new \DateTime();
        foreach ($allRows as $row){
            set_time_limit(60);
            
            // $date = DateTime::createFromFormat('Y-m-d H:i:s', $row['PES_DATE_REQUESTED']);
            $date = DateTime::createFromFormat('Y-m-d', $row['PES_DATE_REQUESTED']);
            $age  = !empty($row['PES_DATE_REQUESTED']) ?  $date->diff($today)->format('%R%a days') : null ;
            $cnum = $row['CNUM'];
            $firstName = trim($row['FIRST_NAME']);
            $lastName = trim($row['LAST_NAME']);
            $emailaddress = trim($row['EMAIL_ADDRESS']);
            
            $offboarded = substr($row['REVALIDATION_STATUS'],0,10)==personRecord::REVALIDATED_OFFBOARDED ? true : false;            
            $flm = !$offboarded && !empty(trim($row['FLM'])) ?  trim($row['FLM']) : null;

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
            <div class='text-center personDetails '   data-cnum='<?=$cnum;?>' data-firstname='<?=$firstName;?>' data-lastname='<?=$lastName;?>' data-emailaddress='<?=$emailaddress;?>'  data-flm='<?=$flm;?>'   >
            <span style='white-space:nowrap' >
            <a class="btn btn-xs btn-info  btnProcessStatusChange accessPes accessCdi" 		data-processstatus='PES' data-toggle="tooltip" data-placement="top" title="With PES Team" ><i class="fas fa-users"></i></a>
            <a class="btn btn-xs btn-info  btnProcessStatusChange accessPes accessCdi" 		data-processstatus='User' data-toggle="tooltip" data-placement="top" title="With Applicant" ><i class="fas fa-user"></i></a>
            <a class="btn btn-xs btn-info  btnProcessStatusChange accessPes accessCdi" 		data-processstatus='Requestor' data-toggle="tooltip" data-placement="top" title="With Requestor" ><i class="fas fa-male"></i><i class="fas fa-female"></i></a>
            <a class="btn btn-xs btn-info   btnProcessStatusChange accessPes accessCdi" 	data-processstatus='CRC' data-toggle="tooltip" data-placement="top" title="Awaiting CRC"><i class="fas fa-gavel"></i></a>
            <button class='btn btn-info btn-xs  btnProcessStatusChange accessPes accessCdi' data-processstatus='Unknown' data-toggle="tooltip"  title="Unknown"><span class="glyphicon glyphicon-erase" ></span></button>
            </span>
            <?php
            // $dateLastChased = !empty($row['DATE_LAST_CHASED']) ? DateTime::createFromFormat('Y-m-d H:i:s', $row['DATE_LAST_CHASED']) : null;
            $dateLastChased = !empty($row['DATE_LAST_CHASED']) ? DateTime::createFromFormat('Y-m-d', $row['DATE_LAST_CHASED']) : null;
            $dateLastChasedFormatted = !empty($row['DATE_LAST_CHASED']) ? $dateLastChased->format('d M Y') : null;
            $dateLastChasedWithLevel = !empty($row['DATE_LAST_CHASED']) ? $dateLastChasedFormatted . $this->extractLastChasedLevelFromComment($row['COMMENT']) : $dateLastChasedFormatted;
            $alertClass = !empty($row['DATE_LAST_CHASED']) ? self::getAlertClassForPesChasedDate($row['DATE_LAST_CHASED']) : 'alert-info';
            ?>
            <div class='alert <?=$alertClass;?>'>
            <input class="form-control input-sm pesDateLastChased" value="<?=$dateLastChasedWithLevel?>" type="text" placeholder='Last Chased' data-toggle='tooltip' title='PES Date Last Chased' data-cnum='<?=$cnum?>'>
            </div>
            <span style='white-space:nowrap' >
            <a class="btn btn-xs btn-info  btnChaser accessPes accessCdi" data-chaser='One'  data-toggle="tooltip" data-placement="top" title="Chaser One" ><i>1</i></a>
            <a class="btn btn-xs btn-info  btnChaser accessPes accessCdi" data-chaser='Two'  data-toggle="tooltip" data-placement="top" title="Chaser Two" ><i>2</i></a>
            <a class="btn btn-xs btn-info  btnChaser accessPes accessCdi" data-chaser='Three' data-toggle="tooltip" data-placement="top" title="Chaser Three"><i>3</i></a>
            </span>
            </div>
            </td>
            <td class='nonSearchable pesStatusCell'><?=personTable::getPesStatusWithButtons($row)['display']?></td>
            <td><?=pesTrackerTable::getCellContentsForPesLevel($row)?></td>
            <td class='pesCommentsTd'><textarea rows="3" cols="20"  data-cnum='<?=$cnum?>'></textarea>
            <button class='btn btn-default btn-xs btnPesSaveComment accessPes accessCdi' data-setpesto='Yes' data-toggle="tooltip" data-placement="top" title="Save Comment" ><span class="glyphicon glyphicon-save" ></span></button>
            <div class='pesComments' data-cnum='<?=$cnum?>'><small><?=$row['COMMENT']?></small></div>
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

    function extractLastChasedLevelFromComment($comment){
        $findChasedComment = strpos($comment, 'Automated PES Chaser Level');
        $level = substr($comment, $findChasedComment+27,6);
        $level = " (" . substr($level,0,strpos($level," ")) .")";
        
        return $level;
    }

    function displayTable($records='Active Initiated'){
        ?>
        <div class='container-fluid' >
        <div class='col-sm-10 col-sm-offset-1'>
          <form class="form-horizontal">
  			<div class="form-group">
    			<label class="control-label col-sm-1" for="pesTrackerTableSearch">Table Search:</label>
    			<div class="col-sm-3" >
      			<input type="text" id="pesTrackerTableSearch" placeholder="Search"  onkeyup=searchTable() width='100%' />
      			<br/>

				</div>

    			<label class="control-label col-sm-1" for="pesRecordFilter">Records:</label>
    			<div class="col-sm-4" >
    			<div class="btn-group" role="group" aria-label="Record Selection">
  					<button type="button" role='button'  class="btn btn-info btn-sm btnRecordSelection active" data-pesrecords='<?=pesTrackerTable::PES_TRACKER_RECORDS_ACTIVE_REQUESTED?>'    data-toggle='tooltip'  title='Active Record in Initiated or Requested status'     >Requested</button>
					<button type="button" role='button'  class="btn btn-info btn-sm btnRecordSelection "       data-pesrecords='<?=pesTrackerTable::PES_TRACKER_RECORDS_ACTIVE_PROVISIONAL?>'  data-toggle='tooltip'  title='Active Records in Provisional Clearance status' >Provisional</button>
  					<button type="button" role='button'  class="btn btn-info btn-sm btnRecordSelection "       data-pesrecords='<?=pesTrackerTable::PES_TRACKER_RECORDS_ACTIVE?>'              data-toggle='tooltip'  title='Active Records'     >Active</button>
  					<button type="button" role='button'  class="btn btn-info btn-sm btnRecordSelection "       data-pesrecords='<?=pesTrackerTable::PES_TRACKER_RECORDS_ACTIVE_PLUS?>'         data-toggle='tooltip'  title='Active+ Records'     >Active+</button>
  					<button type="button" role='button'  class="btn btn-info btn-sm btnRecordSelection"        data-pesrecords='<?=pesTrackerTable::PES_TRACKER_RECORDS_NOT_ACTIVE?>'          data-toggle='tooltip'  title='Recently Closed'  >Recent</button>
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
                <a class="btn btn-sm btn-info  btnSelectProcess accessPes accessCdi" 		data-pesprocess='Requestor' data-toggle="tooltip" data-placement="top" title="With Requestor" ><i class="fas fa-male"></i><i class="fas fa-female"></i></a>
                <a class="btn btn-sm btn-info   btnSelectProcess accessPes accessCdi" 	    data-pesprocess='CRC' data-toggle="tooltip" data-placement="top" title="Awaiting CRC"><i class="fas fa-gavel"></i></a>
                <button class='btn btn-info btn-sm  btnSelectProcess accessPes accessCdi'   data-pesprocess='Unknown' data-toggle="tooltip"  title="Status Unknown" type='button' onclick='return false;'><span class="glyphicon glyphicon-erase" ></span></button>
              	</span>
              	</div>
              	<div class="col-sm-1"  >
              	<span style='white-space:nowrap' id='pesDownload' >
				<a class='btn btn-sm btn-link accessBasedBtn accessPes accessCdi' href='/dn_pesTracker.php'><i class="glyphicon glyphicon-download-alt"></i> PES Tracker</a>
				<a class='btn btn-sm btn-link accessBasedBtn accessPes accessCdi' href='/dn_pesTrackerRecent.php'><i class="glyphicon glyphicon-download-alt"></i> PES Tracker(Recent)</a>
				<a class='btn btn-sm btn-link accessBasedBtn accessPes accessCdi' href='/dn_pesTrackerActivePlus.php'><i class="glyphicon glyphicon-download-alt"></i> PES Tracker(Active+)</a>
				</span>
            	</div>
  			</div>
		  </form>
		  </div>
		</div>

		<div id='pesTrackerTableDiv' class='center-block' width='100%'></div>
		<?php
    }

    static function formatEmailFieldOnTracker($row){

        $cnum = trim($row['CNUM']);
        $email = trim($row['EMAIL_ADDRESS']);
        $firstName = trim($row['FIRST_NAME']);
        $lastName = trim($row['LAST_NAME']);
        $passportFirstName = trim($row['PASSPORT_FIRST_NAME']);
        $passportSurName= trim($row['PASSPORT_SURNAME']);
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

        $formattedField = $email . "<br/>";
        $formattedField.= "<small><i>"; 
        $formattedField.= $passportFirstName . "&nbsp;<b>" . $passportSurName . "</b>";
        $formattedField.= "</i><br/>";
        $formattedField.= $firstName . "&nbsp;<b>" . $lastName . "</b>";
        $formattedField.="</small><br/>";
        $formattedField.= $cnum . "<br/>";
        if (endsWith($email, 'ocean.ibm.com')) {
            $formattedField.= trim($row['KYN_EMAIL_ADDRESS']) . "<br/>";
        }
        $formattedField.= "<div class='alert $alertClass priorityDiv'>Priority:" . $priority . "</div>";
        $formattedField.="<span style='white-space:nowrap' >
            <button class='btn btn-xs btn-danger  btnPesPriority accessPes accessCdi' data-pespriority='1' data-cnum='" . $cnum ."'  data-toggle='tooltip'  title='High' ><span class='glyphicon glyphicon-king' ></button>
            <button class='btn btn-xs btn-warning  btnPesPriority accessPes accessCdi' data-pespriority='2' data-cnum='" . $cnum ."' data-toggle='tooltip'  title='Medium' ><span class='glyphicon glyphicon-knight' ></button>
            <button class='btn btn-xs btn-success  btnPesPriority accessPes accessCdi' data-pespriority='3' data-cnum='" . $cnum ."' data-toggle='tooltip'  title='Low'><span class='glyphicon glyphicon-pawn' ></button>
            <button class='btn btn-xs btn-info btnPesPriority accessPes accessCdi' data-pespriority='99' data-cnum='" . $cnum ."'data-toggle='tooltip'  title='Unknown'><span class='glyphicon glyphicon-erase' ></button>
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

    function getProcessingStatusCell($cnum){
        $data = array($cnum);
        $preparedStmt = $this->prepareGetProcessingStatusStmt($data);
        
        $rs = sqlsrv_execute($preparedStmt);
        if($rs){
            $row = sqlsrv_fetch_array($preparedStmt, SQLSRV_FETCH_ASSOC);

            var_dump(ob_get_level());

            ob_start();
            self::formatProcessingStatusCell($row);
            $cellContents = ob_get_clean();

            var_dump(ob_get_level());

            echo ">>>>>>>>>>>>>>>>>>";
            die('another death');


            return $cellContents;
        }
        return false;
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
	  	<br/>
	  	<button class='btn btn-default btn-xs btnPesStageValueChange accessPes accessCdi' data-setpesto='N/A' data-toggle="tooltip"  title="Not applicable"><span class="glyphicon glyphicon-remove-sign" ></span></button>
	  	<button class='btn btn-info btn-xs btnPesStageValueChange accessPes accessCdi' data-setpesto='TBD'data-toggle="tooltip"  title="Clear Field"><span class="glyphicon glyphicon-erase" ></span></button>
	  	</span>
	  	</div>
        <?php
    }

    static function getCellContentsForPesLevel($row){
        $cell = $row['PES_LEVEL'];
        $cell.= "<br/><span style='white-space:nowrap' >";
        $cell.= trim($row['PES_LEVEL'])!='Level 1' ? "<a class='btn btn-xs btn-info  btnSetPesLevel accessPes accessCdi' data-cnum='" . $row['CNUM'] . "' data-level='Level 1'  data-toggle='tooltip' data-placement='top' title='Set to Level 1' ><i class='fas fa-dice-one'></i></a>" : null;
        $cell.= trim($row['PES_LEVEL'])!='Level 2' ? "<a class='btn btn-xs btn-info  btnSetPesLevel accessPes accessCdi' data-cnum='" . $row['CNUM'] . "' data-level='Level 2'  data-toggle='tooltip' data-placement='top' title='Set to Level 2' ><i class='fas fa-dice-two'></i></a>" : null;
        $cell.= "</span>";
        return $cell;
    }

    function prepareStageUpdate($stage, $data){
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET " . strtoupper(htmlspecialchars($stage)) . " = ? ";
        $sql.= " WHERE CNUM = ? ";

        $preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
        return $preparedStmt;
    }

    function prepareGetProcessingStatusStmt(){
        $sql = " SELECT PROCESSING_STATUS, PROCESSING_STATUS_CHANGED ";
        $sql.= " FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " WHERE CNUM = ? ";

        $preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql);
        return $preparedStmt;
    }

    function prepareProcessStatusUpdate($data){
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PROCESSING_STATUS =?, PROCESSING_STATUS_CHANGED = CURRENT_TIMESTAMP ";
        $sql.= " WHERE CNUM = ? ";

        $preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
        return $preparedStmt;
    }

    function prepareTrackerInsert($data){
        $sql = " INSERT INTO " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " ( CNUM ) VALUES (?) ";
        
        $preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
        return $preparedStmt;
    }

    function prepareResetForRecheck($data){
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET CONSENT = null, ";
        $sql.= " RIGHT_TO_WORK = null, ";
        $sql.= " PROOF_OF_ID = null, ";
        $sql.= " PROOF_OF_RESIDENCY = null, "; 
        $sql.= " CREDIT_CHECK = null, ";
        $sql.= " FINANCIAL_SANCTIONS = null, ";
        $sql.= " CRIMINAL_RECORDS_CHECK = null, "; 
        $sql.= " PROOF_OF_ACTIVITY = null, ";
        $sql.= " PROCESSING_STATUS = 'PES', ";
        $sql.= " PROCESSING_STATUS_CHANGED = CURRENT_TIMESTAMP, "; 
        $sql.= " DATE_LAST_CHASED = null ";
        $sql.= " WHERE CNUM = ? ";

        $preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
        return $preparedStmt;
    }

    function prepareResetForRecheckByWORKER_ID($data){
        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET CONSENT = null, ";
        $sql.= " RIGHT_TO_WORK = null, ";
        $sql.= " PROOF_OF_ID = null, ";
        $sql.= " PROOF_OF_RESIDENCY = null, "; 
        $sql.= " CREDIT_CHECK = null, ";
        $sql.= " FINANCIAL_SANCTIONS = null, ";
        $sql.= " CRIMINAL_RECORDS_CHECK = null, "; 
        $sql.= " PROOF_OF_ACTIVITY = null, ";
        $sql.= " PROCESSING_STATUS = 'PES', ";
        $sql.= " PROCESSING_STATUS_CHANGED = CURRENT_TIMESTAMP, "; 
        $sql.= " DATE_LAST_CHASED = null ";
        $sql.= " WHERE WORKER_ID = ? ";

        $preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
        return $preparedStmt;
    }

    function createNewTrackerRecord($cnum){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));

        if (!$this->existsInDb($trackerRecord)) {

            $data = array($cnum);
            $preparedStmt = $this->prepareTrackerInsert($data);

            $rs = sqlsrv_execute($preparedStmt);

            if(!$rs){
                DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
                throw new \Exception('Unable to create blank Tracker record for ' . $cnum);
            }

            return;
        }
        return false;
    }

    function resetForRecheckByCNUM($cnum){
        $this->createNewTrackerRecord($cnum); // In case there wasn't already a record.

        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));

        $data = array($cnum);
        $preparedStmt = $this->prepareResetForRecheck($data);
        
        $rs = sqlsrv_execute($preparedStmt);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception('Unable to reset for recheck Tracker record for ' . $cnum);
        }
    }

    function resetForRecheckByWORKER_ID($WORKER_ID){
        $this->createNewTrackerRecord($WORKER_ID); // In case there wasn't already a record.

        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('WORKER_ID'=>$WORKER_ID));

        $data = array($WORKER_ID);
        $preparedStmt = $this->prepareResetForRecheckByWORKER_ID($WORKER_ID);
        
        $rs = sqlsrv_execute($preparedStmt);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'prepared sql');
            throw new \Exception('Unable to reset for recheck Tracker record for ' . $WORKER_ID);
        }
    }

    function setPesStageValue($cnum,$stage,$stageValue){
        $trackerRecord = new pesTrackerRecord();
        $trackerRecord->setFromArray(array('CNUM'=>$cnum));

        if (!$this->existsInDb($trackerRecord)) {
            $this->createNewTrackerRecord($cnum);
        }
        $data = array($stageValue, $cnum);
        $preparedStmt = $this->prepareStageUpdate($stage, $data);

        $rs = sqlsrv_execute($preparedStmt);

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
        $data = array($processStatus,$cnum);
        $preparedStmt = $this->prepareProcessStatusUpdate($data);
        
        $rs = sqlsrv_execute($preparedStmt);

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

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PRIORITY=";
        $sql.= !empty($pesPriority) ? "'" . htmlspecialchars($pesPriority) . "' " : " null, ";
        $sql.= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

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

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET PASSPORT_FIRST_NAME=";
        $sql.= !empty($passportFirstname) ? "'" . htmlspecialchars($passportFirstname) . "', " : " null, ";
        $sql.= " PASSPORT_SURNAME=";
        $sql.= !empty($passportSurname) ? "'" . htmlspecialchars($passportSurname) . "'  " : " null ";
        $sql.= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

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

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET DATE_LAST_CHASED=DATE('" . htmlspecialchars($dateLastChased) . "') ";
        $sql.= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

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

        $commentFieldSize = (int)$this->getColumnLength('COMMENT');

        if(strlen($newComment)>$commentFieldSize){
            AuditTable::audit("PES Tracker Comment too long. Will be truncated.<b>Old:</b>$existingComment <br>New:$comment");
            $newComment = substr($newComment,0,$commentFieldSize-20);
        }


        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET COMMENT='" . htmlspecialchars($newComment) . "' ";
        $sql.= " WHERE CNUM='" . htmlspecialchars($cnum) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, $sql);
            throw new \Exception("Failed to update PES Comment for $cnum. Comment was " . $comment);
        }

        return $newComment;
    }

    function prepareGetPesCommentStmt($data){
        $sql = " SELECT COMMENT FROM " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " WHERE CNUM=? ";

        $preparedStmt = sqlsrv_prepare($GLOBALS['conn'], $sql, $data);
        return $preparedStmt;
    }

    function getPesComment($cnum){
        $data = array($cnum);
        $preparedStmt = $this->prepareGetPesCommentStmt($data);

        $rs = sqlsrv_execute($preparedStmt);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__, __METHOD__, 'Prepared Stmt');
            throw new \Exception('Unable to getPesComment for ' . $cnum);
        }

        $row = sqlsrv_fetch_array($preparedStmt, SQLSRV_FETCH_ASSOC);
        return $row['COMMENT'];
    }

    function getTracker($records=self::PES_TRACKER_RECORDS_ACTIVE, Spreadsheet $spreadsheet){
        $sheet = 1;

        $rs = self::returnPesEventsTable($records, pesTrackerTable::PES_TRACKER_RETURN_RESULTS_AS_RESULT_SET);

        if($rs){
            set_time_limit(62);
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

    function changeCnum($fromCnum, $toCnum){

        if (sqlsrv_begin_transaction($GLOBALS['conn']) === false) {
            die( print_r( sqlsrv_errors(), true ));
        }

        $sql = " UPDATE " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
        $sql.= " SET CNUM='" . htmlspecialchars(trim($toCnum)) . "' ";
        $sql.= " WHERE CNUM='" . htmlspecialchars(trim($fromCnum)) . "' ";

        $rs = sqlsrv_query($GLOBALS['conn'], $sql);

        if(!$rs){
            DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
            return false;
        }

        sqlsrv_commit($GLOBALS['conn']);

//         $sql = " DELETE FROM  " . $GLOBALS['Db2Schema'] . "." . $this->tableName;
//         $sql.= " WHERE CNUM='" . htmlspecialchars(trim($fromCnum)) . "' ";

//         $rs = sqlsrv_query($GLOBALS['conn'], $sql);

//         if(!$rs){
//             DbTable::displayErrorMessage($rs, __CLASS__,__METHOD__, $sql);
//             return false;
//         }

        $loader = new Loader();
        $emailAddress = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM in('" . htmlspecialchars(trim($fromCnum)) . "','" . htmlspecialchars(trim($toCnum)) . "') ");

        $this->savePesComment($toCnum, "Serial Number changed from $fromCnum to $toCnum");
        $this->savePesComment($toCnum, "Email Address changed from $emailAddress[$fromCnum] to $emailAddress[$toCnum] ");

        return true;
     }
}
