<?php
namespace vbac;

use itdq\DbRecord;
use itdq\FormClass;

class bandMappingRecord extends DbRecord {

    protected $BUSINESS_TITLE;
    protected $BAND;

    function displayForm($mode){
        ?>
        <form id='bandMappingForm' class="form-horizontal" method='post'>
            <div class="form-group required " >
                <label for='BUSINESS_TITLE' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Full Name'>Business Title</label>
                <div class='col-md-4'>
                    <input id='BUSINESS_TITLE' name='BUSINESS_TITLE' class='form-control' value='<?=!empty($this->BUSINESS_TITLE) ? $this->BUSINESS_TITLE :null ; ?>' />
                </div>
            </div>
            <div class="form-group required " >
                <label for='BAND' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Full Name'>Band</label>
                <div class='col-md-4'>
                    <input id='BAND' name='BAND' class='form-control' value='<?=!empty($this->BAND) ? $this->BAND :null ; ?>' />
                </div>
            </div>
            <div class='form-group'>
                <div class='col-sm-offset-2 -col-md-4'>
                    <?php
                    $this->formHiddenInput('mode',$mode,'mode');
                    $allButtons = array();
                    $submitButton = $mode==FormClass::$modeEDIT ?  $this->formButton('submit','Submit','updateBandMapping',null,'Update') :  $this->formButton('submit','Submit','saveBandMapping',null,'Submit');
                    $resetButton  = $this->formButton('reset','Reset','resetBandMapping  Form',null,'Reset','btn-warning');
                    $allButtons[] = $submitButton;
                    $allButtons[] = $resetButton;
                    $this->formBlueButtons($allButtons);
                    ?>
                </div>
            </div>
        </form>
    <?php
    }

    function confirmDeleteBandMappingModal(){
        ?>
        <!-- Modal -->
        <div id="confirmDeleteBandMappingModal" class="modal fade" role="dialog">
          <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
              <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Confirm deletion of Band Mapping</h4>
              </div>
              <form id='confirmDeleteBandMappingForm' class="form-horizontal" method='post'>
                <div class="modal-body">
                  <div class="panel"></div>
                </div>
                <div class='modal-footer'>
                  <?php
                  $allButtons = null;
                  $submitButton = $this->formButton('submit','Submit','confirmDeleteBandMapping',null,'Confirm','btn-primary');
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