<?php
use vbac\pesEventsRecord;
use vbac\pesEventTable;
use vbac\allTables;
use vbac\pesTrackerTable;
use vbac\pesTrackerRecord;
use vbac\personRecord;

?>
<div class='container'>
<h1 id='portalTitle'>Pes Tracker.</h1>
</div>

<div class='container-fluid'>
<h3>Pes Tracker</h3>
<?php
$pesTrackerTable = new pesTrackerTable(allTables::$PES_TRACKER);
$pesTrackerTable->displayTable(pesTrackerTable::PES_TRACKER_RECORDS_ACTIVE);

$person = new personRecord();
$person->amendPesStatusModal();
$person->confirmSendPesEmailModal();



?>
</div>
<script>
$(document).ready(function(){
	var pesevent = new pesEvent();	
	var person = new personRecord();

	person.initialisePesTrackerTable();
	
	person.listenForEditPesStatus();
	person.listenForSavePesStatus();
	person.listenForInitiatePesFromPortal();
	person.listenforSendPesEmail();
	person.listenforConfirmSendPesEmail();

	var pesevent = new pesEvent();
	pesevent.listenForPesStageValueChange();
	pesevent.listenForSavePesComment();
	pesevent.listenForPesProcessStatusChange();
});
</script>


