<?php


$allRoles = array('001'=>'DBA','002'=>'Storage Analyst','003'=>'Sys prog','004'=>'Service Mgr');
$allGroups = array('001'=>'Group1','002'=>'GroupTwo');
$allTables = array('Role Table'=> $allRoles, 'Groups'=>$allGroups);

?>
<div class='container'>

<ul class="nav nav-tabs">
  <li role="presentation" class="active" id='selectStaticData' ><a>Static Data</a></li>
  <li role="presentation"                id='selectGroupsForRoles'><a >Groups for Role</a></li>
  <li role="presentation"                id='selectGroupDetails'><a >Group Details</a></li>
</ul>




<div id='editStaticDataTables'>
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Manage Static Data Tables</h3>
  </div>
  <div class="panel-body">
  <div class='table-responsive'>
<table class="table table-striped table-bordered" cellspacing="0" width="50%" id='staticDataValues'>
<thead><tr><th>Table Name</th><th>Entry</th></tr></thead>
<tbody>
</tbody>
<tfoot>
<tr><th>Table Name</th><th>Entry</th></tr>
</tfoot>
</table>
</div>
</div>
</div>
</div>



<div id='editGroupsForRoles' hidden >
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Manage Groups for Roles</h3>
  </div>
  <div class="panel-body">
</div>
</div>
</div>







<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Amend Static Data Entry</h4>
      </div>
      <div class="modal-body" >
        <div class='row originalValueRow'>
        	<div class='form-group' >
            	<label for='amendedValue' class='col-md-3 control-label ceta-label-left'>Original Value</label>
           		<div class='col-sm-6'>
           		<input class='form-control' id='originalValue' name='originalValue' type='text' disabled >
           		</div>
           </div>
       </div>
       <div class='row'>
        <div class='form-group' >
       		<label for='amendedValue' class='col-md-3 control-label ceta-label-left' id='amendedLabel'>Amended Value</label>
        	<div class='col-sm-6'>
           		<input class='form-control' id='amendedValue' name='amendedValue' type='text' >
           		<input id='amendTable' name='amendTable' type='hidden' >
           		<input id='amendUid' name='amendUid' type='hidden' >
           </div>
      </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id='saveAmendedStaticData'>Save</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>




<script type="text/javascript">
$('body').on('click','.editRecord',function(){
	var tablename = $(this).data('tablename');
	var value = $(this).data('value');
	var uid = $(this).data('uid');
	$('#originalValue').val(value);
	$('#amendedValue').val('');
	$('#amendTable').val(tablename);
	$('#amendUid').val(uid);
	$('#myModal .modal-title').text('Amend Static Data Entry');
	$('#amendedLabel').text('Amended Value');
	$('.originalValueRow').show();
	$('#myModal').modal('show')
});

$('body').on('click','.newEntry',function(){
	var tablename = $(this).data('tablename');
	var value = $(this).data('value');
	var uid = $(this).data('uid');
	$('#originalValue').val('newEntry');
	$('#amendedValue').val('');
	$('#amendTable').val(tablename);
	$('#amendUid').val(uid);

	$('#myModal .modal-title').text('Create New Entry');
	$('#amendedLabel').text('New Value');
	$('.originalValueRow').hide();

	$('#myModal').modal('show')
});



$('document').ready(function() {

	$('#selectGroupsForRoles').on('click',function(){
		$('#editStaticDataTables').removeClass('active').hide();
		$('#editGroupsForRoles').addClass('active').show();

	});

	$('#selectStaticData').on('click',function(){
		$('#editStaticDataTables').addClass('active').show();
		$('#editGroupsForRoles').removeClass('active').hide();
	});


    $('#staticDataValues tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
    } );



    var table = $('#staticDataValues').DataTable({
    	ajax: {
            url: 'ajax/returnStaticDataForEdit.php',
            type: 'POST',
        }	,
    	responsive: true,
    	processing: true,
    	deferRender:true,
    	colReorder: true,
    	dom: 'Blfrtip',
        buttons: [
                  'colvis',
                  'excelHtml5',
                  'csvHtml5',
                  'print'
              ]
    });

// Apply the search
table.columns().every( function () {
    var that = this;
    $( 'input', this.footer() ).on( 'keyup change', function () {
        if ( that.search() !== this.value ) {
            that
                .search( this.value )
                .draw();
        }
    } );
} );

});


</script>