<?php
?>
<div class='container'>
<h1>PMO Portal</h1>
</div>

<div class='container-fluid'>
<h3>Person Database</h3>

<button id='reportPes' class='btn btn-primary btn-sm'>PES Report</button>
<button id='reportReset' class='btn btn-primary btn-sm'>Reset</button>

<div id='personDatabaseDiv'>


</div>
</div>

<script>

var person = new personRecord();
person.initialisePersonTable();
person.listenForReportPes();
person.listenForReportReset();
</script>