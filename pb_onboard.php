<?php
use vbac\personRecord;

set_time_limit(0);

//Trace::pageOpening($_SERVER['PHP_SELF']);
?>

<div class='container'>
<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>
<h2>Onboard Individual</h2>
</div>
</div>

<div class='row'>
  <div class='col-sm-2'></div>
  <div class='col-sm-8'>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs" id="myTabs" role="tablist">
      <li role="presentation"><a href="#regularTab" aria-controls="regularTab" role="tab"  id="showRegularForm"><b>Reg/Contractor</b></a></li>
      <li role="presentation"><a href="#vendorTab" aria-controls="vendorTab" role="tab" id="showVendorForm"><b>Pre-Hire/Vendor</b></a></li>
    </ul>

    <!-- Tab panes -->
    <div class="tab-content">
      <div role="tabpanel" class="tab-pane fade in" id="regularTab">
        <?php
        $mode = personRecord::$modeDEFINE;
        $person = new personRecord();
        $person->displayRegularBoardingForm($mode);
        ?>
      </div>
      <div role="tabpanel" class="tab-pane fade" id="vendorTab">
        <?php
        $mode = personRecord::$modeDEFINE;
        $person = new personRecord();
        $person->displayVendorBoardingForm($mode);
        ?>
      </div>
    </div>
  </div>
</div>

<br/>
<hr/>
<br/>

<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>

<?php
$mode = personRecord::$modeDEFINE;
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