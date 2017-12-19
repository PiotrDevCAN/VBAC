<?php
use vbac\accessRequestRecord;
use itdq\FormClass;

?>


<div class='container'>
<div class='row'>
<div class='col-sm-2'></div>
<div class='col-sm-8'>
<h2>Access Request</h2>
</div>
<div class='col-sm-2'></div>
</div>
<div class='row'>
<?php
$mode = FormClass::$modeDEFINE;
$accessRequest = new accessRequestRecord();
$accessRequest->displayForm($mode);
?>
</div>


</div>
