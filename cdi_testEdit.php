<?php


?>

<div class='container'>

<table class='table table-responsive table-bordered'id='test' width='100%'>
<thead>
<tr class='fieldNames'><th>Surname</th><th>Name</th><th>Employer</th>
</tr>
</thead>
<tbody>
<tr><td>Daniel</td><td class='editable success' data-key='Daniel' data-field='name' contenteditable="true">Rob</td><td >IBM</td></tr>
<tr><td>Smith</td><td class='editable success' data-key='Smith' data-field='name'  contenteditable="true">Peter</td><td >Microsoft</td></tr>
</tbody>
</table>

</div>

<style>
.editable {
 background-color:lightGrey;
 }

</style>


<script>
function underline(s) {
    var arr = s.split('');
    s = arr.join('\u0332');
    if (s) s = s + '\u0332';
    return s;
}



$(document).ready(function(){
	$('.editable').focusout(function(e){
		console.log(this);
		console.log($(this).text());
		var key = $(this).data('key');
		var field = $(this).data('field');
		var value = $(this).text();
		$(this).removeClass('success').addClass('warning');

		alert('Record :'+ underline(key) + ' Field:' + underline(field) + ' Value:' + underline(value));
	});
});



</script>