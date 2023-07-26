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
<input checked data-size="mini" data-toggle="toggle" type="checkbox" class='toggle' data-width='120' data-on="Reg/Contractor" data-off="Pre-Hire/Vendor" data-onstyle="primary" data-offstyle="warning" id='hasBpEntry'/>
</h2>
</div>
</div>

<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>

<?php
$mode = personRecord::$modeDEFINE;
personRecord::loadKnownCnum();          // knownCnum
personRecord::loadKnownExternalEmail(); // knownExternalEmail
personRecord::loadKnownIBMEmail();      // knownIBMEmail
personRecord::loadKnownKyndrylEmail();  // knownKyndrylEmail
$person = new personRecord();
$person->displayBoardingForm($mode);
?>
</div>
<div class='col-sm-2'></div>
</div>
</div>
<?php
include_once 'includes/modalAdditionalBoardingDetails.html';
include_once 'includes/modalSavingBoardingDetails.html';
?>

<style>
  .toolTipDetails {
    width:  600px;
    max-width: 600px;
    overflow:auto;
  } 
  .toolTipDetails p {
      margin: 0;
      font-weight: bold;
  }
</style>
<?php