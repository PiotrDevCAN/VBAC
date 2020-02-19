<?php
namespace vbac;

use itdq\DbRecord;
use itdq\Loader;
use itdq\AuditTable;
use itdq\FormClass;
use vbac\AgileSquadTable;


class AgileSquadRecord extends DbRecord {

    protected $SQUAD_NUMBER;
    protected $SQUAD_TYPE;
    protected $TRIBE_NUMBER;
    protected $SHIFT;
    protected $SQUAD_LEADER;
    protected $SQUAD_NAME;
    protected $tribeOrganisation;

    function setTribeOrganisation($version){
        $this->tribeOrganisation = $version;
    }


    function displayForm($mode=FormClass::modeDefine){
        $loader = new Loader();
        $notEditable = $mode == FormClass::$modeEDIT ? ' disabled ' : '';
        $nextAvailableSquadNumber = AgileSquadTable::nextAvailableSquadNumber($this->tribeOrganisation);

        $tribeTable = $this->tribeOrganisation=='Original' ? allTables::$AGILE_TRIBE : allTables::$AGILE_TRIBE_NEW;

        $allTribesOrganisation = $loader->loadIndexed("ORGANISATION","TRIBE_NUMBER", $tribeTable);
        $allTribes = $loader->loadIndexed("TRIBE_NAME","TRIBE_NUMBER", $tribeTable);
        ?>
        <form id='squadForm' class="form-horizontal" method='post'>
         <div class="form-group required" >
            <label for=SQUAD_NUMBER class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Squad Number'>Squad Number</label>
        	<div class='col-md-4'>
				<input id='SQUAD_NUMBER' name='SQUAD_NUMBER' class='form-control' type='number' <?=$notEditable;?> value='<?=!empty($this->SQUAD_NUMBER) ? $this->SQUAD_NUMBER :$nextAvailableSquadNumber ; ?>' />
            </div>
        </div>
        <div class="form-group required" >
            <label for='Organisation' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Organisation'>Organisation</label>
        	<div class='col-md-4'>
        	 <div class="form-check">
             <input class="form-check-input" name='Organisation'  type="radio" id="radioTribeOrganisationManaged" value="Managed Services" >
             <label class="form-check-label " for="radio" id="radioTribeOrganisation">Managed Services</label>
             </div>

             <div class="form-check">
             <input class="form-check-input" name='Organisation'  type="radio" id="radioTribeOrganisationProject" value="Project Services" >
             <label class="form-check-label " for="radio" id="radioTribeOrganisationProject">Project Services</label>
             </div>
            </div>
        </div>
        <div class="form-group required " >
            <label for='SQUAD_TYPE' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Squad Type'>Squad Type</label>
        	<div class='col-md-4'>
				<input id='SQUAD_TYPE' name='SQUAD_TYPE' class='form-control' value='<?=!empty($this->SQUAD_TYPE) ? $this->SQUAD_TYPE :null ; ?>' />
            </div>
        </div>
        <div class="form-group required " >
            <label for='SQUAD_NAME' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Squad NAme'>Squad Name</label>
        	<div class='col-md-4'>
				<input id='SQUAD_NAME' name='SQUAD_NAME' class='form-control' value='<?=!empty($this->SQUAD_NAME) ? $this->SQUAD_NAME :null ; ?>' />
            </div>
        </div>
        <div class="form-group required" >
            <label for='TRIBE_NUMBER' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Tribe'>Tribe</label>
        	<div class='col-md-4'>
				<SELECT id='TRIBE_NUMBER' class='form-control select2'  name='TRIBE_NUMBER' >
    				<option value=''></option>
    				<?php
    				foreach ($allTribes as  $tribeNumber => $tribeName) {
    				    ?><option data-organisation='<?=$allTribesOrganisation[trim($tribeNumber)] ?>' value='<?=trim($tribeNumber)?>'
    				    <?=$this->TRIBE_NUMBER == $tribeNumber ? ' selected ' : null;?>
    				    disabled >
    				    <?=trim($tribeName) . " - " . $allTribesOrganisation[trim($tribeNumber)]?>
    				    </option><?php
                        }
                    ?>
    			</select>
        	</div>
        </div>

        <div class="form-group required" >
            <label for='SHIFT' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Squad works Shifts'>Shifts</label>
        	<div class='col-md-4'>
				<SELECT id='SHIFT' class='form-control select2' <?=$notEditable ?> name='SHIFT' >
    				<option value=''></option>
    				<?php
    				foreach (array('Y'=>'Yes','N'=>'No') as $key =>  $shift) {
    				    ?><option value='<?=trim($key)?>'><?=trim($shift)?>
    				    <?=$this->SHIFT == $key ? ' selected ' : null;?>
    				    </option><?php
                        }
                    ?>
    			</select>
        	</div>
        </div>
        <div class="form-group " >
            <label for='SQUAD_LEADER' class='col-sm-2 control-label ceta-label-left' data-toggle='tooltip' data-placement='top' title='Squad Leader'>Squad Leader</label>
        	<div class='col-md-4'>
				<input id='SQUAD_LEADER' name='SQUAD_LEADER' class='form-control typeaheadNotesId' value='<?=!empty($this->SQUAD_LEADER) ? $this->SQUAD_LEADER :null ; ?>'/>
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
	<script>
	function populateTribeDropDown()
	{
		  $('#TRIBE_NUMBER').empty().trigger('change');
		  $('#TRIBE_NUMBER').append('<option value=""></option>')
		  <?php
			foreach ($allTribes as  $tribeNumber => $tribeName) {
		    ?>.append("<option data-organisation='<?=$allTribesOrganisation[trim($tribeNumber)] ?>' value='<?=trim($tribeNumber)?>' ><?=trim($tribeName)?></option>")
		    <?php
            }
            ?>
            .trigger('change');
	}
	</script>
    <?php
    }

}