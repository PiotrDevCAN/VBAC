<?php

use vbac\allTables;
use vbac\pesTrackerTable;
use vbac\personRecord;

?>
<div class='container'>
<div class='row'>
<div class='col-sm-4'>
</div>
<div class='col-sm-4'>
<h1 id='portalTitle' class='text-centre' >PES Tracker.</h1>
</div>
<div class='col-sm-4'>
</div>
</div>
</div>

<div class='container-fluid'>
<?php
$pesTrackerTable = new pesTrackerTable(allTables::$PES_TRACKER);
$pesTrackerTable->displayTable(pesTrackerTable::PES_TRACKER_RECORDS_ACTIVE);

$person = new personRecord();
$person->amendPesStatusModal();
$person->amendPesLevelModal();
$person->confirmSendPesEmailModal();
?>
</div>
<script type="text/javascript">

document.isCdi = <?= $_SESSION['isCdi'] ? "'yes'" : "'no'";?>;
document.isFm = <?= $_SESSION['isFm'] ? "'yes'" : "'no'";?>;
document.tableType = '<?=pesTrackerTable::PES_TRACKER_RECORDS_ACTIVE_REQUESTED ?>';

</script>
<style>
.alert-secondary {
	background-color:light-grey;
	color: black;
}
.pesComments {
	overflow-y: auto;
    height: 100px;
}
.btn-success:focus, .btn-success.focus {
    color: #ffffff;
    background-color: #2c8d3a;
    border-color: #1a5322;
}
.btn-warning:focus, .btn-warning.focus {
    color: #ffffff;
    background-color: #e7a413;
    border-color: #a0720d;
}
.btn-default {
    color: #ffffff;
    background-color: #aea79f;
    border-color: #aea79f;
}
.btn-info:focus, .btn-info.focus {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #3a87ad;
}
.alert-info {
    background-color: #d9edf7;
    border-color: #bce8f1;
    color: #3a87ad;
}
.alert-warning {
    background-color: #fcf8e3;
    border-color: #fbeed5;
    color: #c09853;
}
.alert-success {
    background-color: #dff0d8;
    border-color: #d6e9c6;
    color: #468847;
}
.alert-danger {
    background-color: #f2dede;
    border-color: #eed3d7;
    color: #b94a48;
}
td {
    font-size: 8px;
}
.pesComments {
    font-size: 10px;
}
.formattedEmailTd {
    font-size: 10px;
}
.btnTogglePesTrackerStatusDetails{
    margin-right: 10px;
    margin-top: 5px;
}
</style>