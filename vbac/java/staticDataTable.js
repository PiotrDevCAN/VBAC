/*
 *
 *
 *
 */

function staticDataTable() {

	var table;

	this.init = function(){
		console.log('+++ Function +++ staticDataTable.init');
		console.log('--- Function --- staticDataTable.init');
	},


	this.initialiseDataTable = function(){

	    $('#staticDataValues tfoot th').each( function () {
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );

	    this.table = $('#staticDataValues').DataTable({
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
//	                  'colvis',
	                  'excelHtml5',
	                  'csvHtml5',
	                  'print'
	              ]
	    });

		// Apply the search
		this.table.columns().every( function () {
		    var that = this;
		    $( 'input', this.footer() ).on( 'keyup change', function () {
	        	if ( that.search() !== this.value ) {
		            that
	                	.search( this.value )
	                	.draw();
	        	}
	    	} );
		} );

	},


	this.listenForEditRecord = function(){
		$(document).on('click','.editRecord',function(){
			var tablename = $(this).data('tablename');
			var value = $(this).data('value');
			var uid = $(this).data('uid');
			$('#originalValue').val(value);
			$('#amendedValue').val('');
			$('#amendTable').val(tablename);
			$('#amendUid').val(uid);
			$('#amendStaticDataModal .modal-title').text('Amend Static Data Entry');
			$('#amendedLabel').text('Amended Value');
			$('.originalValueRow').show();
			$('#amendStaticDataModal').modal('show')
		});

	},

	this.listenForNewEntry = function(){
		$(document).on('click','.newEntry',function(){
			var tablename = $(this).data('tablename');
			var value = $(this).data('value');
			var uid = $(this).data('uid');
			$('#originalValue').val('newEntry');
			$('#amendedValue').val('');
			$('#amendTable').val(tablename);
			$('#amendUid').val(uid);

			$('#amendStaticDataModal .modal-title').text('Create New Entry');
			$('#amendedLabel').text('New Value');
			$('.originalValueRow').hide();

			$('#amendStaticDataModal').modal('show')
		});
	},

	this.listenForSaveAmendedStaticData = function(){
		$(document).on('click','#saveAmendedStaticData',function(e){
			var tables = $('.dataTable').DataTable();
			var table = tables.table('#staticDataValues');
			var formData = $('#manageStaticDataForm').serialize();
			$.ajax({
			   	url: "ajax/manageStaticDataTables.php",
			    type: 'POST',
			   	data: formData,
			  	success: function(result){
			   		console.log(result);
					$('#amendStaticDataModal').modal('hide');
					table.ajax.reload();
			   		}
			});
		});
	},

	this.listenForSelectGroupsForRoles = function(){
		$('#selectGroupsForRoles').on('click',function(){
			$('#editStaticDataTables').removeClass('active').hide();
			$('#editGroupsForRoles').addClass('active').show();

		});
	},

	this.listenForSelectStaticData = function(){
		$('#selectGroupsForRoles').on('click',function(){
			$('#editStaticDataTables').removeClass('active').hide();
			$('#editGroupsForRoles').addClass('active').show();
		});
	}

}


$( document ).ready(function() {
	var StaticDataTable = new staticDataTable();
    StaticDataTable.init();
});