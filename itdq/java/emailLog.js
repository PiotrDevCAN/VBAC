
function EmailLog() {

	var table;

	this.init = function(){
	},

	this.initialiseDataTable = function(){
	    // Setup - add a text input to each footer cell
	    $('#emailLogTable tfoot th').each( function () {
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );
		// DataTable
	    EmailLog.table = $('#emailLogTable').DataTable({
	    	ajax: {
	            url: 'ajax/populateEmailLogDatatable.php',
	            data: function ( d ) {
	                d.startDate = $('#START_DATE').val();
	                d.endDate = $('#END_DATE').val();
	            },
	            type: 'POST',
	        }	,
	        order: [[ 0, "desc" ]],
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


//	    ResourceRequest.table.columns([0,1,2,3,4,5,6,7,8,9,10,17,18,20,21,22,23,24,25,26]).visible(false,false);
//	    ResourceRequest.table.columns.adjust().draw(false);



	    // Apply the search
	    EmailLog.table.columns().every( function () {
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

	this.listenForcheckStatus = function(){
		$(document).on('click','.statusCheck', function(e){
			var recordId = $(this).data('reference');
			var url = $(this).data('url');
			var prevStatus = $(this).data('prevstatus');
		    EmailLog.table.clear();
		    EmailLog.table.draw();
		    $('.dataTables_processing', $('#emailLogTable').closest('.dataTables_wrapper')).show();
			$.ajax({
		    	url: "ajax/checkEmailStatus.php",
		        type: 'POST',
		    	data: {recordId:recordId,
		    		   url:url,
		    		   prevStatus:prevStatus},
		    	success: function(result){
		    		EmailLog.table.ajax.reload();
		    	}
		    });
		});
	},

//	this.listenForResendEmail = function(){
//		$(document).on('click','.resendEmail', function(e){
//			var recordId = $(this).data('reference');
//			var url = $(this).data('url');
//		    EmailLog.table.clear();
//		    EmailLog.table.draw();
//		    $('.dataTables_processing', $('#emailLogTable').closest('.dataTables_wrapper')).show();
//			$.ajax({
//		    	url: "ajax/resendEmail.php",
//		        type: 'POST',
//		    	data: {recordId:recordId,
//		    		   url:url
//		    		   },
//		    	success: function(result){
//		    		EmailLog.table.ajax.reload();
//		    	}
//		    });
//		});
//	},


	  this.initialiseDateSelect = function(){

	      $('#InputSTART_DATE').datepicker({ dateFormat: 'dd M yy',
				   altField: '#START_DATE',
				   altFormat: 'yy-mm-dd' ,
				   maxDate: 0,
			       onSelect: function( selectedDate ) {
			            $( "#end_date" ).datepicker( "option", "minDate", selectedDate );}
	      	});

	      var startDate = $('#InputSTART_DATE').datepicker('getDate');

	      $('#InputEND_DATE').datepicker({ dateFormat: 'dd M yy',
				   altField: '#END_DATE',
				   altFormat: 'yy-mm-dd',
				   minDate: startDate,
				   maxDate: 0}
				  );
	  }

}


$( document ).ready(function() {
	var emailLog = new EmailLog();
    emailLog.init();

});




