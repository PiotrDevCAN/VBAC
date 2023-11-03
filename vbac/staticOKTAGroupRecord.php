<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;

/**
 *
 * @author gb001399
 *
 */
class staticOKTAGroupRecord extends DbRecord
{

    public $GROUP;
    public $EMAIL_ADDRESS;

	function displayForm($mode){
        $allButtons = array();
		$allGroups = $GLOBALS['site']['allGroups'];
		$groupPrefix = 'the vBAC tool - production-';
        ?>
        <form id='oktaEntryForm' class="form-horizontal"  method='post'>
        <div class="form-group ">
			<div class='required'>
        		<label for="GROUP" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Okta Group">Okta Group Name</label>
        		<div class="col-md-6">
        		<select class='form-control select' id='GROUP' name='GROUP'
					data-placeholder="Select Okta Group" data-allow-clear="true"
					>
					<?php
					foreach($allGroups as $group) {
						$selected = null;
						$value = $group;
						$group = str_replace($groupPrefix, '', $group);
					?>
						<option value="<?=$value?>" <?=$selected?>><?=$group?></option>
					<?php
					}
				?>
				</select>
				</div>
        	</div>
        </div>
        <div class="form-group ">
			<div class='required'>
        		<label for="EMAIL_ADDRESS" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Email Address">Email Address</label>
        		<div class="col-md-6">
        		<input class="form-control typeahead tt-input" id="EMAIL_ADDRESS" name="EMAIL_ADDRESS" value="<?=$this->EMAIL_ADDRESS?>" placeholder="Enter Email Address" required="required" type="text" >
        		<input id="originalEMAIL_ADDRESS" name="originalEMAIL_ADDRESS"
					value="<?=$this->EMAIL_ADDRESS;?>" type="hidden">
				<p id="IBMNotAllowed" style="display:none; color: CRIMSON">IBM / Ocean email address is no longer allowed.</p>
				</div>
        	</div>
        </div>
        <?php
   		$this->formHiddenInput('mode',$mode,'mode');

   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateOktaEntry',null,'Update') :  $this->formButton('submit','Submit','saveOktaEntry',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetOktaEntry',null,'Reset','btn-warning');
   		$allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
   		?>
   		<div class='form-group'>
   		<div class='col-md-2'></div>
   		<div class='col-md-4'>
   		<?php
   		$this->formBlueButtons($allButtons);
   		?>
   		</div>
   		</div>
	</form>
    <?php

    }
}