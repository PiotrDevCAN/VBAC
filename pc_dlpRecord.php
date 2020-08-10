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
<h2>Data Leakage Protection - DG&amp;CB Licence Tracking</h2>
<h5>Install DG software from <a href='https://ibm.box.com/s/n6aw9fj0ungblkxcctx4y4y4f2ebk5up'>box here</a></h5>
<h5>Install CB software from <a href='https://ibm.box.com/s/uv0duml1r8nndxx5089bsvideh7z4ipk'>box here</a></h5>
</div>
</div>




<div class='row'>
<?php
$mode = dlpRecord::$modeDEFINE;
$dlpRecord = new dlpRecord();
$myCnum = personTable::myCnum();
if(!$myCnum){
   throw new Exception("Didnt find user in PERSON table.");
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
<table id='dlpLicensesTable' class='table table-striped table-bordered compact'   style='width:100%'>
<thead>
<tr><th>CNUM</th><th>LICENSEE<th>HOSTNAME</th><th>APPROVER</th><th>APPROVED</th><th>FUNCTIONAL_MGR</th><th>CREATION_DATE</th><th>EXCEPTION_CODE</th><th>OLD_HOSTNAME</th><th>TRANSFERRED_DATE</th><th>TRANSFERRED_BY</th><th>STATUS</th></tr></thead>
<tbody>
</tbody>
<tfoot><tr><th>CNUM</th><th>LICENSEE</th><th>HOSTNAME</th><th>APPROVER</th><th>APPROVED</th><th>FUNCTIONAL_MGR</th><th>CREATION_DATE</th><th>EXCEPTION_CODE</th><th>OLD_HOSTNAME</th><th>TRANSFERRED_DATE</th><th>TRANSFERRED_BY</th><th>STATUS</th></tr>
</tfoot>
</table>

</div>
</div>

<!-- Modal' Follow -->

		<div id="confirmInstalled" class="modal fade" role="dialog">
  			<div class="modal-dialog">
	        <!-- Modal content-->
    		<div class="modal-content">
      			<div class="modal-header">
        		   <h4 class="modal-title">Confirm DLP is Installed</h4>
      			</div>
      			<div class="modal-body" >
      			<h1 class='text-center' style='font-size:43px;background-color:red;color:white'>STOP</h1>
				<p>You must download and install the software <b>BEFORE</b> raising this request. It is against BCGs to create a DLP record if you don't have the software installed.</p>
				<label for='dlpInstalConfirmed'>Confirm DLP software is installed</label>
				<input type='checkbox' id='dlpInstalConfirmed'>
        		</div>
        		<div class='modal-footer'>
      		  		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
      			</div>
        </div>
        </div>
        </div>

        	<div id="confirmVerified" class="modal fade" role="dialog">
  			<div class="modal-dialog">
	        <!-- Modal content-->
    		<div class="modal-content">
      			<div class="modal-header">
        		   <h4 class="modal-title">Confirm DLP instalation has been verified</h4>
      			</div>
      			<div class="modal-body" >
      			<h1 class='text-center' style='font-size:56px;background-color:red;color:white'>STOP</h1>
				<p>Before approving this request, it is your responsibility to ensure the software is installed on the individual's machine.</p>
					<label for='dlpInstallVerfied'>Confirm DLP software is installed</label>
					<input type='checkbox' id='dlpInstallVerfied'>

        		</div>
        		<div class='modal-footer'>
      		  		<button type="button" class="btn btn-default" data-dismiss="modal" >Close</button>
      			</div>
        </div>
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