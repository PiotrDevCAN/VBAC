<?php
namespace vbac;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;
use itdq\AuditTable;
use itdq\FormClass;


class AgileTribeRecord extends DbRecord {

    protected $TRIBE_NUMBER;
    protected $TRIBE_NAME;
    protected $TRIBE_LEADER;


    function displayForm($mode=FormClass::modeDefine){
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';
        $nextAvailableTribeNumber = AgileTribeTable::nextAvailableTribeNumber();
        ?>
        <form id='tribeForm' class="form-horizontal" method='post'>
         <div class="form-group required" >
            <label for='TRIBE_NUMBER' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Tribe Number'>Tribe Number</label>
        	<div class='col-md-4'>
				<input id='TRIBE_NUMBER' name='TRIBE_NUMBER' class='form-control' type='number'  <?=$notEditable;?> value='<?=!empty($this->TRIBE_NUMBER) ? $this->TRIBE_NUMBER :$nextAvailableTribeNumber ; ?>' />
            </div>
        </div>
        <div class="form-group required " >
            <label for='TRIBE_NAME' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Full Name'>Tribe Name</label>
        	<div class='col-md-4'>
				<input id='TRIBE_NAME' name='TRIBE_NAME' class='form-control' value='<?=!empty($this->TRIBE_NAME) ? $this->TRIBE_NAME :null ; ?>' />
            </div>
        </div>
        <div class="form-group " >
            <label for='TRIBE_LEADER' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Tribe Leader'>Tribe Leader</label>
        	<div class='col-md-4'>
				<input id='TRIBE_LEADER' name='TRIBE_LEADER' class='form-control typeaheadNotesId' value='<?=!empty($this->TRIBE_LEADER) ? $this->TRIBE_LEADER :null ; ?>'/>
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