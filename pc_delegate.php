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


<script>

$(document).ready(function() {

	var Delegate = new delegate();

	var AssetPortal = new assetPortal();

    $('.select2').select2({
        'placeholder' : 'Select your delegate'

        });

    Delegate.listenForSaveDelegate();
    Delegate.initialiseMyDelegatesDataTable();
    Delegate.listenForDeleteDelegate();

})
</script>