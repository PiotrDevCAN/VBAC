<?php
use vbac\personRecord;

?>
<div class='container'>
<h1 id='portalTitle'>Person Finder</h1>
</div>

<div class='container-fluid'>
<h3>Transfer Management Alignment</h3>
<div id='personFinderDatabaseDiv' class='portalDiv'>
</div>
</div>

<?php
$person = new personRecord();
$person->confirmTransferModal();
?>

<script>
$(document).ready(function(){
	var person = new personRecord();
	person.initialisePersonFinderTable();
	person.listenForBtnTransfer();
	person.listenForBtnTransferConfirmed();
});
</script>