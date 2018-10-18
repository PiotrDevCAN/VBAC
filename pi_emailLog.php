<?php
use itdq\Trace;

set_time_limit(0);
do_auth($_SESSION['userBg']);

Trace::pageOpening($_SERVER['PHP_SELF']);

?>
<div class='container'>


<h3>Select Dates</h3>
<form id='reportDates'>
	<div class='form-group' >
       <div id='START_DATE" . "FormGroup' >
       <label for='START_DATE' class='col-md-1 control-label ' data-toggle='tooltip' data-placement='top' title=''>From</label>
       <div class='col-md-2'>
       <div id='calendarFormGroupSTART_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='START_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
       <input id='InputSTART_DATE' class='form-control' type='text' readonly value='' placeholder='Select From' required />
       <input type='hidden' id='START_DATE' name='START_DATE' value='' />
       <span class='input-group-addon'><span id='calendarIconSTART_DATE' class='glyphicon glyphicon-calendar'></span></span>
       </div>
       </div>
       </div>

       <div id='END_DATE" . "FormGroup'>
       <label for='END_DATE' class='col-md-1 control-label ' data-toggle='tooltip' data-placement='top' title=''>To</label>
       <div class='col-md-2'>
       <div id='calendarFormGroupEND_DATE' class='input-group date form_datetime' data-date-format='dd MM yyyy - HH:ii p' data-link-field='END_DATE' data-link-format='yyyy-mm-dd-hh.ii.00'>
       <input id='InputEND_DATE' class='form-control' type='text' readonly value='' placeholder='Select To' required />
       <input type='hidden' id='END_DATE' name='END_DATE' value='' />
       <span class='input-group-addon'><span id='calendarIconEND_DATE' class='glyphicon glyphicon-calendar'></span></span>
       </div>
       </div>
       </div>
       </div>
</form>
</div>

<hr/>

<div class='container-fluid'>
<h3>Email Log</h3>
<div id='emailLogDiv'>
<table id='emailLogTable' class='table table-striped table-bordered compact'   width='100%'>
<thead>
<tr><th>Id</th><th>Details</th><th>Message</th><th>Status</th><th>Sent Timestamp</th><th>Status Timestamp</th></tr></thead>
<tbody>
</tbody>
<tfoot><tr><th>Id</th><th>Details</th><th>Message</th><th>Status</th><th>Sent Timestamp</th><th>Status Timestamp</th></tr></tfoot></table>

</div>
</div>






<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);
?>

<script>
$(document).ready(function() {
	var emailLog = new EmailLog();
	emailLog.initialiseDateSelect();
	emailLog.initialiseDataTable();
	emailLog.listenForcheckStatus();
	emailLog.listenForResendEmail();
});

</script>