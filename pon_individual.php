<?php
use vbac\personRecord;
use itdq\FormClass;

set_time_limit(0);

//Trace::pageOpening($_SERVER['PHP_SELF']);
?>
<div class='container'>
<h2>Onboard Individual</h2>
<?php
$mode = FormClass::$modeDEFINE;
$person = new personRecord();
$person->displayBpDetails($mode)
?>



</div>
<?php

