
function EmailLog() {

	var table;

	this.init = function(){
		console.log('+++ Function +++ EmailLog.init');
		console.log('--- Function --- EmailLog.init');

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
			var recordId = $(e.target).data('reference');
			var url = $(e.target).data('url');
			var prevStatus = $(e.target).data('prevstatus');
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

	this.listenForResendEmail = function(){
		$(document).on('click','.resendEmail', function(e){
			var recordId = $(e.target).data('reference');
			var url = $(e.target).data('url');
		    EmailLog.table.clear();
		    EmailLog.table.draw();
		    $('.dataTables_processing', $('#emailLogTable').closest('.dataTables_wrapper')).show();
			$.ajax({
		    	url: "ajax/resendEmail.php",
		        type: 'POST',
		    	data: {recordId:recordId,
		    		   url:url
		    		   },
		    	success: function(result){
		    		EmailLog.table.ajax.reload();
		    	}
		    });
		});
	},


	this.initialiseDateSelect = function(){
		var startDate,
		endDate,

		startPicker = new Pikaday({
			firstDay:1,
			disableDayFn: function(date){
			    // Disable all but Monday
			    return date.getDay() === 0 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 5 || date.getDay() === 6;
			},
			field: document.getElementById('InputSTART_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			onSelect: function() {
				console.log(this.getMoment().format('Do MMMM YYYY'));
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				console.log(db2Value);
				jQuery('#START_DATE').val(db2Value);
				startDate = this.getDate();
				console.log(startDate);
				updateStartDate();
			}
		}),
		endPicker = new Pikaday({
			firstDay:1,
			disableDayFn: function(date){
				// Disable all but Monday
				return date.getDay() === 0 || date.getDay() === 1 || date.getDay() === 2 || date.getDay() === 3 || date.getDay() === 4 || date.getDay() === 6;
			},
			field: document.getElementById('InputEND_DATE'),
			format: 'D MMM YYYY',
			showTime: false,
			onSelect: function() {
				var db2Value = this.getMoment().format('YYYY-MM-DD')
				jQuery('#END_DATE').val(db2Value);
				endDate = this.getDate();
				updateEndDate();
			}
		}),

		updateStartDate = function() {
			console.log('updatedStartDate');
		    startPicker.setStartRange(startDate);
		    endPicker.setStartRange(startDate);
		    endPicker.setMinDate(startDate);
		    console.log($('#START_DATE').val());
		    EmailLog.table.clear();
		    EmailLog.table.draw();
		    EmailLog.table.ajax.reload();
		},

		updateEndDate = function() {
			console.log('updatedEndDate');
		    startPicker.setEndRange(endDate);
		    startPicker.setMaxDate(endDate);
		    endPicker.setEndRange(endDate);
		    EmailLog.table.clear();
		    EmailLog.table.draw();
		    EmailLog.table.ajax.reload();

		},


	_startDate = startPicker.getDate(),
	_endDate = endPicker.getDate();

	if (_startDate) {
	    startDate = _startDate;
	    this.updateStartDate();
	}

	if (_endDate) {
	    endDate = _endDate;
	    this.updateEndDate();
	}


	}



}


$( document ).ready(function() {
	var emailLog = new EmailLog();
    emailLog.init();

});




