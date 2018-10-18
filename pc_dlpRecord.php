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
<h5>Install DG&CB from :<a href='http://ibm.biz/BdYzHY'>http://ibm.biz/BdYzHY</a></h5>
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
<h3 id='portalTitle'>Licenses</h3>
<div class='row'>
&nbsp;
<button id='reportShowDlpActive'  		class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Active Records</button>
<button id='reportShowDlpPending'      class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Requires Approval</button>
<button id='reportShowDlpTransferred'  class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Transferred Licenses</button>
<button id='reportShowDlpRejected'     class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>Rejected Records</button>
<button id='reportShowDlpAll'		    class='btn btn-primary btn-sm accessBasedBtn accessPmo accessCdi'>All Records</button>
</div>
<div class='row'>
<a class='btn btn-sm btn-link accessBasedBtn accessPmo accessCdi' href='/dn_dlpActive.php'><i class="glyphicon glyphicon-download-alt"></i> DLP Download</a>
</div>




<div id='dlpLicencesReport'>
<table id='dlpLicensesTable' class='table table-striped table-bordered compact'   width='100%'>
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
	Dlp.listenForReportShowDlpActive();
	Dlp.listenForReportShowDlpPending();
	Dlp.listenForReportShowDlpTransferred();
	Dlp.listenForReportShowDlpRejected();
	Dlp.listenForReportShowDlpAll();



});

</script>