<?php
?>
<div class='container'>
<table id='testTable'  class='table table-striped table-bordered compact' cellspacing='0' width='100%'>
<thead><tr><th> Col 1</th><th>Col 2</th><th>Col 3</th></tr></thead>
<tbody>
<tr><td>Data 1a</td><td>Data 2a</td><td>Data 3a</td></tr>
<tr><td>Data 1b</td><td>Data 2b</td><td>Data 3b</td></tr>
<tr><td>Data 1c</td><td>Data 2c</td><td>Data 3c</td></tr>
<tr><td>Data 1d</td><td>Data 2d</td><td>Data 3d</td></tr>
<tr><td>Data 1e</td><td>Data 2e</td><td>Data 3e</td></tr>
<tr><td>Data 1f</td><td>Data 2f</td><td>Data 3f</td></tr>
<tr><td>Data 1g</td><td>Data 2g</td><td>Data 3g</td></tr>
<tr><td>Data 1h</td><td>Data 2h</td><td>Data 3h</td></tr>



</tbody>
<tfoot><tr><th> Col 1</th><th>Col 2</th><th>Col 3</th></tr></tfoot>

</table>
</div>



<script>

var table;

$(document).ready(function(){

console.log('initialise Datatables');

table = $('#testTable').DataTable({
	autoWidth: false,
	deferRender: true,
	responsive: false,
	// scrollX: true,
	processing: true,
	responsive: true,
	colReorder: true,
	dom: 'Blfrtip',
    buttons: [
              'colvis',
              'excelHtml5',
              'csvHtml5',
              'print'
          ],
	});

	var initial = table.columns().visible();

	table.column(1).visible(false);

	var afterInvisible = table.columns().visible();

	table.column(1).visible(true);

	var afterVisible = table.columns().visible();


	console.log(afterInvisible);
	console.log(afterVisible);


	table.on('draw',function(){
		 console.log( 'Redraw occurred at: '+new Date().getTime() );
	});
	location.reload();



	$(afterInvisible).each(function(index){
		console.log('index' + index);
		console.log(this);
		if(this==true){
			console.log('set to true');
			var state = true;
		} else {
			console.log('set to false');
			var state =  false;
		}
		console.log(state);
		table.column(index).visible(state ,false);
	});

	table.columns().draw();

	var afterReset = table.columns().visible();

	console.log(afterReset);



});


</script>