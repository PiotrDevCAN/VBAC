<?php

use vbac\personRecord;
use vbac\assetRequestRecord;
use vbac\personTable;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>
<h2>Resource Strategy - Ring Fencing</h2>
</div>
</div>


<div class='row'>
<?php
$mode = personRecord::$modeDEFINE;
$personRecord = new personRecord();
$assetRequest = new assetRequestRecord();
$myCnum = personTable::myCnum();
if(!$myCnum){
    $assetRequest->unknownUser();
} else {
    $personRecord->displayRfFlagForm($mode);
}
?>
</div>
</div>

<div class='container-fluid'>
<hr/>
<div class='row'>
<a class='btn btn-sm btn-link accessRes' href='/dn_ringFenced.php'><i class="glyphicon glyphicon-download-alt"></i> Ring Fenced Report</a>
</div>


<div id='rfFlagReport'>
<table id='rfFlagTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead>
<tr><th>CNUM</th><th>NOTES_ID</th><th>LOB</th><th>CTB_RTB</th><th>FM</th><th>REVAL</th><th>EXP</th><th>FROM</th><th>TO</th></tr></thead>
<tbody>
</tbody>
<tfoot>
<tr><th>CNUM</th><th>NOTES_ID</th><th>LOB</th><th>CTB_RTB</th><th>FM</th><th>REVAL</th><th>EXP</th><th>FROM</th><th>TO</th></tr></thead>
</tfoot>
</table>

</div>
</div>


<script type="text/javascript">
var Person = new personRecord();

$(document).ready(function() {

	Person.initialiseRfStartEndDate();
	Person.initialiseRfFlagReport();
	Person.listenForSaveRfFlag();
	Person.listenForDeleteRfFlag();




});

</script>
