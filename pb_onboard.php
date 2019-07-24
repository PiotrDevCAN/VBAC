<?php
use vbac\personRecord;
use itdq\FormClass;
use itdq\Loader;
use vbac\allTables;
use vbac\staticDataSubPlatformTable;

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
staticDataSubPlatformTable::prepareJsonObjectForSubPlatformSelect();
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

function changeSubplatform(dataCategory){
    $("#subPlatform").select2({
        data:dataCategory,
        placeholder:'Select'
    })
    .attr('disabled',false)
    .attr('required',true);

};

$(document).ready(function() {
	$('.toggle').bootstrapToggle()

	var person = new personRecord();
    person.initialisePersonFormSelect2();
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

    $("#work_stream").on( "change", function(e){
        if($('.accountOrganisation:checked').val()=='BAU'){
            var workstream = $('#work_stream').val();
            var workstreamId = workstreamDetails[workstream];
            $("#subPlatform").select2("destroy");
            $("#subPlatform").html("<option><option>");
            changeSubplatform( platformWithinStream[workstreamId] );
        } else {
            $("#subPlatform").select2({
                placeholder:'Select'
            })
            .val('')
            .trigger('change')
            .attr('disabled',true)
            .attr('required',false);
        }

    });



});

</script>

<?php

