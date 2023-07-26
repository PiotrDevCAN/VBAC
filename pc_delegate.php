<?php

use vbac\delegateRecord;

set_time_limit(0);

$delegate = new delegateRecord();

?>
<div class='row'>
<div class='col-sm-offset-4 col-sm-4' >
<?=$delegate->displayForm();?>
</div>
</div>
<div class='row'>
<div class='col-sm-offset-4 col-sm-4' >
<?=$delegate->displayMyDelegates();?>
</div>
</div>