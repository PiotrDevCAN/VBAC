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

  this.countRequestsForPortal = function(){
  	  $('#countPmoForExport').html('**');
	  $('#countNonPmoForExport').html('**');
  	  $('#countBauForExport').html('**');
	  $('#countNonBauExport').html('**');
	  $('#countPmoExported').html('**');
	  $('#countBauRaised').html('**');
	  $('#countNonBauRaised').html('**');

      $.ajax({
	        url: "ajax/countRequestsForPortal.php",
	        type: 'GET',
	        success: function(result){
	        	var resultObj = JSON.parse(result);
	        	$('#countPmoForExport').html(resultObj.pmoForExport);
	        	$('#countNonPmoForExport').html(resultObj.nonPmoForExport);
	        	$('#countBauForExport').html(resultObj.bauForExport);
	        	$('#countNonBauExport').html(resultObj.nonBauForExport);
	      	    $('#countPmoExported').html(resultObj.pmoExported);
	      	    $('#countBauRaised').html(resultObj.bauRaised);
	    	    $('#countNonBauRaised').html(resultObj.nonBauRaised);
	        }
      });
  },


  this.initialiseAssetRequestPortal = function(){
      $.ajax({
	        url: "ajax/createHtmlForAssetPortal.php",
	        type: 'GET',
	        success: function(result){
	          var AssetPortal = new assetPortal();
	          $('#assetRequestsDatatablesDiv').html(result);
	          AssetPortal.initialiseAssetRequestDataTable();
	          AssetPortal.countRequestsForPortal();
	        }
      });
  },


  this.initialiseAssetRequestDataTable = function(showType,pmoRaised){
	showType = typeof(showType) == 'undefined'  ? 'all' : showType;
	pmoRaised = typeof(pmoRaised) == 'undefined'  ? true : pmoRaised;

	// Setup - add a text input to each footer cell
    $('#assetPortalTable thead th').each( function () {
          var title = $(this).text();
          $(this).html( title + '<br/><input type="text" id="footer'+ title + '" placeholder="Search '+title+'" />' );
      } );
    // DataTable
    assetPortal.table = $('#assetPortalTable').DataTable({
        ajax: {
              url: 'ajax/populateAssetRequestPortal.php',
              type: 'POST',
              data: {show:showType,
            	     pmoRaised:pmoRaised}
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
                      { "data": "FM", "defaultContent": "" },
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
                      { "data": "COMMENT" , "defaultContent": ""},
                      { "data": "USER_CREATED" , "defaultContent": ""}
                      ,{ "data": "REQUESTEE_EMAIL" , "defaultContent": ""}
                      ,{ "data": "REQUESTEE_NOTES" , "defaultContent": ""}
                      ,{ "data": "APPROVER_EMAIL" , "defaultContent": ""}
                      ,{ "data": "FM_EMAIL" , "defaultContent": ""}
                      ,{ "data": "FM_NOTES" , "defaultContent": ""}
                      ,{ "data": "CTB_RTB" , "defaultContent": ""}
                      ,{ "data": "TT_BAU" , "defaultContent": ""}
                      ,{ "data": "LOB" , "defaultContent": ""}
                      ,{ "data": "WORK_STREAM" , "defaultContent": ""}
                      ,{ "data": "PRE_REQ_REQUEST" , "defaultContent": ""}
                      ,{ "data": "REQUEST_RETURN" , "defaultContent": ""}
                  ],
          columnDefs: [
                         { visible: false, targets: [8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30] }
                  ] ,
          order: [[ 0, "desc" ]],
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

          $( 'input', this.header() ).on( 'keyup change', function () {
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
	        AssetPortal.countRequestsForPortal();
	    });
  },

  this.listenForReportReload = function(){
	    $(document).on('click','#reportReload', function(e){
	    	$('#portalTitle').text('Asset Request Portal');
	    	$.fn.dataTableExt.afnFiltering.pop();
	    	assetPortal.table.ajax.reload();
	        AssetPortal.countRequestsForPortal();
	      });
  },

  this.listenForReportShowAll = function(){
	  $(document).on('click','#reportShowAll', function(e){
		  assetPortal.table.destroy();
		  AssetPortal.initialiseAssetRequestDataTable('all');
		  $('#portalTitle').text('Asset Request Portal - Show All');
	  });
},

  this.listenForReportShowExportable = function(){
		$(document).on('click','#reportShowExportable', function(e){
			assetPortal.table.destroy();
			AssetPortal.initialiseAssetRequestDataTable('exportable',true);
			$('#portalTitle').text('Asset Request Portal - Show Pmo To Raise Requests');
	  });
},


this.listenForReportShowUserRaised = function(){
		$(document).on('click','#reportShowUserRaised', function(e){
			assetPortal.table.destroy();
			AssetPortal.initialiseAssetRequestDataTable('exportable',false);
			$('#portalTitle').text('Asset Request Portal - Show User Raised Requests');
	  });
},

this.listenForReportExported = function(){
	  console.log('set listener for clicked on exported');
	  $(document).on('click','#reportShowExported', function(e){
		  console.log('clicked on exported');
		  assetPortal.table.destroy();
		  AssetPortal.initialiseAssetRequestDataTable('exported');
		  $('#portalTitle').text('Asset Request Portal - Exported');
	  });
},

this.listenForReportBauRaised = function(){
	  $(document).on('click','#reportShowBauRaised', function(e){
		  assetPortal.table.destroy();
		  AssetPortal.initialiseAssetRequestDataTable('bauRaised');
		  $('#portalTitle').text('Asset Request Portal - Raised BAU');
	  });
},

this.listenForReportNonBauRaised = function(){
	  console.log('set listener for clicked on exported');
	  $(document).on('click','#reportShowNonBauRaised', function(e){
		  assetPortal.table.destroy();
		  AssetPortal.initialiseAssetRequestDataTable('nonBauRaised');
		  $('#portalTitle').text('Asset Request Portal - Raised Non-BAU');
	  });
},


this.listenForReportShowUid = function(){
	$(document).on('click','#reportShowUid', function(e){
    	$('#portalTitle').text('Asset Request Portal - Show UID' );
    	$.fn.dataTableExt.afnFiltering.pop();

		assetPortal.table.destroy();
		AssetPortal.initialiseAssetRequestDataTable('all');


    	assetPortal.table.columns().visible(false,false);
    	assetPortal.table.columns([0,1,2,3,10,11]).visible(true);
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
			        AssetPortal.countRequestsForPortal();
		        }
	      });
	  });
  },


  this.listenForExportBauButton = function(){
	  $(document).on('click','#exportBauForOrderIt', function(e){
		  $('#exportBauForOrderIt').addClass('spinning');
		  $('#exportBauForOrderIt').attr('disabled',true);
	      $.ajax({
		        url: "ajax/exportForOrderIt.php",
		        type: 'GET',
		        data: { bau: true},
		        success: function(result){
		        	console.log(result);
		        	var resultObj = JSON.parse(result);
			    	assetPortal.table.ajax.reload();
		        	$('#exportResultsModal .modal-body').html(resultObj.messages);
		        	$('#exportResultsModal').modal('show');
		  		    $('#exportBauForOrderIt').removeClass('spinning');
				    $('#exportBauForOrderIt').attr('disabled',false);
			        AssetPortal.countRequestsForPortal();
		        }
	      });
	  });
  },

  this.listenForExportNonBauButton = function(){
	  $(document).on('click','#exportNonBauForOrderIt', function(e){
		  $('#exportNonBauForOrderIt').addClass('spinning');
		  $('#exportNonBauForOrderIt').attr('disabled',true);
	      $.ajax({
		        url: "ajax/exportForOrderIt.php",
		        type: 'GET',
		        data: { bau: false},
		        success: function(result){
		        	console.log(result);
		        	var resultObj = JSON.parse(result);
			    	assetPortal.table.ajax.reload();
		        	$('#exportResultsModal .modal-body').html(resultObj.messages);
		        	$('#exportResultsModal').modal('show');
		  		    $('#exportNonBauForOrderIt').removeClass('spinning');
				    $('#exportNonBauForOrderIt').attr('disabled',false);
			        AssetPortal.countRequestsForPortal();
		        }
	      });
	  });
  },
  
  this.listenForAmendOrderIt = function(){
	  $(document).on('click','.btnAmendOrderItNumber', function(e){
		  console.log('wish to amend Order IT number');
		  console.log(e);		 
		  var reference = $(e.target).data('reference');
		  var currentOit = $(e.target).data('orderit');
		  $('#amendOrderItRequestReference').val(reference);
		  $('#amendOrderItCurrent').val(currentOit);
		  $('#amendOrderItNewOrderIt').val('');
		  $('#amendOrderItModal').modal('show');
	  });
  },
  
  this.listenForSaveAmendedOrderIt = function(){
	  $(document).on('click','#confirmedSaveOrderIt', function(e){
		  $('#confirmedSaveOrderIt').addClass('spinning');
		  $('#confirmedSaveOrderIt').attr('disabled',true);
		  var reference = $('#amendOrderItRequestReference').val();
		  var currentOit = $('#amendOrderItCurrent').val();		  
		  var newOit = $('#amendOrderItNewOrderIt').val();
	      $.ajax({
		        url: "ajax/saveAmendedOrderIt.php",
		        type: 'POST',
		        data: { reference: reference,
		        	    currentOit: currentOit,
		        	    newOit: newOit },
		        success: function(result){
		        	console.log(result);
		        	var resultObj = JSON.parse(result);
			    	assetPortal.table.ajax.reload();
		  		    $('#confirmedSaveOrderIt').removeClass('spinning');
				    $('#confirmedSaveOrderIt').attr('disabled',false);
					var reference = $('#amendOrderItRequestReference').val('');
					var currentOit = $('#amendOrderItCurrent').val('');		  
					var newOit = $('#amendOrderItNewOrderIt').val('');
				    $('#amendOrderItModal').modal('hide');

		        }
	      });
		  
	  });

  },
  




  this.listenForEditUid = function(){
	  $(document).on('click','.btnEditUid', function(e){
		  $('#asset').val($(e.target).data('asset'));
		  $('#userid').val($(e.target).data('requestee'));

		  var primaryUid   = $(e.target).data('primaryuid');
		  var secondaryUid = $(e.target).data('secondaryuid');

		  var primaryTitle = $(e.target).data('primarytitle');
		  $('#primaryLabel').text(primaryTitle);
		  $('#primaryUid').attr('placeholder',primaryTitle);

		  if(primaryUid){
			  $('#primaryUid').val(primaryUid);
		  } else {
			  $('#primaryUid').val('');
		  }

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

		  if(secondaryUid){
			  $('#secondaryUid').val(secondaryUid);
		  } else {
			  $('#secondaryUid').val('');
		  }




		  $('#reference').val($(e.target).data('reference'));
//		  $('#primaryUid').val('');
//		  $('#secondaryUid').val('');
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

  this.listenForDeVarbButton = function(){
	  $(document).on('click','#deVarb', function(e){
		  $('#deVarb').addClass('spinning');
		  $('#deVarb').attr('disabled',true);
		  if(!confirm('This will remove SELECTED requests from the VARB, please confirm that is what you want to do')){
			  $('#deVarb').removeClass('spinning');
			  $('#deVarb').attr('disabled',false);
			  return false;
		  }
		  var formData = $('#mapVarbToOrderItForm').serialize();
	      $.ajax({
		        url: "ajax/deVarb.php",
		        type: 'POST',
		        data:formData,
		        success: function(result){
		        	var resultObj = JSON.parse(result);
			    	// assetPortal.table.ajax.reload();
		        	$('#deVarb').removeClass('spinning');
				    $('#deVarb').attr('disabled',false);
				    $('#mapVarbToOrderItModal').modal('hide');
				    assetPortal.table.ajax.reload();
				    AssetPortal.countRequestsForPortal();
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
		         allowClear:true,
		         });

			$('#mappedVarb').select2({
		         placeholder:"Select VARB",
		         allowClear:true,
		         });
			$('#mappedRef').select2({
		         placeholder:"Select Request Reference",
		         allowClear:true,
		         });

			AssetPortal.populateRequestTableForOrderIt();
			AssetPortal.listenForOrderItSelected();
			AssetPortal.listenForMappedVarbSelected();
			AssetPortal.listenForMappedRefSelected();

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

  this.listenForMappedVarbSelected = function(){
	  $('#mappedVarb').on('select2:select', function (e) {
		  assetPortal.requestsWithStatus.ajax.reload();
		});
  }

  this.listenForMappedRefSelected = function(){
	  $('#mappedRef').on('select2:select', function (e) {
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
		  		    $('#saveMapVarbToOrderIT').removeClass('spinning');
		        }
	    	  });
	      } else {
	    	  alert('Form is not valid, please correct');
	    	  $('#saveMapVarbToOrderIT').removeClass('spinning');
	      }
		  assetPortal.table.ajax.reload();
		  AssetPortal.countRequestsForPortal();
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
			    	AssetPortal.countRequestsForPortal();

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
		        	$('#saveOrderItStatus').removeClass('spinning');

		  		  	assetPortal.table.ajax.reload();
		  		    AssetPortal.countRequestsForPortal();

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
			$('#unmappedRef').select2({
		         placeholder:"Select Request Reference",
		         });
			AssetPortal.populateRequestTableForVarb();
			AssetPortal.listenForVarbSelectedForMapping();
			AssetPortal.listenForRefSelectedForMapping();

	  });
  }


  this.listenForVarbSelectedForMapping = function(){
	  console.log('setup listener');
	  $('#unmappedVarb').off('select2:select.varb').on('select2:select.varb', function (e) {
		  assetPortal.varbRequestTable.ajax.reload();
		  $('#deVarb').attr('disabled',false);
		});
  }

  this.listenForRefSelectedForMapping = function(){
	  console.log('setup listener');
	  $('#unmappedRef').off('select2:select.varb').on('select2:select.varb', function (e) {
		  assetPortal.varbRequestTable.ajax.reload();
		  $('#deVarb').attr('disabled',false);
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


this.listenForAddToJustification  = function(){
    $(document).on('click','.btnAddToJustification', function(e){
    	var reference = $(e.target).data('reference');
      	$.ajax({
	        url: "ajax/getDetailsForEditJustification.php",
	        type: 'GET',
	        data: { reference : reference },
	        success: function(result){

	        	var resultObj = JSON.parse(result);
	        	console.log(resultObj);
	        	console.log($(e.target));

	        	$('#editJustificationRequestReference').html($(e.target).data('reference'));
	        	$('#editJustificationRequestee').html($(e.target).data('requestee'));
	        	$('#editJustificationAssetTitle').html($(e.target).data('asset'));
	        	$('#editJustificationStatus').val($(e.target).data('status'));

	        	$('#editJustificationJustification').val(resultObj.justification);
	        	$('#editJustificationComment').html(resultObj.comment);
	        	$('#justificationEditModal').modal('show');
	        }
	     });
    });
},


this.listenForSaveAmendedJustification = function(){
	$(document).on('click','#editJustificationConfirm', function(e){
	    var form = document.getElementById('editJustificationForm');
	    var formValid = form.checkValidity();
	    if(formValid){
			$('#editJustificationConfirm').addClass('spinning');
			var reference = $('#editJustificationRequestReference').text();
			var justification = $('#editJustificationJustification').val();
			var status = $('#editJustificationStatus').val();

			console.log(reference);
			console.log(justification);
			console.log(status);

	  	  	$.ajax({
		        url: "ajax/saveAmendedJustification.php",
		        type: 'POST',
		        data: { reference : reference,
		        	    justification : justification,
		        	    status : status },
		        success: function(result){
		        	var resultObj = JSON.parse(result);
		        	$('#editJustificationConfirm').removeClass('spinning');
		        	$('#justificationEditModal').modal('hide');
		        	assetPortal.table.ajax.reload();
		        }
		    });
	    }
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
	          	//var status = approveReject ? 'Approved for Order IT' : 'Rejected in vBAC';
	          	//var orderitstatus = raisedInOrderIt ? orderItStatus : 'Yet to be raised';
	          	// var status = approveReject && raisedInOrderIt ? 'Raised in Order IT' : status;   --> We are now saying - even for the 'raised in order it' they need to go throufg 'Approved for Order IT' status

	          	console.log(approveReject);
	          	console.log(orderItStatus);
	          	console.log(raisedInOrderIt);



	          	switch(true) {
	          	case approveReject && raisedInOrderIt:
	          		console.log('true and true');
	          		// It was already raised in order it - and now it's approved.
	          		var status = 'Approved for Order IT';
	          		var orderitstatus = 'Raised in Order IT';
	          		break;
	          	case approveReject && !raisedInOrderIt:
	          		console.log('true and false');
	          		// It's NOT already raised in order it - and has now been approved.
	          		var status = 'Approved for Order IT';
	          		var orderitstatus = 'Yet to be raised';
	          		break;
	          	case !approveReject && raisedInOrderIt:
	          		console.log('false and true');
	          		// It was already raised in order it - but has now been rejected in vbac.
	          		var status = 'Rejected in vBAC';
	          		var orderitstatus = 'Raised in Order IT';
	          		break;
	          	case !approveReject && !raisedInOrderIt:
	          		console.log('false and false');
	          		// It's NOT raised in order it - but has now been rejected in vbac, so we WON'T Raise it in Order IT.
	          		var status = 'Rejected in vBAC';
	          		var orderitstatus = 'Not to be raised';
	          		break;
	          	default:
	          		break;
	          	}

     	        $(allDisabledFields).attr('disabled',true);

	          	$.ajax({
			        url: "ajax/updateAssetRequestStatus.php",
			        type: 'POST',
			        data: {reference: reference,
			        	   status : status,
			        	   orderitstatus : orderitstatus,
			        	   comment : comment },
			        success: function(result){
			        	console.log(result);
			        	var resultObj = JSON.parse(result);
			        	$('#approveRejectModal').modal('hide');
			        	assetPortal.table.ajax.reload();
			        	AssetPortal.countRequestsForPortal();
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

		  var primaryUid   = $(e.target).data('primaryuid');
		  var secondaryUid = $(e.target).data('secondaryuid');


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

		  if(primaryUid){
			  $('#primaryUidRet').val(primaryUid);
			  $('#primaryUidRet').attr('disabled',true);
		  } else {
			  $('#primaryUidRet').val('');
			  $('#primaryUidRet').attr('disabled',false);
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

		  if(secondaryUid){
			  $('#secondaryUidRet').val(secondaryUid);
			  $('#secondaryUidRet').attr('disabled',true);
		  } else {
			  $('#secondaryUidRet').val('');
			  $('#secondaryUidRet').attr('disabled',false);
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
			    	AssetPortal.countRequestsForPortal();
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
	            	  var ref  = $('#unmappedRef').find(':selected').val();
	            	  var varbObject = { 'varb' : varb,
	            			  			  'ref' : ref };
	            	  return varbObject; }
	          		}	,
	          columns: [
	                      { "data": "INCLUDED" , "defaultContent": "", "width":"5%" },
	                      { "data": "REFERENCE" ,"defaultContent": "", "width":"5%" },
	                      { "data": "ORDERIT_NUMBER" ,"defaultContent": "", "width":"15%" },
	                      { "data": "PERSON" ,"defaultContent": "", "width":"25%" },
	                      { "data": "ASSET","defaultContent": "", "width":"25%"},
	                      { "data": "COMMENT","defaultContent": "", "width":"25%"},
	                  ],

	          autoWidth: false,
	          deferRender: true,
	          responsive: false,
	          processing: true,
	          responsive: true,
	          pageLength: 20,
	          order: [[ 1, "asc" ]],
	          language: {
	        	    "emptyTable": "Please select VARB or Request Reference"
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
	            	  var varb = $('#mappedVarb').find(':selected').val();
	            	  var ref  = $('#mappedRef').find(':selected').val();

	            	  var oitObject = { 'orderit' : orderit,
	            			  			'varb' : varb,
	            			  			'ref'  : ref };
	            	  return oitObject; }
	              },

	          columns: [
	                      { "data": "REFERENCE" ,"defaultContent": "", "width":"5%" },
	                      { "data": "PERSON" ,"defaultContent": "", "width":"5%" },
	                      { "data": "ASSET","defaultContent": "", "width":"15%"},
	                      { "data": "STATUS","defaultContent": "", "width":"15%"},
	                      { "data": "ACTION","defaultContent": "", "width":"20%"},
	                      { "data": "PRIMARY_UID","defaultContent": "", "width":"8%"},
	                      { "data": "COMMENT","defaultContent": "", "width":"25%"},
	                      { "data": "ORDERIT_RESPONDED","defaultContent": "", "width":"7%"}
	                      
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
	        	    "emptyTable": "Please select Order IT/Varb or Request Reference"
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