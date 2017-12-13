<?php
use vbac\personRecord;
use itdq\FormClass;

set_time_limit(0);

//Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>
<h2>Onboard Individual</h2>
</div>
<div class='col-sm-2'></div>
</div>
<div class='row'>
<?php
$mode = FormClass::$modeDEFINE;
$person = new personRecord();
$person->displayBpDetails($mode);
?>
</div>


</div>

<script type="text/javascript">
<!--
$(document).ready(function() {
	var person = new personRecord();
    person.listenForName();
    person.listenForSerial();
});
//-->

console.log($('#NAME'));

</script>

<?php

