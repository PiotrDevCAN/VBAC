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
    protected $PES_DATE_RESPONDED;
    protected $PES_DETAILS;
    protected $PES_STATUS;

    protected $REVALIDATION_DATE_FIELD;
    protected $REVALIDATION_STATUS;

    protected $CBN_DATE_FIELD;
    protected $CBN_STATUS;

    protected $WORK_STREAM;


    protected $person_notesid;
    protected $person_bio;


   // private static $pesTaskId = 'lbgvetpr@uk.ibm.com';
    private static $pesTaskId = 'rob.daniel@uk.ibm.com';
    private static $pesEmailBody = '<table width="100%" border="0" cellspacing="0" cellpadding="0">
                             <tr><td align="center">
                                <table width="50%">
                                    <tr><td colspan="2" style="font-size:16px;padding-bottom:10px"">Please initiate PES check for the following individual:</td></tr>
                                    <tr><th style="background-color:silver;font-size:20px">Name</th><td style="font-size:20px">&&name&&</td></tr>
                                    <tr><th style="background-color:silver;font-size:20px">Email Address</th><td style="font-size:20px">&&email&&</td></tr>
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
        '/&&country&&/',
        '/&&lob&&/',
        '/&&role&&/',
        '/&&contract&&/',
        '/&&requestor&&/',
        '/&&requested&&/',
        '/&&functionalMgr&&/',
    );


    function displayBoardingForm($mode){
        $workstreamTable = new staticDataWorkstreamTable(allTables::$STATIC_WORKSTREAMS);
        $allManagers = array('bob Mgr'=>'bob@email.com','cheryl mgr'=>'cheryl@email.com','cheryl two'=>'cheryl2@email.com');
        //$allWorkStream = array('Work Stream 1'=>'ws001','Work Stream 2'=>'ws002','Work Stream 3'=>'ws003','Work Stream 4'=>'ws004');
        $allWorkstream = $workstreamTable->getallWorkstream();
        JavaScript::buildSelectArray($allWorkstream, 'workStream');
        ?>
        <form id='boardingForm'  class="form-horizontal" onsubmit="return false;">
		<div class='col-sm-2'></div>

		<div class='col-sm-8'>



        <div class="panel panel-default">
  		<div class="panel-heading">
    	<h3 class="panel-title">Employee Details</h3>
  		</div>
  		<div class="panel-body">
		<div class="form-group">
        <div class="col-sm-6">

        <input class="form-control" id="person_name" name="person_name" value="" required type="text" placeholder='Start typing name' >

        </div>
        <div class='col-sm-6'>
        <input class='form-control' id='person_serial' name='CNUM' value='<?=$this->CNUM?>' required type='text' placeholder='or SerialNum & Country Code (9 digits)' >
        </div>
        </div>

    <div id='personDetails'  hidden>
        <div class='form-group' >
        <div class='col-sm-6'>
        <input class='form-control' id='person_notesid' name='person_NOTES_ID' value='' required type='text' disabled='disabled' >
        </div>

        <div class='col-sm-6'>
        <input class='form-control' id='person_intranet' name='EMAIL_ADDRESS' value='' required type='text' disabled='disabled'>
        </div>
        </div>

        <div class='form-group' >
        <div class='col-sm-12'>
        <input class='form-control' id='person_bio' name='person_bio' value='' required type='text' disabled='disabled' placeholder="Enter email">
        <input id='person_uid' name='person_uid' value='' type='hidden'  required >
        <input id='person_is_mgr' name='FM_MANAGER_FLAG' value=''  type='hidden'  required >
        <input id='person_first_name' name='FIRST_NAME' value=''  type='hidden'  required >
        <input id='person_last_name' name='LAST_NAME' value=''  type='hidden'  required >
        <input id='person_phone' name='PHONE_NUMBER' value=''  type='hidden'  required >
        </div>
        </div>

		<div class='form-group' >
        <div class='col-sm-6'>
        	<select class='form-control select select2 input-lg' id='person_contractor_id'
                  	          name='person_contractor_id'
                  	          required='required'
                >
            	<option value='no'>No Contractor Id Required</option>
            	<option value='yes'>Contractor Id is Required</option>
            	</select>
       </div>
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
        	<select class='form-control select select2' id='FM_EMAIL'
                  	          name='FM_EMAIL'
                  	          required='required'
                  	          placeholder='Select functional manager'
                >
                <option value=''>Select Functional Mgr</option>
                <?php
                foreach ($allManagers as $mgrName => $mgrEmail){
                    echo"<option value='" . $mgrEmail . "'>" . $mgrName . "</option>";
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
          <input class="form-control" id="open_seat" name="OPEN_SEAT_NUMBER" value="<?=$this->OPEN_SEAT_NUMBER?>" required type="text" placeholder='Open Seat' >

         </div>
        <div class='col-sm-6'>
            <div class="radio">
  				<label><input type="radio" name="CTB_RTB" required value='CTB'>CTB</label>
  				<label><input type="radio" name="CTB_RTB" required value='RTB'>RTB</label>
  				<label><input type="radio" name="CTB_RTB" required value='Other'>Other</label>
			</div>


        </div>
     </div>

    <div class='form-group' >
        <div class='col-sm-6'>
            <div class="radio">
  			<label><input type="radio" name="TT_BAU" required class='accountOrganisation' value='T&T'>T&T</label>
  			<label><input type="radio" name="TT_BAU" required class='accountOrganisation' value='BAU'>BAU</label>
			</div>
        </div>

        <div class='col-sm-6'>
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
          <input class="form-control" id="start_date" name="START_DATE" value="<?=$this->START_DATE?>" required="required" type="text" placeholder='Start Date' >
           </div>

        <div class='col-sm-6'>
          <input class="form-control" id="end_date" name="PROJECTED_END_DATE" value="<?=$this->PROJECTED_END_DATE?>"  type="text" placeholder='End Date (if known)' >

           </div>
         </div>
         </div>
         </div>


		<?php
	$allButtons = null;
	$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateBoarding',null,'Update') :  $this->formButton('submit','Submit','saveBoarding',null,'Submit');
  	$allButtons[] = $submitButton;
	$this->formBlueButtons($allButtons);
	?>
	</div>
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
      		<div class="modal-body" ><p>unchanged</p>
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      		</div>
    		</div>
  		</div>
		</div>
        <?php
    }


    function sendPesRequest(){
        $now = new \DateTime();
        $replacements = array('Rob Daniel','robdaniel@uk.ibm.com','UK','GTS','Cognitive Delivery','Ventus','fred.smith@uk.ibm.com',$now->format('Y-m-d H:i:s'),'mickeyMouse@ibm.com');
        $message = preg_replace(self::$pesEmailPatterns, $replacements, self::$pesEmailBody);

        \itdq\BlueMail::send_mail(array(self::$pesTaskId,'antstark@uk.ibm.com'), 'PES Request - Fred Smith', $message, 'vbacNoReply@uk.ibm.com');
    }


}