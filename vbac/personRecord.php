<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;


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

    protected $FM_CNUM;
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
    protected $WORK_STREAM;

    protected $person_notesid;
    protected $person_bio;


    function displayForm($mode){
        $allManagers = array('bob Mgr'=>'bob@email.com','cheryl mgr'=>'cheryl@email.com','cheryl two'=>'cheryl2@email.com');
        $allWorkStream = array('Work Stream 1'=>'ws001','Work Stream 2'=>'ws002','Work Stream 3'=>'ws003','Work Stream 4'=>'ws004');

        ?>

		<div class='col-sm-2'></div>

		<div class='col-sm-8'>

        <form id='displayBpDetails'  class="form-horizontal">

        <div class="panel panel-default">
  		<div class="panel-heading">
    	<h3 class="panel-title">Employee Details</h3>
  		</div>
  		<div class="panel-body">
		<div class="form-group">
        <div class="col-sm-6">
        <input class="form-control" id="person_name" name="person_name" value="" required="required" type="text" placeholder='Start typing name' >
        </div>
        <div class='col-sm-6'>
        <input class='form-control' id='person_serial' name='person_serial' value='<?=$this->CNUM?>' required='required' type='text' placeholder='or SerialNum & Country Code (9 digits)' >
        </div>
        </div>

    <div id='personDetails'  hidden>
        <div class='form-group' >
        <div class='col-sm-6'>
        <input class='form-control' id='person_notesid' name='person_NOTES_ID' value='' required='required' type='text' disabled='disabled' >
        </div>

        <div class='col-sm-6'>
        <input class='form-control' id='person_intranet' name='EMAIL_ADDRESS' value='' required='required' type='text' disabled='disabled'>
        </div>
        </div>

        <div class='form-group' >
        <div class='col-sm-12'>
        <input class='form-control' id='person_bio' name='person_bio' value='' required='required' type='text' disabled='disabled' placeholder="Enter email">
        <input id='person_uid' name='person_uid' value='' required='required' type='hidden'  >
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
        	<select class='form-control select select2' id='person_fm_mgr'
                  	          name='person_contractor_id'
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
<!--         <div id='functionalManagerDetails' > -->
<!--         <div class='form-group' > -->
<!--         <div class='col-sm-6'> -->
<!--         <input class='form-control' id='person_fm_notesid' name='person_fm_notesid' value='' required='required' type='text' disabled='disabled' placeholder='Notes ID' > -->
<!--         </div> -->
<!--         <div class='col-sm-6'> -->
<!--         <input class='form-control' id='person_fm_intranet' name='person_fm_intranet' value='' required='required' type='text' disabled='disabled' placeholder='Email'> -->
<!--         </div> -->
<!--         </div> -->
<!-- 		</div> -->
</div>
</div>

<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Role Details</h3>
  </div>
  <div class="panel-body">

    <div class='form-group' >
        <div class='col-sm-6'>
          <input class="form-control" id="open_seat" name="OPEN_SEAT_NUMBER" value="<?=$this->OPEN_SEAT_NUMBER?>" required="required" type="text" placeholder='Open Seat' >

         </div>
        <div class='col-sm-6'>
              <select class='form-control select select2' id='work_stream'
                  	          name='work_stream'
                  	          required='required'
                  	          placeholder='Select functional manager'
                >
                <option value=''>Select Work Stream</option>
                <?php
                foreach ($allWorkStream as $wsName => $wsId){
                    echo"<option value='" . $wsId . "'>" . $wsName . "</option>";
                };
                ?>
            	</select>
        </div>
     </div>

    <div class='form-group' >
        <div class='col-sm-6'>
            <div class="radio">
  			<label><input type="radio" name="TT_BAU">T&T</label>
  			<label><input type="radio" name="TT_BAU">BAU</label>
			</div>
        </div>

        <div class='col-sm-6'>
            <div class="radio">
  			<label><input type="radio" name="ACCOUNT_ORGANISATION">CTB</label>
  			<label><input type="radio" name="ACCOUNT_ORGANISATION">RTB</label>
  			<label><input type="radio" name="ACCOUNT_ORGANISATION">Other</label>
			</div>
			</div>
    </div>

    <div class='form-group' >
        <div class='col-sm-6'>
          <input class="form-control" id="start_date" name=""start_date"" value="<?=$this->START_DATE?>" required="required" type="text" placeholder='Start Date' >
           </div>

        <div class='col-sm-6'>
          <input class="form-control" id="end_date" name="end_date" value="<?=$this->PROJECTED_END_DATE?>" required="required" type="text" placeholder='End Date (if known)' >

           </div>
         </div>
         </div>
         </div>
	</form>

		<?php
	$allButtons = null;
	$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateRfs',null,'Update') :  $this->formButton('submit','Submit','saveRfs',null,'Submit');
  	$resetButton  = $this->formButton('reset','Reset','resetRfs',null,'Reset','btn-warning');
	$allButtons[] = $submitButton;
	$allButtons[] = $resetButton;
	$this->formBlueButtons($allButtons);
	?>


	</div>
    <?php
    }

}