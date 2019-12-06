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
<?php
$squadRecord = new AgileTribeRecord();
$squadRecord->displayForm(FormClass::$modeDEFINE);
?>
</div>

<div class='container'>
<table id='tribeTable' class='table table-striped table-bordered compact'  style='width:100%'>
<thead>
<tr><th>Tribe Number</th><th>Tribe Name</th><th>Tribe Leader</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Tribe Number</th><th>Tribe Name</th><th>Tribe Leader</th></tr>
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

console.log(Tribe);

$(document).ready(function() {
	Tribe.initialiseAgileTribeTable();
	Tribe.listenForSubmitTribeForm();
	Tribe.listenForLeader();
	Tribe.listenForEditTribe();
});

</script>
