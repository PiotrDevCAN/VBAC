<?php
namespace vbac;

use itdq\DbRecord;


/**
 *
 * @author gb001399
 *
 */
class personRecord extends DbRecord
{

    protected $NAME;
    protected $CNUM;
    protected $FUNCTIONAL_MGR_FLAG;
    protected $FUNCTIONAL_MGR_CNUM;

    protected $person_NOTES_ID;
    protected $person_INTRANET_ID;
    protected $person_PHONE;
    protected $person_UID;


    function displayBpDetails($mode){
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
        <input class="form-control" id="NAME" name="NAME" value="<?=$this->NAME?>" required="required" type="text" placeholder='Start typing name' >
        </div>
        <div class='col-sm-6'>
        <input class='form-control' id='person_serial' name='person_serial' value='' required='required' type='text' placeholder='SerialNum & Country Code (9 digits)' >
        </div>
        </div>

        <div id='personDetails' >
        <div class='form-group' >
        <div class='col-sm-6'>
        <input class='form-control' id='person_notesid' name='person_notesid' value='' required='required' type='text' disabled='disabled' >
        </div>

        <div class='col-sm-6'>
        <input class='form-control' id='person_intranet' name='person_intranet' value='' required='required' type='text' disabled='disabled'>
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
        	<select class='form-control select' id='person_contractor_id'
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

        <input class="form-control" id="NAME" name="NAME" value="<?=$this->NAME?>" required="required" type="text" placeholder='Start typing a name to perform a lookup' >
        </div>

        </div>
        <div id='functionalManagerDetails' >
        <div class='form-group' >
        <div class='col-sm-6'>
        <input class='form-control' id='person_fm_notesid' name='person_fm_notesid' value='' required='required' type='text' disabled='disabled' placeholder='Notes ID' >
        </div>
        <div class='col-sm-6'>
        <input class='form-control' id='person_fm_intranet' name='person_fm_intranet' value='' required='required' type='text' disabled='disabled' placeholder='Email'>
        </div>
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
          <input class="form-control" id="role_on_the_account" name="role_on_the_account" value="<?=$this->NAME?>" required="required" type="text" placeholder='Role on the account' >

         </div>
        <div class='col-sm-6'>
          <input class="form-control" id="account_organization" name="account_organization" value="<?=$this->NAME?>" required="required" type="text" placeholder='Account Organization' >

           </div>
         </div>

    <div class='form-group' >
        <div class='col-sm-6'>
          <input class="form-control" id="tt_bau" name="tt_bau" value="<?=$this->NAME?>" required="required" type="text" placeholder='T&T/BAU' >

         </div>

        <div class='col-sm-6'>
          <input class="form-control" id="bau_work_stream" name="bau_work_stream" value="<?=$this->NAME?>" required="required" type="text" placeholder='BAU - Work Stream' >
        </div>

    </div>
    <div class='form-group' >
        <div class='col-sm-6'>
          <input class="form-control" id="start_date" name=""start_date"" value="<?=$this->NAME?>" required="required" type="text" placeholder='Start Date' >
           </div>

        <div class='col-sm-6'>
          <input class="form-control" id=""end_date"" name="end_date" value="<?=$this->NAME?>" required="required" type="text" placeholder='End Date (if known)' >

           </div>
         </div>
         </div>
         </div>
	</form>
	</div>
	<div class='col-sm-2'></div>





    <?php
    }


    function displayForm($mode){

    }

}