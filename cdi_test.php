<?php
use vbac\allTables;

?>

<form id='boardingForm'  class="form-horizontal" onsubmit="return false;">
<div class="form-group">
<div class="col-sm-6">
<input class="form-control typeahead" id="person_name" name="person_name"
	              			   value="hello"
	              			   type="text" placeholder='Start typing name/serial/email' 
	              			   data-name='robdaniel'
	              			   >
<button type="button" class="btn btn-success" id='test' data-name='alanJones'><span class='glyphicon glyphicon-edit ' aria-hidden='true'></span>Test</button>              			   
	                           
</div>
</div>
</form>

<script>

	  $(document).on('click','#test', function(e){
		  console.log(e);
		  console.log($(e));

		  console.log(this);

		  console.log($(this));
		  console.log($(this).data('name'));
		  


		  

		  console.log($(e).parent());
		  
		  console.log($(this));
		  console.log($(this).data('name'));
		  var name = $(this).data('name');
		  $('#person_name').val(name);

		  console.log($('#person_name').data('name'));
		  console.log($('#person_name').val());

	  });



</script>


