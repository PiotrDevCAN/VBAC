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
	var xhrPool = []; // to save the ajax calls, so they can be cancelled.

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
	        processing: true,
	        serverSide: true,
	        scrollColapse: false,
	    	searchDelay: 500,
	    	ajax: {
	            url: 'ajax/populateAuditDatatable.php',
	            type: 'POST',
	            dataSrc: function ( json ) {
	            	$('.dataTables_processing').css({"display": "block", "z-index": 10000 })
	            	console.log(xhrPool);
	                //Make your callback here.
	            	if(json.messages.length != 0){
		            	$('#db2ErrorModal .modal-body').html(json.messages);
		            	$('#db2ErrorModal').modal('show');	            		
	            	}  
//	            	$('#auditTable').show();  
	                return json.data;
	            	},
	            beforeSend: function (jqXHR, settings) {	            	
	            	$('.dataTables_processing').css({"display": "block", "z-index": 10000 })
//	            	$('#auditTable').hide();  
	            	$.each(xhrPool, function(idx, jqXHR) {
	            	          jqXHR.abort();
	            	          xhrPool.splice(idx, 1);
	            	});
	                xhrPool.push(jqXHR);
	            	},
	        	},
	        language: {
	                    searchPlaceholder: "Search ALL fields - Very slow",
	                    emptyTable: "No records found"
	        },
	    	autoWidth: true,
	        responsive: false,
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
//	      	    	$('.dataTables_processing').html("<p>Searching for " + val + " in Column " + col + ".......</p><i id='processingIcon' class='fa fa-cog fa-spin fa-2x'></i>").show();
	      	    	auditRecord.table.columns(col).search( val ).draw();
	      	    },
	       	    500
	    );
	    
	    // Apply the search
	    auditRecord.table.columns().every( function () {
	        $( 'input', this.footer() ).on( 'keyup change', function () {
	        	var id = $(this).attr('id');
	        	var column = id.substr(9);
        		searchAt( this.value,column );
	        });
	    });
	    
//	    auditRecord.table.on( 'search.dt', function () {
//	    	console.log('caught search.dt');
//	    	$('#auditTableMasterDiv').hide();  
//	    	console.log($('.dataTables_processing'));
//	    } );
	    
//	    auditRecord.table.on( 'processing.dt', function ( e, settings, processing ) {
//	    	console.log('caught processing.dt');
//	    	console.log(processing);
//	    	console.log($('#processingIndicator'));
//	        $('#processingIndicator').css( 'display', processing ? 'block' : 'none' );
//	    } )
	    
	    
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
	var xhrPool = []; // to save the ajax calls, so they can be cancelled.
	var audit = new auditRecord();
    audit.init();
});