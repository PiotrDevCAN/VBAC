<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;

class personSquadRecord extends DbRecord {

    protected $ID;
    protected $CNUM;
    protected $WORKER_ID;
    protected $SQUAD_NUMBER;
    protected $TYPE;

    const PRIMARY = '1';
    const SECONDARY = '2';
    
    static public $allTypes = array(
      self::PRIMARY => 'Primary', 
      self::SECONDARY => 'Secondary' 
    );
    
    function displayForm($mode){
        ?>
        <form id='squadForm' class="form-horizontal" method='post'>
        <div class="form-group required" >
          <label for="EMAIL_ADDRESS" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Email Address">Email Address</label>
          <div class="col-md-4">
            <input class="form-control typeahead" id="person_name" name="person_name" 
              value="" placeholder='Start typing name/serial/email' type="text" 
              required="required" >
          </div>
			  </div>
        <div class="form-group required" >
          <label for=SQUAD_NUMBER class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Tribe Name'>Tribe Name</label>
        	<div class='col-md-4'>
            <select class='form-control select select2' 
                id='TRIBE_NUMBER'
                name='TRIBE_NUMBER'
                required='required'
                data-placeholder='Agile Tribe' >
                <option value='0'>Select Agile Tribe</option>
            </select>
            <input type='hidden' id='originalTRIBE_NUMBER' NAME='originalTRIBE_NUMBER' value='' />
			    </div>
        </div>
        <div class="form-group required" >
          <label for=SQUAD_NUMBER class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Squad Name'>Squad Name</label>
        	<div class='col-md-4'>
            <select class='form-control select select2' 
                id='SQUAD_NUMBER'
                name='SQUAD_NUMBER'
                required='required'
                data-placeholder='Agile Tribe first'
                disabled >
                <option value='0'>Select Agile Squad</option>
            </select>
            <input type='hidden' id='originalSQUAD_NUMBER' NAME='originalSQUAD_NUMBER' value='<?=$this->SQUAD_NUMBER ?>' />
			    </div>
        </div>
        <div class="form-group required" >
          <label for=TYPE class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Assignment Type'>Assignment Type</label>
        	<div class='col-md-4'>
            <select class='form-control select select2' 
                id='TYPE'
                required='required'
                name='TYPE'>
                <option value=''>Select Assignment Type</option>
                <option value='1'>Primary</option>
                <option value='2'>Secondary</option>        
            </select>
			    </div>
        </div>
   		<div class='form-group'>
   		<div class='col-sm-offset-2 -col-md-4'>
        <input type='hidden' id='ID' name='ID' value='<?=$this->ID ?>' />
        <input type='hidden' id='CNUM' name='CNUM' value='<?=$this->CNUM ?>' />
        <input type='hidden' id='WORKER_ID' name='WORKER_ID' value='<?=$this->WORKER_ID ?>' />
        <input class='form-control' id='EMAIL_ADDRESS' name='EMAIL_ADDRESS' value='' type='hidden' >
        <input class='form-control' id='KYN_EMAIL_ADDRESS' name='KYN_EMAIL_ADDRESS' value='' type='hidden' >
        <?php
        $this->formHiddenInput('mode',$mode,'mode');
        $allButtons = array();
   		$submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','update',null,'Update') :  $this->formButton('submit','Submit','save',null,'Submit');
   		$resetButton  = $this->formButton('reset','Reset','reset',null,'Reset','btn-warning');
   		$allButtons[] = $submitButton;
   		$allButtons[] = $resetButton;
   		$this->formBlueButtons($allButtons);
  		?>
  		</div>
  		</div>
        </form>
        <?php
    }

    function confirmDeleteSquadAssignmentModal(){
        ?>
        <!-- Modal -->
        <div id="confirmDeleteSquadAssignmentModal" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm deletion of Assignment</h4>
              </div>
              <form id='confirmDeleteSquadAssignmentForm' class="form-horizontal" method='post'>
                <div class="modal-body">
                  <div class="panel"></div>
                </div>
                <div class='modal-footer'>
                  <?php
                  $allButtons = null;
                  $submitButton = $this->formButton('submit','Submit','confirmDeleteSquadAssignment',null,'Confirm','btn-primary');
                  $allButtons[] = $submitButton;
                  $this->formBlueButtons($allButtons);
                  ?>
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <?php
    }
}