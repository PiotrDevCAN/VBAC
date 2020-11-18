<?php
use vbac\personRecord;
use itdq\FormClass;
use itdq\Loader;
use vbac\allTables;

set_time_limit(0);

//Trace::pageOpening($_SERVER['PHP_SELF']);
?>

<div class='container'>
<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>
<h2>Onboard Individual
<input checked data-size="mini" data-toggle="toggle" type="checkbox" class='toggle' data-width='120' data-on="IBMer Boarding" data-off="Pre-Hire/Vendor" data-onstyle="primary" data-offstyle="warning" id='hasBpEntry'>
</h2>
</div>
</div>


<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>

<?php
$mode = personRecord::$modeDEFINE;
personRecord::loadKnownCnum();
personRecord::loadKnownEmail();
$person = new personRecord();
$person->displayBoardingForm($mode);
?>
</div>
<div class='col-sm-2'></div>
</div>
</div>
<?php
$person->savingBoardingDetailsModal();
// $loader = new Loader();
// $countryCodes = $loader->loadIndexed('COUNTRY_NAME','COUNTRY_CODE',allTables::$STATIC_COUNTRY_CODES);
?>

<script type="text/javascript">
var startDate,endDate;
var startPicker, endPicker;


$(document).ready(function() {
	$('.toggle').bootstrapToggle()

	var person = new personRecord();
    person.initialiseStartEndDate();
    person.listenForHasBpEntry();
	person.listenForName();
	person.listenForEmail();
    person.listenForSerial();
    person.listenForSaveBoarding();
    person.listenForAccountOrganisation();
    person.listenForCtbRtb();
    person.listenForInitiatePesFromBoarding();
    person.listenForLinkToPreBoarded();
    person.listenForEmployeeTypeRadioBtn();

});





</script>

<?php

