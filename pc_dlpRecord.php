<?php

use vbac\personTable;
use vbac\dlpRecord;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>
<h2>Data Leakage Protection - DG&CB Licence Tracking</h2>
<h5>Install DG&CB from :<a href='https://ibm.ent.box.com/s/bzzq07z0dj08exsudctkbwlgr1vwzcp7/folder/49286077657'>https://ibm.ent.box.com/s/bzzq07z0dj08exsudctkbwlgr1vwzcp7/folder/49286077657</a></h5>
</div>
</div>




<div class='row'>
<?php
$mode = dlpRecord::$modeDEFINE;
$dlpRecord = new dlpRecord();
$myCnum = personTable::myCnum();
if(!$myCnum){
    $assetRequest->unknownUser();
} else {
    $dlpRecord->displayForm($mode);
}
?>
</div>

<?=$dlpRecord->saveResponseModal();?>
</div>

<div class='container-fluid'>
<h3>Licenses</h3>
<div id='dlpLicencesReport'>
<table id='dlpLicensesTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead>
<tr><th>CNUM</th><th>LICENSEE<th>HOSTNAME</th><th>APPROVER</th><th>APPROVED</th><th>FUNCTIONAL_MGR</th><th>CREATION_DATE</th><th>EXCEPTION_CODE</th><th>OLD_HOSTNAME</th><th>TRANSFERRED_DATE</th><th>TRANSFERRED_BY</th><th>STATUS</th></tr></thead>
<tbody>
</tbody>
<tfoot><tr><th>CNUM</th><th>LICENSEE</th><th>HOSTNAME</th><th>APPROVER</th><th>APPROVED</th><th>FUNCTIONAL_MGR</th><th>CREATION_DATE</th><th>EXCEPTION_CODE</th><th>OLD_HOSTNAME</th><th>TRANSFERRED_DATE</th><th>TRANSFERRED_BY</th><th>STATUS</th></tr>
</tfoot>
</table>

</div>
</div>


<script type="text/javascript">
var Dlp = new dlp();

$(document).ready(function() {

	Dlp.init();
	Dlp.listenForSaveDlp();
	Dlp.listenForSelectLicencee();
	Dlp.initialiseLicenseeReport();
	Dlp.listenForRejectDlp();
	Dlp.listenForApproveDlp();
	Dlp.listenForDeleteDlp();


});

</script>