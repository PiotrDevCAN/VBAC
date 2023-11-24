<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;


class AgileTribeRecord extends DbRecord {

    protected $TRIBE_NUMBER;
    protected $TRIBE_NAME;
    protected $TRIBE_LEADER;
    protected $ORGANISATION;
    protected $ITERATION_MGR;

    function displayForm($mode,$version=null){
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';
        $nextAvailableTribeNumber = AgileTribeTable::nextAvailableTribeNumber($version);
        $managedChecked = $this->ORGANISATION=='Managed Services' || empty($this->ORGANISATION) ? " checked='checked' " : null;
        $projectChecked = empty($managedChecked) ?  " checked='checked' " : null;
         ?>
        <form id='tribeForm' class="form-horizontal" method='post'>
         <div class="form-group required" >
            <label for='TRIBE_NUMBER' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Tribe Number'>Tribe Number</label>
        	<div class='col-md-4'>
				<input id='TRIBE_NUMBER' name='TRIBE_NUMBER' class='form-control' type='number'  <?=$notEditable;?> value='<?=!empty($this->TRIBE_NUMBER) ? $this->TRIBE_NUMBER :$nextAvailableTribeNumber ; ?>' placeholder="Tribe Number"/>
            </div>
        </div>
        <div class="form-group " >
            <label for='ORGANISATION' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Organisation'>Organisation</label>
        	<div class='col-md-4'>
        	 <div class="form-check">
             <input class="form-check-input" name='ORGANISATION'  type="radio" id="radioTribeOrganisationManaged" value="Managed Services" <?=$managedChecked?>>
             <label class="form-check-label " for="radio" id="radioTribeOrganisation">Managed Services</label>
             </div>

             <div class="form-check">
             <input class="form-check-input" name='ORGANISATION'  type="radio" id="radioTribeOrganisationProject" value="Project Services" <?=$projectChecked?>>
             <label class="form-check-label " for="radio" id="radioTribeOrganisationProject">Project Services</label>
             </div>
            </div>
        </div>

        <div class="form-group required " >
            <label for='TRIBE_NAME' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Full Name'>Tribe Name</label>
        	<div class='col-md-4'>
				<input id='TRIBE_NAME' name='TRIBE_NAME' class='form-control' value='<?=!empty($this->TRIBE_NAME) ? $this->TRIBE_NAME :null ; ?>' placeholder="Tribe Name"/>
            </div>
        </div>
        <div class="form-group " >
            <label for='TRIBE_LEADER' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Tribe Leader'>Tribe Leader</label>
        	<div class='col-md-4'>
				<input id='TRIBE_LEADER' name='TRIBE_LEADER' class='form-control typeaheadEmailId' value='<?=!empty($this->TRIBE_LEADER) ? $this->TRIBE_LEADER :null ; ?>' placeholder="Tribe Leader"/>
            </div>
        </div>
        
        <div class="form-group required " >
            <label for='ITERATION_MGR' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Iteration Manager'>Iteration Manager</label>
        	<div class='col-md-4'>
				<input id='ITERATION_MGR' name='ITERATION_MGR' class='form-control typeaheadEmailId' value='<?=!empty($this->ITERATION_MGR) ? $this->ITERATION_MGR :null ; ?>' placeholder="Iteration Manager"/>
            </div>
        </div>
        

   		<div class='form-group'>
   		<div class='col-sm-offset-2 -col-md-4'>
        <?php
        $this->formHiddenInput('mode',$mode,'mode');
        $allButtons = array();
   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateTribe',null,'Update') :  $this->formButton('submit','Submit','saveTribe',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','resetTribe  Form',null,'Reset','btn-warning');
   		$allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
   		$this->formBlueButtons($allButtons);
  		?>
  		</div>
  		</div>
	</form>
    <?php

    }

}