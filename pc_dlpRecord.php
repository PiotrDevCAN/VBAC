<?php

use vbac\personTable;
use vbac\dlpRecord;
use vbac\personRecord;

set_time_limit(0);
ob_start();
?>
<div class='container'>
<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>
<h2>Data Leakage Protection - DG&amp;CB Licence Tracking</h2>
<h5>Install DG / CB Software from <a href='https://kyndryl.box.com/s/spr0ndbw659o3yumbnnnu239a038fbr6' target='_blank' >here</a></h5>
<h5 style="font-weight: bold;">
Please drop a mail to Kyndryl IBM LBG IAM Requests <a href='<?=personRecord::$securityOps[0];?>'><?=personRecord::$securityOps[0];?></a> team if you want to raise any concern related to DG/CB with details like
Laptop Host Name, is it an issue with DG or CB and provide detailed description of issue or error.
</h5>
</div>
</div>

<div class='row'>
<?php
$mode = dlpRecord::$modeDEFINE;
$dlpRecord = new dlpRecord();
$myCnum = personTable::myCnum();
$myCnum = 'aaa';
if(!$myCnum){
    $notFound = true;
    echo '<div class="col-lg-12"><h3>Didnt find user in PERSON table.</h3></div>';
    // throw new Exception("Didnt find user in PERSON table.");
} else {
    $notFound = false;
    $dlpRecord->displayForm($mode);
}
?>
</div>
</div>
<?php
if(!$myCnum){

} else {
?>
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
<?php    
}
?>
<?php
include_once "includes/modalSaveResponse.html";
include_once 'includes/modalConfirmInstalled.html';
include_once 'includes/modalConfirmVerified.html';
?>