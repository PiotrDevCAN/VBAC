<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;

class skillSetRecord extends DbRecord {

    protected $SKILLSET_ID;
    protected $SKILLSET;

    function displayForm($mode){
        ?>
        <form id='skillSetForm' class="form-horizontal" method='post'>
            <div class="form-group required " >
                <label for='SKILLSET' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Full Name'>Skillset</label>
                <div class='col-md-4'>
                    <input id='SKILLSET' name='SKILLSET' class='form-control' value='<?=!empty($this->SKILLSET) ? $this->SKILLSET :null ; ?>' />
                </div>
            </div>
            <div class='form-group'>
                <div class='col-sm-offset-2 -col-md-4'>
                    <?php
                    $this->formHiddenInput('mode',$mode,'mode');
                    $this->formHiddenInput('SKILLSET_ID',$this->SKILLSET_ID,'SKILLSET_ID');
                    $allButtons = array();
                    $submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateSkillset',null,'Update') :  $this->formButton('submit','Submit','saveSkillset',null,'Submit');
                    $resetButton  = $this->formButton('reset','Reset','resetSkillset  Form',null,'Reset','btn-warning');
                    $allButtons[] = $submitButton;
                    $allButtons[] = $resetButton;
                    $this->formBlueButtons($allButtons);
                    ?>
                </div>
            </div>
        </form>
    <?php
    }

    function confirmDeleteSkillsetModal(){
        ?>
        <!-- Modal -->
        <div id="confirmDeleteSkillsetModal" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm deletion of Skillset</h4>
              </div>
              <form id='confirmDeleteSkillsetForm' class="form-horizontal" method='post'>
                <div class="modal-body">
                  <div class="panel"></div>
                </div>
                <div class='modal-footer'>
                  <?php
                  $allButtons = null;
                  $submitButton = $this->formButton('submit','Submit','confirmDeleteSkillset',null,'Confirm','btn-primary');
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