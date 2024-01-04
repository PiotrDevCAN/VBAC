<?php

use itdq\Trace;

?>
<div class='container'>
<h2>Worker API lookup</h2>
<?php

Trace::pageOpening($_SERVER['PHP_SELF']);
?>

<form id='workerAPIlookupForm' class="form-horizontal"  method='post'>
	<div class="form-group ">
		<div class='required'>
			<label for="EMAIL_ADDRESS" class="col-md-2 control-label ceta-label-left" data-toggle="tooltip" data-placement="top" title="Email Address">Email Address</label>
			<div class="col-md-6">
				<input class="form-control typeahead tt-input" id="EMAIL_ADDRESS" name="EMAIL_ADDRESS" value="" placeholder="Enter Email Address" required="required" type="text" >
			</div>
		</div>
	</div>
	
	<div class="alert alert-success hide" role="alert" id='activeAlert'>
		Employee has <b>an active</b> status.
	</div>
	<div class="alert alert-danger hide" role="alert" id='notActiveAlert'>
		Employee has <b>a non-active</b> status.
	</div>
	<div class="alert alert-info hide" role="alert" id='managerAlert'>
		Employee is <b>a manager</b>.
	</div>
	<div class="alert alert-info hide" role="alert" id='notManagerAlert'>
		Employee is <b>not a manager</b>.
	</div>
	<div class="alert alert-warning hide" role="alert" id='emptyCNUMAlert'>
		Employee does not have <b>CNUM</b> value.
	</div>
	<div class="alert alert-warning hide" role="alert" id='emptyWorkerIdAlert'>
		Employee does not have <b>Worker Id</b> value.
	</div>
	<div class="alert alert-warning hide" role="alert" id='emptyEmailAlert'>
		Employee does not have <b>Email Address</b> value.
	</div>
</form>

</div>

<?php
Trace::pageLoadComplete($_SERVER['PHP_SELF']);