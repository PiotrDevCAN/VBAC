<?php

use vbac\AgileSquadRecord;
use itdq\FormClass;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<h2>Manage Squad Records</h2>
<?php
$squadRecord = new AgileSquadRecord();
$squadRecord->displayForm(FormClass::$modeDEFINE);
?>
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



<script type="text/javascript">
var Squad = new agileSquad();

$(document).ready(function() {
	Squad.initialiseAgileSquadTable();
	Squad.listenForSubmitSquadForm();
	Squad.listenForLeader();
	Squad.listenForEditSquad();
});

</script>
