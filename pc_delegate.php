<?php

use vbac\delegateRecord;

set_time_limit(0);

?>
<div class='container'>
<div class='row'>
<div class='col-sm-3'></div>
<div class='col-sm-4'>
<?php

$delegate = new delegateRecord();
$delegate->displayForm();


?>
</div>
</div>
</div>



<script>

$(document).ready(function() {

	var person = new personRecord();
    $('.select2').select2({
        'placeholder' : 'Select your delegate'

        });



})



</script>





<?php
