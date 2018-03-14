/**
 *
 */


function assetPortal() {
	var table;
	var varbRequestTable;

  this.init = function(){
    console.log('+++ Function +++ assetPortal.init');
    console.log('--- Function --- assetPortal.init');
  },

  this.initialiseAssetRequestPortal = function(){
      $.ajax({
	        url: "ajax/createHtmlForAssetPortal.php",
	        type: 'GET',
	        success: function(result){
	          var AssetPortal = new assetPortal();
	          $('#assetRequestsDatatablesDiv').html(result);
	          AssetPortal.initialiseAssetRequestDataTable();
	        }
      });
  },
  
  
  this.initialiseAssetRequestDataTable = function(showAll){  
	showAll = typeof(showAll) == 'undefined'  ? false : showAll;
	  
	// Setup - add a text input to each footer cell
    $('#assetPortalTable tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input type="text" id="footer'+ title + '" placeholder="Search '+title+'" />' );
      } );
    // DataTable
    assetPortal.table = $('#assetPortalTable').DataTable({
        ajax: {
              url: 'ajax/populateAssetRequestPortal.php',
              type: 'POST',
              data: {showAll:showAll}
          }	,
          columns: [
                      { "data": "REFERENCE" , "defaultContent": "" },
                      { "data": "CT_ID" ,"defaultContent": "<i>no ctid</i>" },
                      { "data": "PERSON" ,"defaultContent": "" },
                      { "data": "ASSET","defaultContent": "<i>unknown</i>"},
                      { "data": "STATUS", "defaultContent": "" },
                      { "data": "JUSTIFICATION", "defaultContent": "" },
                      { "data": "REQUESTOR", "defaultContent": "" },
                      { "data": "APPROVER", "defaultContent": "" },                                           
                      { "data": "USER_LOCATION", "defaultContent": "<i>unknown</i>" },
                      { "data": "PRIMARY_UID", "defaultContent": "<i>unknown</i>" },
                      { "data": "SECONDARY_UID", "defaultContent": "<i>unknown</i>" },
                      { "data": "DATE_ISSUED_TO_IBM", "defaultContent": "<i>unknown</i>" },
                      { "data": "DATE_ISSUED_TO_USER", "defaultContent": "" },
                      { "data": "DATE_RETURNED", "defaultContent": "" },
                      { "data": "EDUCATION_CONFIRMATION", "defaultContent": "" },                  
                      { "data": "ORDERIT_VARB_REF", "defaultContent": ""},
                      { "data": "ORDERIT_NUMBER", "defaultContent": "" },
                      { "data": "ORDERIT_STATUS" , "defaultContent": ""},
                      { "data": "ORDERIT_TYPE" , "defaultContent": ""}
                  ],
          columnDefs: [
                         { visible: false, targets: [8,9,10,11,12,13,14,15,16,17,18] }
                  ] ,
          order: [[ 5, "asc" ]],
          autoWidth: true,
          deferRender: true,
          responsive: false,
          // scrollX: true,
          processing: true,
          responsive: true,
          language: {
        	    "emptyTable": "No Asset Requests to show"
          		},   
          dom: 'Blfrtip',
//	      colReorder: true,
          buttons: [
                    'colvis',
                    'excelHtml5',
                    'csvHtml5',
                    'print'
                ],
      });
      // Apply the search
      assetPortal.table.columns().every( function () {
          var that = this;

          $( 'input', this.footer() ).on( 'keyup change', function () {
              if ( that.search() !== this.value ) {
                  that
                      .search( this.value )
                      .draw();
              }
          });
     });
  },
  
  this.listenForReportReset = function(){
	    $(document).on('click','#reportReset', function(e){
	    	$('#portalTitle').text('Asset Request Portal');
	    	$.fn.dataTableExt.afnFiltering.pop();
	    	assetPortal.table.columns().visible(false,false);
	    	assetPortal.table.columns([0,1,2,3,4,5,6,7]).visible(true);
	    	assetPortal.table.search('').order([0,"asc"]).draw();
	    });
  },  
  
  this.listenForReportReload = function(){
	    $(document).on('click','#reportReload', function(e){
	    	$('#portalTitle').text('Asset Request Portal');
	    	$.fn.dataTableExt.afnFiltering.pop();
	    	assetPortal.table.ajax.reload();
	      });
  },
  
  this.listenForReportShowAll = function(){
	  $(document).on('click','#reportShowAll', function(e){
		  assetPortal.table.destroy();
		  AssetPortal.initialiseAssetRequestDataTable(true);
		  $('#portalTitle').text('Asset Request Portal - Show All');
	  });
},

	this.listenForReportShowExportable = function(){
		$(document).on('click','#reportShowExportable', function(e){
			assetPortal.table.destroy();
			AssetPortal.initialiseAssetRequestDataTable(false);
			$('#portalTitle').text('Asset Request Portal - Show Exportable Requests');
	  });
},
  
  
  this.listenForExportButton = function(){
	  $(document).on('click','#exportForOrderIt', function(e){
		  $('#exportForOrderIt').addClass('spinning');
		  $('#exportForOrderIt').attr('disabled',true);
	      $.ajax({
		        url: "ajax/exportForOrderIt.php",
		        type: 'GET',
		        success: function(result){
		        	console.log(result);
		        	var resultObj = JSON.parse(result);
			    	assetPortal.table.ajax.reload();
		        	$('#exportResultsModal .modal-body').html(resultObj.messages);
		        	$('#exportResultsModal').modal('show');
		  		    $('#exportForOrderIt').removeClass('spinning');
				    $('#exportForOrderIt').attr('disabled',false);
		        }
	      });		 
	  });
  }
  
  this.listenForMapVarbButton = function(){
	  $(document).on('click','#mapVarbToOrderIt', function(e){
		  $('#mapVarbToOrderIt').addClass('spinning');
		  $('#mapVarbToOrderIt').attr('disabled',true);
	      $.ajax({
		        url: "ajax/prepareForMapVarbToOrderIT.php",
		        type: 'GET',
		        success: function(result){
		        	var resultObj = JSON.parse(result);
			    	// assetPortal.table.ajax.reload();
		        	$('#mapVarbToOrderItModal .modal-body').html(resultObj.form);
		        	$('#mapVarbToOrderItModal').modal('show');
		  		    $('#mapVarbToOrderIt').removeClass('spinning');
				    $('#mapVarbToOrderIt').attr('disabled',false);
		        }
	      });		 
	  });
  }
  
  this.listenForSaveMapping = function(){
	  $(document).on('click','#saveMapVarbToOrderIT', function(e){
		  
		 
		  
		  
		  var formData = $('#mapVarbToOrderItForm').serialize();		  
	      $.ajax({
		        url: "ajax/saveVarbToOrderItMapping.php",
		        type: 'POST',
		        data: formData,  
		        success: function(result){
		        	console.log(result);
		        	var resultObj = JSON.parse(result);
		        	$('#mapVarbToOrderItModal .modal-body').html('');
		        	$('#mapVarbToOrderItModal').modal('hide');

		        }
	      });
		  
		  
	  });
},
  
  
  
  
  this.listenForMapVarbModalShown = function(){
	  $('#mapVarbToOrderItModal').on('shown.bs.modal',function(){
			$('#unmappedVarb').select2({
		         placeholder:"Select VARB Reference",
		         });
			AssetPortal.populateRequestTableForVarb();
			AssetPortal.listenForVarbSelectedForMapping();
		  
	  });
  }
  
  
  this.listenForVarbSelectedForMapping = function(){
	  console.log('setup listener');
	  $('#unmappedVarb').off('select2:select.varb').on('select2:select.varb', function (e) {
		  assetPortal.varbRequestTable.ajax.reload();
		});
  }
  
  this.populateRequestTableForVarb = function(){ 
	  assetPortal.varbRequestTable = $('#requestsWithinVarb').DataTable({
	          ajax: {
	              url: 'ajax/populateRequestTableForVarb.php',
	              type: 'POST',
	              data: function ( d ) {
	            	  var varb = $('#unmappedVarb').find(':selected').val();
	            	  var varbObject = { 'varb' : varb };
	            	  return varbObject; }	        		
	          		}	,
	          columns: [
	                      { "data": "INCLUDED" , "defaultContent": "", "width":"10%" },
	                      { "data": "REFERENCE" ,"defaultContent": "", "width":"10%" },
	                      { "data": "PERSON" ,"defaultContent": "", "width":"40%" },
	                      { "data": "ASSET","defaultContent": "", "width":"40%"}
	                  ],
	
	          autoWidth: false,
	          deferRender: true,
	          responsive: false,
	          processing: true,
	          responsive: true,
	          pageLength: 20,
	          order: [[ 1, "asc" ]],
	          language: {
	        	    "emptyTable": "Please select VARB"
	          		},   
	          dom: 'Bfrtip',
//		      colReorder: true,
	          buttons: [
	                    'csvHtml5'
	                ],
	      });  
  	}
};


var AssetPortal = new assetPortal();

$( document ).ready(function() {
	AssetPortal = new assetPortal();
	AssetPortal.init();
});