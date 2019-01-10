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
	            	console.log('dataSrc');
	            	console.log(json);
	            	console.log($('#auditTable_processing').is(":visible"));  
	                //Make your callback here.
	            	if(json.messages.length != 0){
		            	$('#db2ErrorModal .modal-body').html(json.messages);
		            	$('#db2ErrorModal').modal('show');	            		
	            	}  
	            	console.log(json.data);
	                return json.data;
	            	},
	            beforeSend: function (jqXHR, settings) {	
	            	console.log('before send');
	             	console.log($('.dataTables_processing'));
	             	console.log($('#auditTable_processing').is(":visible")); 
	            	
	            	$.each(xhrPool, function(idx, jqXHR) {
	            	          jqXHR.abort();  // basically, cancel any existing request, so this one is the only one running
	            	          xhrPool.splice(idx, 1);
	            	});
	                xhrPool.push(jqXHR);
	            	},
	        	},
	        language: {
	                    searchPlaceholder: "Search ALL fields - Very slow",
	                    emptyTable: "No records found",
	                    processing: "Processing<i class='fas fa-spinner fa-spin '></i>"
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
	    
	    auditRecord.table.on( 'processing.dt', function ( e, settings, processing ) {
	    	var processing =( xhrPool[0].readyState!=4 );
	    	if(processing){
	    		$('#auditTable_processing').show(); 
	    		$('tbody').hide();
	    	} else {
	    		$('#auditTable_processing').hide(); 
	    		$('tbody').show();
	    	}
	    	
	    } );
	    
	    
	},
	        
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
	            
	            data: function ( d ) {
	            	console.log(d);
	                d.type = "Revalidation";
	            },
	            dataSrc: function ( json ) {
	            	console.log('dataSrc');
	            	console.log(json);
	            	console.log($('#auditTable_processing').is(":visible"));  
	                //Make your callback here.
	            	if(json.messages.length != 0){
		            	$('#db2ErrorModal .modal-body').html(json.messages);
		            	$('#db2ErrorModal').modal('show');	            		
	            	}  
	            	console.log(json.data);
	                return json.data;
	            	},
	            beforeSend: function (jqXHR, settings) {	
	            	console.log('before send');
	             	console.log($('.dataTables_processing'));
	             	console.log($('#auditTable_processing').is(":visible")); 
	            	
	            	$.each(xhrPool, function(idx, jqXHR) {
	            	          jqXHR.abort();  // basically, cancel any existing request, so this one is the only one running
	            	          xhrPool.splice(idx, 1);
	            	});
	                xhrPool.push(jqXHR);
	            	},
	        	},
	        language: {
	                    searchPlaceholder: "Search ALL fields - Very slow",
	                    emptyTable: "No records found",
	                    processing: "Processing<i class='fas fa-spinner fa-spin '></i>"
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
	    
	    auditRecord.table.on( 'processing.dt', function ( e, settings, processing ) {
	    	var processing =( xhrPool[0].readyState!=4 );
	    	if(processing){
	    		$('#auditTable_processing').show(); 
	    		$('tbody').hide();
	    	} else {
	    		$('#auditTable_processing').hide(); 
	    		$('tbody').show();
	    	}
	    	
	    } );
	    



	}

}

$( document ).ready(function() {
	var xhrPool = []; // to save the ajax calls, so they can be cancelled.
	var audit = new auditRecord();
    audit.init();
});