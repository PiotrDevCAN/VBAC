<?php
namespace vbac;

use itdq\DbRecord;
use itdq\Loader;
use itdq\JavaScript;
use itdq\AuditTable;
use itdq\FormClass;

class workLocationRecord extends DbRecord {

    protected $ID;
    protected $COUNTRY;
    protected $CITY;
    protected $ADDRESS;
    protected $ONSHORE;
    protected $CBC_IN_PLACE;

    function displayForm($mode){
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';
        $loader = new Loader();
        $allCountries = $loader->load("COUNTRY",allTables::$STATIC_LOCATIONS);
        $allCities = $loader->load("CITY",allTables::$STATIC_LOCATIONS);
        ?>
        <form id='workLocationForm' class="form-horizontal" method='post'>
            <div class="form-group required" >
                <label for='COUNTRY' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Country'>Country</label>
                <div class='col-md-4'>
                    <select class='form-control select select2' id='COUNTRY' name='COUNTRY'>
                        <option value=''>Select..</option>
                        <?php
                        foreach ($allCountries as $key =>  $value) {
                            ?><option value='<?=trim($key)?>'><?=trim($value)?>
                            <?=$this->COUNTRY == $key ? ' selected ' : null;?>
                            </option><?php
                            }
                        ?>
                    </select>
                
                </div>
            </div>
            <div class="form-group required " >
                <label for='CITY' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='City'>City</label>
                <div class='col-md-4'>
                    <select class='form-control select select2' id='CITY' name='CITY'>
                        <option value=''>Select..</option>
                        <?php
                        foreach ($allCities as $key =>  $value) {
                            ?><option value='<?=trim($key)?>'><?=trim($value)?>
                            <?=$this->CITY == $key ? ' selected ' : null;?>
                            </option><?php
                            }
                        ?>
                    </select>
                
                </div>
            </div>
            <div class="form-group " >
                <label for='ADDRESS' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Address'>Address</label>
                <div class='col-md-4'>
                    <textarea class="form-control" id="ADDRESS" name="ADDRESS" ><?=!empty($this->ADDRESS) ? $this->ADDRESS :null ; ?></textarea>
                </div>
            </div>
            <div class="form-group required " >
                <label for='ONSHORE' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='On Shore'>On Shore</label>
                <div class='col-md-4'>
                    <select id='ONSHORE' class='form-control select2' <?=$notEditable ?> name='ONSHORE' >
                        <option value=''>Select..</option>
                        <?php
                        foreach (array('Y'=>'Yes','N'=>'No') as $key =>  $value) {
                            ?><option value='<?=trim($value)?>'><?=trim($value)?>
                            <?=$this->ONSHORE == $key ? ' selected ' : null;?>
                            </option><?php
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group required " >
                <label for='CBC_IN_PLACE' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='CBC In Place'>CBC In Place</label>
                <div class='col-md-4'>
                    <select id='CBC_IN_PLACE' class='form-control select2' <?=$notEditable ?> name='CBC_IN_PLACE' >
                        <option value=''>Select..</option>
                        <?php
                        foreach (array('Y'=>'Yes','N'=>'No') as $key =>  $value) {
                            ?><option value='<?=trim($value)?>'><?=trim($value)?>
                            <?=$this->CBC_IN_PLACE == $key ? ' selected ' : null;?>
                            </option><?php
                            }
                        ?>
                    </select>
                </div>
            </div>
            <div class='form-group'>
                <div class='col-sm-offset-2 -col-md-4'>
                    <?php
                    $this->formHiddenInput('mode',$mode,'mode');
                    $this->formHiddenInput('ID',$this->ID,'ID');
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