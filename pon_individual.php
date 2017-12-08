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
$person->displayBpDetails($mode);

?>
<input type="text" id="faces-input" />

<script>
//Use your API key instead of API_KEY
var config = { key: vbac;rob.daniel@uk.ibm.com};

FacesTypeAhead.init(
    document.getElementById('faces-input'),
    config
    );
</script>


</div>
<?php

