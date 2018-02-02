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
<input checked data-size="mini" data-toggle="toggle" type="checkbox" class='toggle' data-width='100' data-on="Boarding" data-off="Pre-Boarding" id='hasBpEntry'>
</h2>
</div>
</div>


<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>

<?php
$mode = personRecord::$modeDEFINE;
personRecord::loadKnownCnum();
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
    person.initialisePersonFormSelect2();
    person.listenForHasBpEntry();
	person.listenForName();
    person.listenForSerial();
    person.listenForSaveBoarding();
    person.listenForAccountOrganisation();
    person.listenForCtbRtb();
    person.listenForInitiatePesFromBoarding();
    person.listenForLinkToPreBoarded();

});

$(document).ready(function(){

    updateStartDate = function() {
		console.log('updateStartDate');
		console.log(startPicker);
		console.log(startDate);
        startPicker.setStartRange(startDate);
        endPicker.setStartRange(startDate);
        endPicker.setMinDate(startDate);
		console.log('updateStartDate');
    },
    updateEndDate = function() {
		console.log('updatedEndDate');
        startPicker.setEndRange(endDate);
        startPicker.setMaxDate(endDate);
        endPicker.setEndRange(endDate);
    },
    startPicker = new Pikaday({
    	firstDay:1,
// 		disableDayFn: function(date){
// 		    // Disable weekend
// 		    return date.getDay() === 0 || date.getDay() === 6;
// 		},
        field: document.getElementById('start_date'),
        format: 'D MMM YYYY',
        showTime: false,
        onSelect: function() {
        	console.log('onSelect for startPicker');
        	console.log(this);
            console.log(this.getMoment().format('Do MMMM YYYY'));
            var db2Value = this.getMoment().format('YYYY-MM-DD')
            console.log(db2Value);
            jQuery('#start_date').val(db2Value);
            startDate = this.getDate();
            console.log(startDate);
            updateStartDate();
        }
    }),
    endPicker = new Pikaday({
    	firstDay:1,
// 		disableDayFn: function(date){
// 		    // Disable weekend
// 		    return date.getDay() === 0 || date.getDay() === 6;
// 		},
        field: document.getElementById('end_date'),
        format: 'D MMM YYYY',
        showTime: false,
        onSelect: function() {
        	console.log('onSelect for endPicker');
            console.log(this.getMoment().format('Do MMMM YYYY'));
            var db2Value = this.getMoment().format('YYYY-MM-DD')
            console.log(db2Value);
            jQuery('#end_date').val(db2Value);
            endDate = this.getDate();
            updateEndDate();
        }
    }),
    _startDate = startPicker.getDate(),
    _endDate = endPicker.getDate();

    console.log(startDate);
    console.log(_startDate);

    if (_startDate) {
        startDate = _startDate;
        updateStartDate();
    }

    if (_endDate) {
        endDate = _endDate;
        updateEndDate();
    }
});





</script>

<?php

