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

        <form id='displayBpDetails'  class="form-horizontal">
        <div class="form-group" id="displayBpDetails">

		<div class="form-group">
        <div class="col-sm-5">
        <input class="form-control" id="NAME" name="NAME" value="<?=$this->NAME?>" required="required" type="text" placeholder='Start typing a name to perform a lookup' >
        </div>
        </div>

        <div id='personDetails' hidden >

        <div class='form-group' >
        <label class='col-sm-1 ' for='person_notesid'>Notesid</label>
        <div class='col-sm-3'>
        <input class='form-control' id='person_notesid' name='person_notesid' value='' required='required' type='text' disabled='disabled' >
        </div>

        <label class='col-sm-1 ' for='person_intranet'>eMail</label>
        <div class='col-sm-3'>
        <input class='form-control' id='person_intranet' name='person_intranet' value='' required='required' type='text' disabled='disabled'>
        </div>
        </div>

        <div class='form-group' >
        <label class='col-sm-1 ' for='person_bio'>Bio</label>
        <div class='col-sm-7'>
        	<input class='form-control' id='person_bio' name='person_bio' value='' required='required' type='text' disabled='disabled' placeholder="Enter email">
        <input id='person_uid' name='person_uid' value='' required='required' type='hidden'  >
        </div>
        </div>

        </div>


		</form>
    	</div>
        <?php

    }

    function displayForm($mode){

    }

}