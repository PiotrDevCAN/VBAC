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
    protected $PHONE_NUMBER;
    protected $EMAIL_ADDRESS;

    protected $LBG_EMAIL;
    protected $EMPLOYEE_TYPE;
    protected $WORK_STREAM;

    protected $FM_EMAIL;
    protected $FM_MANAGER_FLAG;

    protected $CTB_RTB;
    protected $TT_BAU;
    protected $LOB;

    protected $ROLE_ON_THE_ACCOUNT;
    protected $ROLE_TECHNOLOGY;

    protected $START_DATE;
    protected $PROJECTED_END_DATE;

    protected $THIRD_PARTY_VDI_ACCESS;
    protected $EXTERNAL_COMPANY;
    protected $COUNTRY;
    protected $CT_ID;
    protected $CT_REQUEST;
    protected $CYBERARK;
    protected $FM_APPROVAL;
    protected $FULL_VPN;
    protected $IBM_BASE_LOCATION;
    protected $LBG_LOCATION;
    protected $LAPTOP_ASSET;
    protected $MPW_ACCOUNT_TYPE;
    protected $MPW_DOMAIN;
    protected $MPW_ID;
    protected $OCS;
    protected $OFFBOARDED_DATE;

    protected $PES_DATE_REQUESTED;
    protected $PES_DATE_RESPONDED;
    protected $PES_DETAILS;
    protected $PES_STATUS;

    protected $RAS_ACCESS;

    protected $REVALIDATION_DATE_FIELD;
    protected $REVALIDATION_STATUS;

    protected $SDID;
    protected $VDI;
    protected $VPN_LITE;


    protected $person_notesid;
    protected $person_bio;

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
        <input id='person_is_mgr' name='person_is_mgr' value=''  type='hidden'  required >
        <input id='person_first_name' name='person_first_name' value=''  type='hidden'  required >
        <input id='person_last_name' name='person_last_name' value=''  type='hidden'  required >
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
                  	          name='person_fm_mgr'
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


    function errorSavingBoardingDetailsModal(){
        ?>
    	 <!-- Modal -->
		<div id="errorSavingBoardingDetailsModal" class="modal fade" role="dialog">
  		<div class="modal-dialog">

            <!-- Modal content-->
    		<div class="modal-content">
      		<div class="modal-header">
        		<button type="button" class="close" data-dismiss="modal">&times;</button>
        		<h4 class="modal-title">Error Saving Boarding Details</h4>
      		</div>
      		<div class="modal-body" >
      		</div>
      		<div class="modal-footer">
        		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      		</div>
    		</div>
  		</div>
		</div>
        <?php
    }

}