<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;
use itdq\Loader;
use itdq\JavaScript;


/**
 *
 * @author gb001399
 *
 */
class personRecord extends DbRecord
{

    protected $CNUM;
    protected $OPEN_SEAT_NUMBER;
    protected $FIRST_NAME;
    protected $LAST_NAME;

    protected $EMAIL_ADDRESS;
    protected $NOTES_ID;
    protected $LBG_EMAIL;

    protected $EMPLOYEE_TYPE;

    protected $FM_CNUM;
    protected $FM_MANAGER_FLAG;

    protected $CTB_RTB;
    protected $TT_BAU;
    protected $LOB;
    protected $ROLE_ON_THE_ACCOUNT;
    protected $ROLE_TECHNOLOGY;

    protected $START_DATE;
    protected $PROJECTED_END_DATE;

    protected $COUNTRY;
    protected $IBM_BASE_LOCATION;
    protected $LBG_LOCATION;

    protected $OFFBOARDED_DATE;

    protected $PES_DATE_REQUESTED;
    protected $PES_REQUESTOR;
    protected $PES_DATE_RESPONDED;
    protected $PES_STATUS_DETAILS;
    protected $PES_STATUS;


    protected $REVALIDATION_DATE_FIELD;
    protected $REVALIDATION_STATUS;

    protected $CBN_DATE_FIELD;
    protected $CBN_STATUS;

    protected $WORK_STREAM;
    protected $CONTRACTOR_ID_REQUIRED;


    protected $person_bio;

    // Fields to be edited in the DataTables Reports. Need to know their position in the array $row;
    const FIELD_CNUM = 0;
    const FIELD_NOTES_ID = 5;
    const FIELD_FM_FLAG = 9;
    const FIELD_LOB = 12;
    const FIELD_ROLE_ON_THE_ACCOUNT = 13;
    const FIELD_COUNTRY = 17;
    const FIELD_PES_DATE_REQUESTED = 21;
    const FIELD_PES_REQUESTOR = 22;
    const FIELD_PES_DATE_RESPONDED = 23;
    const FIELD_PES_STATUS_DETAILS = 24;
    const FIELD_PES_STATUS = 25;


   // private static $pesTaskId = 'lbgvetpr@uk.ibm.com';
    private static $pesTaskId = 'rob.daniel@uk.ibm.com';
    private static $pesEmailBody = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
                             <tr><td align="center">
                                <table width="50%">
                                    <tr><td colspan="2" style="font-size:16px;padding-bottom:10px"">Please initiate PES check for the following individual:</td></tr>
                                    <tr><th style="background-color:silver;font-size:20px">Name</th><td style="font-size:20px">&&name&&</td></tr>
                                    <tr><th style="background-color:silver;font-size:20px">Email Address</th><td style="font-size:20px">&&email&&</td></tr>
                                    <tr><th style="background-color:silver;font-size:20px">Notes Id</th><td style="font-size:20px">&&notesid&&</td></tr>
                                    <tr><th style="background-color:silver;font-size:20px">Country working in </th><td style="font-size:20px">&&country&&</td></tr>
                                    <tr><th style="background-color:silver;font-size:20px">LoB</th><td style="font-size:20px">&&lob&&</td></tr>
                                    <tr><th style="background-color:silver;font-size:20px">Role on Project</th><td style="font-size:20px">&&role&&</td></tr>
                                    <tr><th style="background-color:silver;font-size:20px">Contract</th><td style="font-size:20px">&&contract&&</td></tr>
                                    <tr><th style="background-color:WhiteSmoke;font-size:16px">Requested By</th><td style="font-size:16px">&&requestor&&</td></tr>
                                    <tr><th style="background-color:WhiteSmoke;font-size:16px">Requested Timestamp</th><td style="font-size:16px">&&requested&&</td></tr>
                                    <tr><th style="background-color:WhiteSmoke;font-size:16px">Functional Mgr (on CC)</th><td style="font-size:16px">&&functionalMgr&&</td></tr>
                                </table>
                            </td></tr>
                            </table>';
    private static $pesEmailPatterns = array(
        '/&&name&&/',
        '/&&email&&/',
        '/&&notesid&&/',
        '/&&country&&/',
        '/&&lob&&/',
        '/&&role&&/',
        '/&&contract&&/',
        '/&&requestor&&/',
        '/&&requested&&/',
        '/&&functionalMgr&&/',
    );


    private static  $lobValue = array('GTS','GBS','IMI','Cloud','Security','Other');


    const PES_STATUS_CLEARED   = 'Cleared';
    const PES_STATUS_DECLINED  = 'Declined';
    const PES_STATUS_EXCEPTION = 'Exception';
    const PES_STATUS_FAILED    = 'Failed';
    const PES_STATUS_INITIATED = 'Initiated';
    const PES_STATUS_REMOVED   = 'Removed';


    function displayBoardingForm($mode){
        $loader = new Loader();
        $workstreamTable = new staticDataWorkstreamTable(allTables::$STATIC_WORKSTREAMS);
        //$allManagers = array('bob Mgr'=>'bob@email.com','cheryl mgr'=>'cheryl@email.com','cheryl two'=>'cheryl2@email.com');
        $allEmployeeTypes = $loader->load('EMPLOYEE_TYPE',allTables::$PERSON);

        $isFM = personTable::isManager($_SESSION['ssoEmail']);
        $fmPredicate = $isFM ? " UPPER(EMAIL_ADDRESS)='" . db2_escape_string(trim(strtoupper($_SESSION['ssoEmail']))) . "'  AND UPPER(LEFT(FM_MANAGER_FLAG,1))='Y'  " : " UPPER(LEFT(FM_MANAGER_FLAG,1))='Y' "; // FM Can only board people to themselves.
        $fmPredicate = $mode==FormClass::$modeEDIT ? "( " . $fmPredicate . " ) OR ( CNUM='" . db2_escape_string($this->FM_CNUM) . "' ) " : $fmPredicate;
        $allManagers =  $loader->loadIndexed('NOTES_ID','CNUM',allTables::$PERSON, $fmPredicate);
        $allManagers = empty($allManagers)? array('VBAC00001'=>'Dummy Fm') : $allManagers;
        $userDetails = $loader->loadIndexed('CNUM','EMAIL_ADDRESS',allTables::$PERSON, " EMAIL_ADDRESS='" . db2_escape_string($GLOBALS['ltcuser']['mail']) . "' ");
        $userCnum = isset($userDetails[$GLOBALS['ltcuser']['mail']]) ? $userDetails[$GLOBALS['ltcuser']['mail']] : false;
        //$allWorkStream = array('Work Stream 1'=>'ws001','Work Stream 2'=>'ws002','Work Stream 3'=>'ws003','Work Stream 4'=>'ws004');
        $allWorkstream = $workstreamTable->getallWorkstream();
        JavaScript::buildSelectArray($allWorkstream, 'workStream');
        ?>
        <form id='boardingForm'  class="form-horizontal" onsubmit="return false;">
        <div class="panel panel-default">
  		<div class="panel-heading">
    	<h3 class="panel-title">Employee Details</h3>
  		</div>
  		<div class="panel-body">
			<div class="form-group">
        	<div class="col-sm-6">

        <?php
        $notEditable = $mode==FormClass::$modeEDIT ? ' disabled ' : null;
        $displayForEdit = $notEditable ? 'hidden' : null;
        $onlyEditable = $mode==FormClass::$modeEDIT ? 'text' : 'hidden'; // Some fields the user can edit - but not see/set the first time.
        $hideDivFromEdit = $mode==FormClass::$modeEDIT ? ' style="display: none;"  ' : null; //Some fields we don't show on the edit screen.
        ?>

        <input class="form-control" id="person_name" name="person_name" value="<?=$this->FIRST_NAME . " " . $this->LAST_NAME?>" required type="text" placeholder='Start typing name/serial/email'  <?=$notEditable?>>

        </div>
        <div class='col-sm-6'>
        <input class='form-control' id='person_serial' name='CNUM' value='<?=$this->CNUM?>' required type='text' disabled='disabled' placeholder='Serial Number' <?=$notEditable?> >
        </div>
        </div>

    	<div id='personDetails'  display='<?=$displayForEdit?>'>
        <div class='form-group' >
        <div class='col-sm-6'>
        <input class='form-control' id='person_notesid' name='NOTES_ID' value='<?=$this->NOTES_ID?>' required type='text' disabled='disabled' placeholder="Notesid" <?=$notEditable?>>
        </div>

        <div class='col-sm-6'>
        <input class='form-control' id='person_intranet' name='EMAIL_ADDRESS' value='<?=$this->EMAIL_ADDRESS?>' required type='text' disabled='disabled' placeholder="Intranet" <?=$notEditable?>>
        </div>
        </div>

        <div class='form-group' >
        <div class='col-sm-12' <?=$hideDivFromEdit?>>
        <input class='form-control' id='person_bio' name='person_bio' value='' required type='text' disabled='disabled' placeholder="Bio">
        <input id='person_uid' name='person_uid' value='' type='hidden'  required >


       	<input id='person_is_mgr' name='FM_MANAGER_FLAG' value='<?=$this->FM_MANAGER_FLAG?>'  type='hidden'  required >
		<input id='person_employee_type' name='EMPLOYEE_TYPE' value='<?=$this->EMPLOYEE_TYPE?>'  type='Hidden'  required >

        <input id='person_first_name' name='FIRST_NAME' value='<?=$this->FIRST_NAME?>'  type='hidden'  required <?=$notEditable?>>
        <input id='person_last_name' name='LAST_NAME' value='<?=$this->LAST_NAME?>'  type='hidden'  required <?=$notEditable?>>


        <input id='person_ibm_location' name='IBM_BASE_LOCATION' value='<?=$this->IBM_BASE_LOCATION?>'  type='hidden'  required >
        <input id='person_country' name='COUNTRY' value='<?=$this->COUNTRY?>'  type='hidden'  required >

        </div>
        </div>
        </div>

		<div class='form-group' >
        <div class='col-sm-6' <?=$hideDivFromEdit?>>
        	<select class='form-control select select2' id='person_contractor_id'
                  	          name='CONTRACTOR_ID_REQUIRED'
                  	          required='required'
                >
            	<option value='no' <?=strtoupper(substr($this->CONTRACTOR_ID_REQUIRED,0,1))=='N' ? ' selected ' : null;?>>No Contractor Id Required</option>
            	<option value='yes' <?=strtoupper(substr($this->CONTRACTOR_ID_REQUIRED,0,1))=='Y' ? ' selected ' : null;?>>Contractor Id is Required</option>
            	</select>
       </div>
       </div>

</div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Functional Manager Details</h3>
  </div>
  <div class="panel-body">
        <div class="form-group">
        <div class="col-sm-6">
        	<select class='form-control select select2' id='FM_CNUM'
                  	          name='FM_CNUM'
                  	          required='required'
                  	          placeholder='Select functional manager'
                >
                <option value=''>Select Functional Mgr</option>
                <?php
                foreach ($allManagers as $mgrCnum => $mgrNotesid){
                    echo"<option value='" . $mgrCnum . "' ";
                    echo $userCnum==$mgrCnum ? " selected " : null;
                    echo $mgrCnum==$this->FM_CNUM ? " selected " : null;
                    echo ">" . $mgrNotesid . "</option>";
                };
                ?>
            	</select>
        </div>
        </div>
</div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Role Details</h3>
  </div>
  <div class="panel-body">

    <div class='form-group' >
        <div class='col-sm-6'>
          <input class="form-control" id="open_seat" name="OPEN_SEAT_NUMBER" value="<?=$this->OPEN_SEAT_NUMBER?>" type="text" placeholder='Open Seat' data-toggle='tooltip' title='Open Seat'>

         </div>
        <div class='col-sm-6'>
             <div class="radio">
  				<label><input type="radio" name="CTB_RTB" required value='CTB' <?=substr($this->CTB_RTB,0,3)=='CTB'? 'checked' : null ?>>CTB</label>
  				<label><input type="radio" name="CTB_RTB" required value='RTB' <?=substr($this->CTB_RTB,0,3)=='RTB'? 'checked' : null ?>>RTB</label>
  				<label><input type="radio" name="CTB_RTB" required value='Other' <?=substr($this->CTB_RTB,0,5)=='Other'? 'checked' : null ?>>Other</label>
			</div>
        </div>
     </div>

    <div class='form-group' >
        <div class='col-sm-6'>
          <input class="form-control" id="open_seat" name="ROLE_ON_THE_ACCOUNT" value="<?=$this->ROLE_ON_THE_ACCOUNT?>" required type="text" placeholder='Role on account' >

         </div>
        <div class='col-sm-6'>
        	   	<select class='form-control select select2' id='lob'
                  	          name='LOB'
                  	          required
                  	          placeholder='Select Lob'
            	>
                <option value=''>Select Lob</option>
                <?php
                foreach (self::$lobValue as $lob) {
                    ?><option value='<?=$lob?>'  <?=trim($this->LOB)==trim($lob)? ' selected ' : null ?>   ><?=$lob?></option><?php
                }
                ?>
            </select>
		</div>
     </div>


    <div class='form-group' >
        <div class='col-sm-6'>
            <div class="radio">
  			<label><input type="radio" name="TT_BAU" required class='accountOrganisation' value='T&T' <?=substr($this->TT_BAU,0,3)=='T&T'? 'checked' : null ?>>T&T</label>
  			<label><input type="radio" name="TT_BAU" required class='accountOrganisation' value='BAU' <?=substr($this->TT_BAU,0,3)=='BAU'? 'checked' : null ?>>BAU</label>
			</div>
        </div>

        <?php
        if(substr($this->TT_BAU,0,3)=='T&T'){
            ?>
            <script>
            $(document).on('ready', function(){
            	$(document).click($('.accountOrganisation')[0]);
            });
            </script>
            <?php
        }
        ?>

        <div class='col-sm-6'>
        	<input id='currentWorkstream' value='<?=$this->WORK_STREAM?>' type='hidden'>
        	<select class='form-control select select2' id='work_stream'
                  	          name='WORK_STREAM'
                  	          disabled
                  	          placeholder='Select T&T/BAU First'
            >
                <option value=''>Select T&T/BAU First</option>
            </select>

		</div>
    </div>

    <div class='form-group' >
        <div class='col-sm-6'>
          <input class="form-control" id="start_date" name="START_DATE" value="<?=$this->START_DATE?>" required="required" type="text" placeholder='Start Date' data-toggle='tooltip' title='Start Date'>
           </div>

        <div class='col-sm-6'>
          <input class="form-control" id="end_date" name="PROJECTED_END_DATE" value="<?=$this->PROJECTED_END_DATE?>"  type="text" placeholder='End Date (if known)' data-toggle='tooltip' title='End Date'>

           </div>
         </div>
         </div>
         </div>


		<?php
	$allButtons = null;
	$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateBoarding',null,'Update','btn-primary glyphicon glyphicon-refresh') :  $this->formButton('submit','Submit','saveBoarding',null,'Submit','btn-primary glyphicon glyphicon-refresh');
	$pesButton    = $mode==FormClass::$modeEDIT ?  null :  $this->formButton('button','initiatePes','initiatePes','disabled','Initiate PES','btn-primary btnPesInitiate glyphicon glyphicon-refresh');
  	$allButtons[] = $submitButton;
  	$allButtons[] = $pesButton;
	$this->formBlueButtons($allButtons);
	$this->formHiddenInput('requestor',$GLOBALS['ltcuser']['mail'],'requestor');
	?>

	</form>
    <?php
    }


    function savingBoardingDetailsModal(){
        ?>
    	 <!-- Modal -->
		<div id="savingBoardingDetailsModal" class="modal fade" role="dialog">
  		<div class="modal-dialog">

            <!-- Modal content-->
    		<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal">&times;</button>
        		<h4 class="modal-title">Saving Boarding Details</h4>
      		</div>
      		<div class="modal-body" >
      			<div class="panel"></div>
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      		</div>
    		</div>
  		</div>
		</div>
        <?php
    }


    function amendPesStatusModal(){
        ?>
    	 <!-- Modal -->
		<div id="amendPesStatusModal" class="modal fade" role="dialog">
  		<div class="modal-dialog">

            <!-- Modal content-->
    		<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal">&times;</button>
        		<h4 class="modal-title">Amend PES Status</h4>
      	    </div>
      		<div class="modal-body" >
      		<form id='psmForm' class="form-horizontal"  method='post'>

      		<div class="panel panel-default">
  				<div class="panel-heading">
    				<h3 class="panel-title">Employee Details</h3>
  				</div>
  			<div class="panel-body">
      			<div class='row'>
      				<div class='form-group' >
        				<div class='col-sm-6'>
          					<input class="form-control" id="psm_notesid" name="psm_notesid" value="" type="text" disabled>
        			    </div>
        				<div class='col-sm-6'>
        				    <input class="form-control" id="psm_cnum" name="psm_cnum" value="" type="text" disabled>
        			    </div>
     				</div>
     			</div>
     		</div>
      		</div>

      		<div class="panel panel-default">
  				<div class="panel-heading">
    				<h3 class="panel-title">PES Status</h3>
  				</div>
  			<div class="panel-body">
     			<div class='row'>
      				<div class='form-group required' >
        				<label for='psm_status' class='col-md-2 control-label '>Status</label>
        				<div class='col-md-4'>
              				<select class='form-control select' id='psm_status'
                  	          	name='psm_status'
                  	          	required='required'
                  	          	data-tags="true" data-placeholder="Status" data-allow-clear="true"
                  	           >
            				<option value=''>Status</option>
            				<option value='<?=personRecord::PES_STATUS_CLEARED;?>'><?=personRecord::PES_STATUS_CLEARED?></option>
            				<option value='<?=personRecord::PES_STATUS_DECLINED;?>'><?=personRecord::PES_STATUS_DECLINED?></option>
            				<option value='<?=personRecord::PES_STATUS_EXCEPTION;?>'><?=personRecord::PES_STATUS_EXCEPTION?></option>
            				<option value='<?=personRecord::PES_STATUS_FAILED;?>'><?=personRecord::PES_STATUS_FAILED?></option>
            				<option value='<?=personRecord::PES_STATUS_INITIATED;?>'><?=personRecord::PES_STATUS_INITIATED?></option>
            				<option value='<?=personRecord::PES_STATUS_REMOVED;?>'><?=personRecord::PES_STATUS_REMOVED?></option>
               				</select>
            		 </div>
            	</div>
            	</div>

     			<div class='row'>
      				<div class='form-group required' >
        				<label for='psm_detail' class='col-md-2 control-label '>Detail</label>
        					<div class='col-md-9'>
  								<input class="form-control" id="psm_detail" name="psm_detail" value="" type="text"  >
            		 		</div>
            		</div>
     				</div>
     			</div>
      		</div>
      		<div class="modal-footer">
      		<?php
      		$allButtons = null;
      		$submitButton = $this->formButton('submit','Submit','savePesStatus',null,'Submit','btn-primary');
      		$allButtons[] = $submitButton;
      		$this->formBlueButtons($allButtons);
      		?>

       		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
      		</div>
      		</form>
    		</div>
  		</div>
		</div>
		</div>
        <?php


    }

    function editPersonModal(){
        ?>
    	 <!-- Modal -->
		<div id="editPersonModal" class="modal fade" role="dialog">
  			<div class="modal-dialog">
    			<div class="modal-content">
    			<div class="modal-header">
	   				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				<h4 class="modal-title">Edit Person Record</h4>
        		</div>
   		    	<div class="modal-body" >
   		    	</div>
   		    	<div class='modal-footer'>
   		    	</div>
      			</div>
    		</div>
  		</div>
		<?php
    }


    function editPersonModalBody(){
        ?>
        <div class='container-fluid'>
        <?php
        $this->displayBoardingForm(FormClass::$modeEDIT);
        ?>
        </div>
        <?php
    }

    function confirmChangeFmFlagModal(){
        ?>
    	 <!-- Modal -->
		<div id="confirmChangeFmFlagModal" class="modal fade" role="dialog">
  			<div class="modal-dialog">
    			<div class="modal-content">
    			<div class="modal-header">
	   				<button type="button" class="close" data-dismiss="modal">&times;</button>
      				<h4 class="modal-title">Confirm change of FM Flag</h4>
        		</div>

   		    	<form id='confirmFmFlagChangeForm' class="form-horizontal"  method='post' >
   		    	<div class="modal-body" >
   		    	</div>
   		    	<div class='modal-footer'>
   		    	<?php
   		    	$allButtons = null;
      			$submitButton = $this->formButton('submit','Submit','confirmFmStatusChange',null,'Submit','btn-primary');
      			$allButtons[] = $submitButton;
      			$this->formBlueButtons($allButtons);
      			?>
       			<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
   		    	</div>
   		    	</form>
      			</div>
    		</div>
  		</div>
		<?php
    }





    function sendPesRequest(){
        $loader = new Loader();
        $fmEmail = $loader->loadIndexed('EMAIL_ADDRESS','CNUM',allTables::$PERSON," CNUM='" . db2_escape_string(trim($this->FM_CNUM)) . "' ");
        $now = new \DateTime();
        $replacements = array($this->FIRST_NAME . " " . $this->LAST_NAME,
                              $this->EMAIL_ADDRESS,
                              $this->NOTES_ID,
                              $this->COUNTRY,
                              $this->LOB,
                              $this->ROLE_ON_THE_ACCOUNT,
                              'Ventus',
                              $_SESSION['ssoEmail'],
                              $now->format('Y-m-d H:i:s'),
                              $fmEmail[trim($this->FM_CNUM)]);
        $message = preg_replace(self::$pesEmailPatterns, $replacements, self::$pesEmailBody);

        \itdq\BlueMail::send_mail(array(self::$pesTaskId), 'vBAC PES Request - ' . $this->CNUM ." (" . $this->FIRST_NAME . " " . $this->LAST_NAME . ")", $message, 'vbacNoReply@uk.ibm.com');

    }

    function setPesRequested(){
        $table = new personTable(allTables::$PERSON);
        $success = $table->setPesRequested($this->CNUM, $_SESSION['ssoEmail']);
        return $success;
    }
}