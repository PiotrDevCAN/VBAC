<?php

use vbac\AgileSquadRecord;
use itdq\FormClass;
use itdq\Loader;
use vbac\AgileSquadTable;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<h2>Manage Squad Records</h2>
<form id='tribeVersion' class="form-horizontal" method='post'>
    <div class="form-group">
    <div class="col-sm-offset-2 col-sm-4">
    <input  data-toggle="toggle" type="checkbox" class='toggle' data-width='100%' data-on="Original Squads" data-off="New Squads" id='version' name='version' value='Original' data-onstyle='success' data-offstyle='warning' checked>
    </div>
    </div>
</form>
<div id='squadDisplayForm'>
<?php
$squadRecord = new AgileSquadRecord();
$squadRecord->setTribeOrganisation('Original');
$squadRecord->displayForm(FormClass::$modeDEFINE);
?>
</div>
</div>

<div class='container'>
<table id='squadTable' class='table table-striped table-bordered compact'  style='width:100%'>
<thead>
<tr><th>Squad Number</th><th>Squad Type</th><th>Squad Name</th><th>Tribe Number</th><th>Shift</th><th>Squad Leader</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Squad Number</th><th>Squad Type</th><th>Squad Name</th><th>Tribe Number</th><th>Shift</th><th>Squad Leader</th></tr>
</tfoot>
</table>
</div>

<!-- Modal -->
<div id="modalInfo" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modalInfo-title">Information</h4>
      </div>
      <div class="modalInfo-body" >
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default modalButton" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>

<?=AgileSquadTable::buildTribeSelects();?>

<script type="text/javascript">

var Squad = new agileSquad();

function initialiseTribeNumber(selectedTribeNumber){
	var version = $('#version').prop('checked') ? 'Original' : 'New';
	var organisation = $('#radioTribeOrganisationManaged').prop('checked') ? 'Managed' : 'Project';
	var tribeSelectName = 'tribes' + version + organisation;
	var tribesSelect = eval(tribeSelectName);
	if ($('#TRIBE_NUMBER').hasClass("select2-hidden-accessible")) {
    // 	Select2 has been initialized
    	$('#TRIBE_NUMBER').empty().trigger('change');
    	$('#TRIBE_NUMBER').select2('destroy');
	}

	$('#TRIBE_NUMBER').select2({
		data: tribesSelect
	});

	if(selectedTribeNumber){
		$('#TRIBE_NUMBER').val(selectedTribeNumber).trigger('change');
	}
	$('#SHIFT').select2();
}
// Set the listener for change to Organisation
function setListenerForOrganisation(){
	console.log('set listener for radio button');
    $('input[type=radio]').click(function(){
        initialiseTribeNumber();
//         if ($('#TRIBE_NUMBER').hasClass("select2-hidden-accessible")) {
//             $('#TRIBE_NUMBER').select2('destroy');
//             initialiseTribeNumber();
//         };
    });
}


$(document).ready(function() {
	Squad.initialiseAgileSquadTable();
	Squad.listenForSubmitSquadForm();
	Squad.listenForLeader();
	Squad.listenForEditSquad();

	initialiseTribeNumber();
	setListenerForOrganisation();

    $('#version').bootstrapToggle();

    $('#version').change({squad: agileSquad}, function(event) {
        var version = $('#version').prop('checked') ? 'Original' : 'New';
        $("#TRIBE_NUMBER").empty().trigger('change')
        $('#squadDisplayForm').html('');
        $.ajax({
            url: "ajax/getSquadRecordDisplayForm.php",
            type: 'POST',
            data: { version: version },
            success: function(result){
                var resultObj = JSON.parse(result);
            	$('#squadDisplayForm').html(resultObj.displayForm);
            	initialiseTribeNumber();
            	setListenerForOrganisation();
            }
      });
    	event.data.squad.table.ajax.reload();
    });
});


</script>
