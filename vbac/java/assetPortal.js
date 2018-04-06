/**
 *
 */


function assetPortal() {
	var table;
	var varbRequestTable;
	var requestsWithStatus;

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
                      { "data": "REFERENCE" , "defaultContent": "",
                    	  render: {
                  	        _: 'display',
                  	        sort: 'reference'
                  	    }                     
                      
                      },
                      { "data": "CT_ID" ,"defaultContent": "<i>no ctid</i>" },
                      { "data": "PERSON" ,"defaultContent": "" },
                      { "data": "ASSET","defaultContent": "<i>unknown</i>"},
                      { "data": "STATUS", "defaultContent": "" },
                      { "data": "JUSTIFICATION", "defaultContent": "" },                   
                      { "data": "REQUESTOR", "defaultContent": "",
                    	  render: {
                    	        _: 'display',
                    	        sort: 'timestamp'
                    	    }
                      },
                      { "data": "APPROVER", "defaultContent": "" },                                           
                      { "data": "LOCATION", "defaultContent": "<i>unknown</i>" },
                      { "data": "PRIMARY_UID", "defaultContent": "<i>unknown</i>" },
                      { "data": "SECONDARY_UID", "defaultContent": "<i>unknown</i>" },
                      { "data": "DATE_ISSUED_TO_IBM", "defaultContent": "<i>unknown</i>" },
                      { "data": "DATE_ISSUED_TO_USER", "defaultContent": "" },
                      { "data": "DATE_RETURNED", "defaultContent": "" },                 
                      { "data": "ORDERIT_VARB_REF", "defaultContent": ""},
                      { "data": "ORDERIT_NUMBER", "defaultContent": "" },
                      { "data": "ORDERIT_STATUS" , "defaultContent": ""},
                      { "data": "ORDERIT_TYPE" , "defaultContent": ""},
                      { "data": "COMMENT" , "defaultContent": ""}
                  ],
          columnDefs: [
                         { visible: false, targets: [8,9,10,11,12,13,14,15,16,17,18] }
                  ] ,
          order: [[ 18, "asc" ]],
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

this.listenForReportShowUid = function(){
	$(document).on('click','#reportShowUid', function(e){
    	$('#portalTitle').text('Asset Request Portal - Show UID' );
    	$.fn.dataTableExt.afnFiltering.pop();
    	
		assetPortal.table.destroy();
		AssetPortal.initialiseAssetRequestDataTable(true);
    	
    	
    	assetPortal.table.columns().visible(false,false);
    	assetPortal.table.columns([0,1,2,3,9,10]).visible(true);
    	assetPortal.table.search('').order([0,"desc"]).draw();
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
  },
  
  
  this.listenForEditUid = function(){
	  $(document).on('click','.btnEditUid', function(e){
		  $('#asset').val($(e.target).data('asset'));
		  $('#userid').val($(e.target).data('requestee'));		  
		  
		  var primaryTitle = $(e.target).data('primarytitle');
		  $('#primaryLabel').text(primaryTitle);
		  $('#primaryUid').attr('placeholder',primaryTitle);
		  
		  var secondaryTitle = $(e.target).data('secondarytitle');
		  if(secondaryTitle) {
			 $('#secondaryLabel').text(secondaryTitle);
			 $('#secondaryUid').attr('placeholder',secondaryTitle);
			 $('#secondaryUidFormGroup').show(); 
		  } else {
			 $('#secondaryLabel').text('null');
			 $('#secondaryUid').attr('placeholder','null');
			 $('#secondaryUidFormGroup').hide(); 			  
		  }
		 
		  $('#reference').val($(e.target).data('reference')); 
		  $('#primaryUid').val('');
		  $('#secondaryUid').val('');
		  $('#editUidModal').modal('show');
		 
	 
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
  
  
  this.listenForSetOitStatusButton = function(){
	  $(document).on('click','#setOrderItStatus', function(e){
		  $('#setOrderItStatus').addClass('spinning');
		  $('#setOrderItStatus').attr('disabled',true);
	      $.ajax({
		        url: "ajax/prepareForSetOrderItStatus.php",
		        type: 'GET',
		        success: function(result){
		        	var resultObj = JSON.parse(result);
			    	// assetPortal.table.ajax.reload();
		        	$('#setOitStatusModal .modal-body').html(resultObj.form);
		        	$('#setOitStatusModal').modal('show');
		  		    $('#setOrderItStatus').removeClass('spinning');
				    $('#setOrderItStatus').attr('disabled',false);
		        }
	      });		 
	  });
  } 
  
  
  this.listenForSetOitStatusModalShown = function(){
	  $('#setOitStatusModal').on('shown.bs.modal',function(){
			$('#orderit').select2({
		         placeholder:"Select Order IT",
		         });
			AssetPortal.populateRequestTableForOrderIt();
			AssetPortal.listenForOrderItSelected();
		  
	  });
  }
  
  
  this.listenForOrderItSelected = function(){
	  console.log('setup listener for orderit selected');
	  console.log($('#orderit'));
	  $('#orderit').on('select2:select', function (e) {
		  console.log('event triggered');
		  assetPortal.requestsWithStatus.ajax.reload();
		});
  }
  
  
  this.listenForSaveMapping = function(){
	  $(document).on('click','#saveMapVarbToOrderIT', function(e){ 
		  $('#saveMapVarbToOrderIT').addClass('spinning');
	      var form = document.getElementById('mapVarbToOrderItForm');
	      var formValid = form.checkValidity();
	      if(formValid){
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
	      } else {
	    	  alert('Form is not valid, please correct');
	      }	
		  $('#saveMapVarbToOrderIT').removeClass('spinning');
		  assetPortal.table.ajax.reload();
	  });
},


this.listenForSaveEditUid = function(){
	  $(document).on('click','#saveEditUid', function(e){ 
		  $('#saveEditUid').addClass('spinning');
		  var formData = $('#editUidForm').serialize();	
		  console.log(formData);
	      $.ajax({
		        url: "ajax/saveEditUid.php",
		        type: 'POST',
		        data: formData,  
		        success: function(result){
		        	console.log(result);
		        	var resultObj = JSON.parse(result);
		  		    $('#saveEditUid').removeClass('spinning');
		        	$('#editUidModal').modal('hide');	
			    	assetPortal.table.ajax.reload();
		        	
		        }
	      });
		  
		  
	  });
},


this.listenForSaveOrderItStatus = function(){
	  $(document).on('click','#saveOrderItStatus', function(e){ 
		  $('#saveOrderItStatus').addClass('spinning');
	      var form = document.getElementById('setOrderItStatusForm');
	      var formValid = form.checkValidity();
	      if(formValid){
			  var formData = $('#setOrderItStatusForm').serialize();
	    	  $.ajax({
		        url: "ajax/saveOrderItStatus.php",
		        type: 'POST',
		        data: formData,  
		        success: function(result){
		        	console.log(result);
		        	var resultObj = JSON.parse(result);
		        	$('#setOitStatusModal .modal-body').html('');
		        	$('#saveOrderItStatus').removeClass('spinning');
		        	$('#setOitStatusModal').modal('hide');
		  		  	
		  		  	assetPortal.table.ajax.reload();

		        }
	    	  });
	      } else {
	    	  alert('Form is not valid, please correct');
	    	  $('#saveOrderItStatus').removeClass('spinning');
	      }	

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
  
  
  
  
  this.listenForAssetRequestApprove  = function(){
	    $(document).on('click','.btnAssetRequestApprove', function(e){
	    	$('#approveRejectRequestReference').val($(e.target).data('reference'));
	    	$('#approveRejectRequestee').val($(e.target).data('requestee'));    	 	
	    	$('#approveRejectAssetTitle').val($(e.target).data('asset'));   
	    	$('#approveRejectRequestOrderItStatus').val($(e.target).data('orderitstatus'));
	    	$('#assetRequestApprovalToggle').prop('checked',true).change();
	    	
	    	$('#approveRejectRequestComment').val('').attr('required',false);	    	
	    	$('#approveRejectModal').modal('show');
	    });
}, 


this.listenForAssetRequestReject  = function(){
    $(document).on('click','.btnAssetRequestReject', function(e){    	
    	$('#approveRejectRequestReference').val($(e.target).data('reference'));
    	$('#approveRejectRequestee').val($(e.target).data('requestee'));    	 	
    	$('#approveRejectAssetTitle').val($(e.target).data('asset'));   
    	$('#approveRejectRequestOrderItStatus').val($(e.target).data('orderitstatus'));
    	$('#assetRequestApprovalToggle').prop('checked',false).change();
    	
    	console.log($('#assetRequestApprovalToggle'));
    	
    	$('#approveRejectRequestComment').val('').attr('required',true);
    	
    	$('#approveRejectModal').modal('show');    	

    });
},



this.listenForAssetRequestApproveRejectConfirm  = function(){
    $(document).on('click','#assetRequestApproveRejectConfirm', function(e){  
	      var form = document.getElementById('assetRequestApproveRejectForm');
	      var formValid = form.checkValidity();
	      if(formValid){
	    	  $('#approveRejectModal').modal('hide');  
	          	var allDisabledFields = ($("#assetRequestApproveRejectForm input:disabled"));
	          	$(allDisabledFields).attr('disabled',false);	          		
	          	var reference = $('#approveRejectRequestReference').val();
	          	var comment = $('#approveRejectRequestComment').val();
	          	var orderItStatus = $('#approveRejectRequestOrderItStatus').val();
	          	
	          	var approveReject = $('#assetRequestApprovalToggle').is(':checked' );
	          	var raisedInOrderIt = orderItStatus == 'Raised in Order IT' ? true : false;
	          	var status = approveReject ? 'Approved for Order IT' : 'Rejected in vBAC';
	          	var status = approveReject && raisedInOrderIt ? 'Raised in Order IT' : status;
	          	
	          	

		         $(allDisabledFields).attr('disabled',true);
	          	
	          	$.ajax({
			        url: "ajax/updateAssetRequestStatus.php",
			        type: 'POST',
			        data: {reference: reference,
			        	   status : status,
			        	   comment : comment },  
			        success: function(result){
			        	console.log(result);
			        	var resultObj = JSON.parse(result);
			        	$('#approveRejectModal').modal('hide');  
			        	assetPortal.table.ajax.reload();
			        }
		      });
	      	    	  
	      } else {
	    	  alert('Please complete justification');
	      }
    });
},


this.listenForAssetRequestApproveRejectToggle  = function(){
    $(document).off('change.varb').on('change.varb','#assetRequestApprovalToggle', function(e){    
    	var comment = $('#assetRequestApprovalComment');    	
    	comment.prop("required", !comment.prop("required"));
    	
    });
},



this.listenForAssetReturned = function(){
	  $(document).on('click','.btnAssetReturned', function(e){ 
		  
	  
		  $('#assetRet').val($(e.target).data('asset'));
		  $('#useridRet').val($(e.target).data('requestee'));	
		  
		  var primaryTitle = $(e.target).data('primarytitle');
		  if(primaryTitle) {
			  $('#primaryLabelRet').text(primaryTitle);
			  $('#primaryUidRet').attr('placeholder',primaryTitle);
			  $('#primaryUidFormGroupRet').show();
		  } else {
			 $('#primaryLabelRet').text('null');
			 $('#primaryUidRet').attr('placeholder','null');
			 $('#primaryUidFormGroupRet').hide(); 			  
		  }
		  
		  var secondaryTitle = $(e.target).data('secondarytitle');
		  if(secondaryTitle) {
			 $('#secondaryLabelRet').text(secondaryTitle);
			 $('#secondaryUidRet').attr('placeholder',secondaryTitle);
			 $('#secondaryUidFormGroupRet').show(); 
		  } else {
			 $('#secondaryLabelRet').text('null');
			 $('#secondaryUidRet').attr('placeholder','null');
			 $('#secondaryUidFormGroupRet').hide(); 			  
		  }
		 
		  $('#referenceRet').val($(e.target).data('reference')); 
		  $('#confirmAssetReturned').attr('disabled',false);
		  $('#confirmReturnedModal').modal('show');	  		  
	  });
},

this.listenForConfirmedAssetReturnedModalShown = function(){
	$('#confirmReturnedModal').on('shown.bs.modal',function(){
		$('#date_returned').datepicker({ dateFormat: 'dd M yy',
		   altField: '#date_returned_db2',
		   altFormat: 'yy-mm-dd' ,
		   minDate: -365,
		   maxDate: +0,
			});
		var dateReturned = $('#date_returned').datepicker('getDate');
	});
	
},




this.listenForConfirmedAssetReturned = function(){
	  $(document).on('click','#confirmAssetReturned', function(e){ 
		  $('#confirmAssetReturned').addClass('spinning');
		  $('#confirmAssetReturned').attr('disabled',true);
		  var formData = $('#confirmReturnedForm').serialize();			  
		  console.log(formData);
	      $.ajax({
		        url: "ajax/saveAssetReturned.php",
		        type: 'POST',
		        data: formData,  
		        success: function(result){
		        	console.log(result);
		        	var resultObj = JSON.parse(result);
		  		    $('#confirmAssetReturned').removeClass('spinning');
		  		    $('#confirmAssetReturned').attr('disabled',false);
		        	$('#confirmReturnedModal').modal('hide');	
			    	assetPortal.table.ajax.reload();
		        	
		        }
	      });
		  
 
		  
	  });
},

  
  
  
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
	                      { "data": "INCLUDED" , "defaultContent": "", "width":"5%" },
	                      { "data": "REFERENCE" ,"defaultContent": "", "width":"5%" },
	                      { "data": "PERSON" ,"defaultContent": "", "width":"30%" },
	                      { "data": "ASSET","defaultContent": "", "width":"20%"},
	                      { "data": "PRIMARY_UID","defaultContent": "", "width":"20"},
	                      { "data": "SECONDARY_UID","defaultContent": "", "width":"20%"}
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
  	},

  	this.populateRequestTableForOrderIt = function(){ 
	  assetPortal.requestsWithStatus = $('#requestsWithStatus').DataTable({
	          ajax: {
	              url: 'ajax/populateRequestTableForOrderIt.php',
	              type: 'POST',
	              data: function ( d ) {
	            	  var orderit = $('#orderit').find(':selected').val();
	            	  var oitObject = { 'orderit' : orderit };
	            	  return oitObject; }
	              },

	          columns: [
	                      { "data": "REFERENCE" ,"defaultContent": "", "width":"5%" },
	                      { "data": "EMAIL" ,"defaultContent": "", "width":"30%" },
	                      { "data": "ASSET","defaultContent": "", "width":"25%"},
	                      { "data": "ORDERIT_STATUS","defaultContent": "", "width":"25%"},
	                      { "data": "ACTION","defaultContent": "", "width":"20%"}
	                  ],
	                  
	          drawCallback: function(settings) {
	                      console.log($('.statusToggle'));
	                      $('.statusToggle').bootstrapToggle();
	                  },
	          autoWidth: false,
	          deferRender: true,
	          responsive: false,
	          processing: true,
	          responsive: true,
	          pageLength: 20,
	          order: [[ 1, "asc" ]],
	          language: {
	        	    "emptyTable": "Please select Order IT"
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