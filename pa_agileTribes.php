<?php

use vbac\personRecord;
use vbac\assetRequestRecord;
use vbac\personTable;
use vbac\AgileTribeRecord;
use itdq\FormClass;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<h2>Manage Tribe Records</h2>
<form id='tribeVersion' class="form-horizontal" method='post'>
    <div class="form-group">
    <div class="col-sm-offset-2 col-sm-4">
    <input  data-toggle="toggle" type="checkbox" class='toggle' data-width='100%' data-on="Original Squads" data-off="New Squads" id='version' name='version' value='Original' data-onstyle='success' data-offstyle='warning' checked>
    </div>
    </div>
</form>


<?php
$squadRecord = new AgileTribeRecord();
$squadRecord->displayForm(FormClass::$modeDEFINE);
?>
</div>

<div class='container'>
<table id='tribeTable' class='table table-striped table-bordered compact'  style='width:100%'>
<thead>
<tr><th>Tribe Number</th><th>Tribe Name</th><th>Tribe Leader</th><th>Organisation</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Tribe Number</th><th>Tribe Name</th><th>Tribe Leader</th><th>Organisation</th></tr>
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



<script type="text/javascript">
var Tribe = new agileTribe();

$(document).ready(function() {

	version = $('#version').prop('checked') ? 'Original' : 'New';
	Tribe.initialiseAgileTribeTable(version);
	Tribe.listenForSubmitTribeForm();
	Tribe.listenForLeader();
	Tribe.listenForEditTribe();

    $('#version').bootstrapToggle();

    console.log(Tribe);

    console.log(agileTribe.table);
    console.log(agileTribe.spinner);

    $('#version').change({tribe: agileTribe.table}, function(event) {
        console.log(event);
        console.log(event.data);
    	event.data.tribe.ajax.reload();
    });



});

</script>
