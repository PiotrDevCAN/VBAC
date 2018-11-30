<?php
use vbac\personRecord;
use vbac\personTable;
?>

<div class='container'>
<h1 id='portalTitle'>Linked Portal</h1>
<p>Records of Pre-Boarders and the IBMers they've been linked too</p>
</div>

<div class='container-fluid'>
<h3>Person Database</h3>

<button id='reportAction' 		class='btn btn-primary btn-sm '>Action Mode</button>
<button id='reportPes'    		class='btn btn-primary btn-sm '>PES Report</button>
<button id='reportAll'  		class='btn btn-primary btn-sm '>Show all Columns</button>
&nbsp;
<button id='reportReload'  		class='btn btn-warning btn-sm '>Reload Data</button>
<button id='reportReset'  		class='btn btn-warning btn-sm '>Reset</button>
<div id='personDatabaseDiv' class='portalDiv'>
</div>
</div>

<?php
$person = new personRecord();
$person->editPersonModal();

?>

<script>
$(document).ready(function(){
	var person = new personRecord();
	person.initialisePersonTable('<?=personTable::PORTAL_PRE_BOARDER_WITH_LINKED?>');
	person.listenForReportPes();
	person.listenForReportAction();
	person.listenForReportReset();
	person.listenForReportReload();
	person.listenForReportAll();
	person.listenForEditPerson();
	
});

</script>