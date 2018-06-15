/*
 *
 *
 *
 */


function auditRecord() {

	var table;
	var dataTableElements;
	var currentXmlDoc;
	var spinner =  '<div id="overlay"><i class="fa fa-spinner fa-spin spin-big"></i></div>';

	this.init = function(){
		console.log('+++ Function +++ auditRecord.init');
		console.log('--- Function --- auditRecord.init');
	},


	this.initialiseAuditTable = function(){
	    $.ajax({
	    	url: "ajax/createHtmlForAuditTable.php",
	    	type: 'POST',
	    	success: function(result){
	    		var Audit = new auditRecord();
	    		$('#auditDatabaseDiv').html(result);
	    		Audit.initialiseDataTable();
	    	}
	    });

	},

	this.initialiseDataTable = function(){
	    // Setup - add a text input to each footer cell
		var columnNumber = 1;
	    $('#auditTable tfoot th').each( function () {	    	
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'"  id="dtSearch_' + columnNumber++ + '"  />');
	    } );
		// DataTable
	    auditRecord.table = $('#auditTable').DataTable({
	    	serverSide: true,
	    	searchDelay: 1500,
	    	ajax: {
	            url: 'ajax/populateAuditDatatable.php',
	            type: 'POST',
	            dataSrc: function ( json ) {
	                //Make your callback here.
	            	console.log(json.messages.length);
	            	console.log(json.messages.length != 0);
	            	console.log(json.messages.length != '0' ) ;
	            	console.log(json.messages) ;
	            	if(json.messages.length != 0){
		            	$('#db2ErrorModal .modal-body').html(json.messages);
		            	$('#db2ErrorModal').modal('show');	            		
	            	}
	                return json.data;
	            }             	
	        }	,
	        order: [[ 0, "desc" ]],
	    	autoWidth: true,
	    	processing: true,
	    	dom: 'Blfrtip',
	        buttons: [
	                  'colvis',
	                  'excelHtml5',
	                  'csvHtml5',
	                  'print'
	              ],
	    });
	    
	    var searchAt = $.fn.dataTable.util.throttle(
	      	    function ( val, col ) {
	        	auditRecord.table.columns(col).search( val ).draw();
	      	    },
	       	    400
	    );
	    
	    // Apply the search
	    auditRecord.table.columns().every( function () {
	        $( 'input', this.footer() ).on( 'keyup change', function () {
	        	var id = $(this).attr('id');
	        	var column = id.substr(9);
        		searchAt( this.value,column );
	        });
	    });
	},
	        

//	        $( 'input', this.footer() ).on( 'keyup change', function () {
//	            if ( that.search() !== this.value ) {
//	                that
//	                    .search( this.value )
//	                    .draw();
//	            }
//	        } );


	this.initialiseRevalidationAuditTable = function(){
		console.log('initialiseRevalidationAuditTable');
	    $.ajax({
	    	url: "ajax/createHtmlForAuditTable.php",
	    	type: 'POST',
	    	success: function(result){
	    		var Audit = new auditRecord();
	    		$('#revalidationAuditDiv').html(result);
	    		Audit.initialiseRevalidationTable();
	    	}
	    });

	},

	this.initialiseRevalidationTable = function(){
	    // Setup - add a text input to each footer cell
	    $('#auditTable tfoot th').each( function () {
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );
		// DataTable
	    auditRecord.table = $('#auditTable').DataTable({
	    	ajax: {
	            url: 'ajax/populateAuditDatatable.php',
	            data : { type : 'revalidation'},
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

	    // Apply the search
	    auditRecord.table.columns().every( function () {
	        var that = this;

	        $( 'input', this.footer() ).on( 'keyup change', function () {
	            if ( that.search() !== this.value ) {
	                that
	                    .search( this.value )
	                    .draw();
	            }
	        } );
	    } );


	}

}

$( document ).ready(function() {
	var audit = new auditRecord();
    audit.init();
});