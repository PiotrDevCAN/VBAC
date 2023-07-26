<?php
use vbac\personRecord;

?>
<div class='container'>
<h1 id='portalTitle'>Person Finder</h1>
</div>

<div class='container-fluid'>
<h3>Transfer Management Alignment</h3>
<div id='personFinderDatabaseDiv' class='portalDiv'>
<table id='personFinderTable' class='table table-striped table-bordered compact' cellspacing='0' width='100%' style='display: none;'>
<thead>
<tr><th>CNUM</th><th>FIRST_NAME</th><th>LAST_NAME</th><th>EMAIL_ADDRESS</th><th>KYNDRYL_EMAIL_ADDRESS</th><th>NOTES_ID</th><th>FM_CNUM</th></tr>
</thead>
<tbody>
</tbody>
<tfoot>
<tr><th>CNUM</th><th>FIRST_NAME</th><th>LAST_NAME</th><th>EMAIL_ADDRESS</th><th>KYNDRYL_EMAIL_ADDRESS</th><th>NOTES_ID</th><th>FM_CNUM</th></tr>
</tfoot>
</table>
</div>
</div>

<?php
$person = new personRecord();
$person->confirmTransferModal();
?>