/*
 *
 *
 *
 */



function dlp() {
	
	var table;

	this.init = function(){
		console.log('+++ Function +++ dlp.init');		
		$('.toggle').bootstrapToggle();

		$('#licencee').select2({
	  	  	width: '100%',
		  	placeholder: 'Select Licencee',
			allowClear: true,			
		});

	  	$('#approvingManager').select2({
	  	  	width: '100%',
		  	placeholder: 'Select Approving Mgr',
			allowClear: true,
		    });
		console.log('--- Function --- dlp.init');
	},
	
	this.initialiseLicenseeDropDown = function(){
		$('#saveDlpLicence').attr('disabled',true);  // Lock the Save Button - when the listener fires we unlock it. Saves duplicates.
	  	$('#licencee').select2({
			ajax: {
				    url: 'ajax/populateDlpLicenseeDropdown.php',
				    dataType: 'json'
				    // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
				  },
	  		
	  		
	  	  	width: '100%',
		  	placeholder: 'Select licence holder',
			allowClear: true,
		    });		
	},
		  
	this.listenForSelectLicencee = function(){
		$(document).off('select2:select','#licencee');
		$(document).on('select2:select','#licencee', function (e) {
			$('#saveDlpLicence').attr('disabled',false);
			console.log(e.params.data);
			var cnum = e.params.data.id;
			var fmcnum = cnumfm[cnum];
		    var hostname = licences[cnum]; 
		    
		    console.log(hostname);
		    console.log(cnum);
		    console.log(fmcnum);
		    
		    
		    $('#currentHostname').val(hostname);
		    
		    console.log($('#currentHostname').val())
		    
		    $('#approvingManager').val(fmcnum).trigger('change');
		    
		    console.log($('#approvingManager').val());
		    
		});
	},
	  
	  
	this.listenForSaveDlp = function(){
		$(document).on('click','#saveDlpLicence', function(){
			console.log('they want to save');

			$('#saveDlpLicence').addClass('spinning');
			$('#saveDlpLicence').attr('disabled',true);

		      var form = document.getElementById('dlpRecordingForm');
		      var formValid = form.checkValidity();
		      
		      var currentHostname = $('#currentHostname').val();
		      var hostname = ($('#hostname').val()).toUpperCase();
		      
		      console.log(formValid);
		      console.log(currentHostname);
		      console.log(hostname);
		      
		      formValid = formValid ? currentHostname != hostname : formValid;
		      console.log(formValid);
		      

		      if(formValid){
		    	  var Dlp = new dlp();
		    	  Dlp.saveRecord();
		      } else if(currentHostname == hostname) {
		    	  alert('New Hostname(' + hostname  + ') matches current Hostname (' + currentHostname + ') for the licencee');
	    		  $('#saveDlpLicence').removeClass('spinning');
	    		  $('#saveDlpLicence').attr('disabled',false);
		      } else {
		    	  alert('Please correct form');
	    		  $('#saveDlpLicence').removeClass('spinning');
	    		  $('#saveDlpLicence').attr('disabled',false);
		      }
		  });

	  },
	  
	  this.saveRecord = function(){
		  console.log('would save DLP now');
		  var allDisabledFields = ($("#dlpRecordingForm input:disabled"));
	      $(allDisabledFields).attr('disabled',false);
	      var formData = $('#dlpRecordingForm').serialize();
	      $(allDisabledFields).attr('disabled',true);
	      console.log(formData);
	      $.ajax({
	    	  url: "ajax/saveDlp.php",
	          data : formData,
	          type: 'POST',
	          success: function(result){
	        	  var resultObj = JSON.parse(result);
	        	  console.log(resultObj);
	        	  console.log(resultObj.licencee);
	        	  console.log(resultObj.hostname);
	        	  console.log(licences);
	        	  licences[resultObj.licencee] = resultObj.hostname;
	        	  console.log(licences);
	        	  $('#licensee').val(null).trigger('change');
	        	//  Dlp.initialiseLicenseeDropDown();
	        	//  Dlp.listenForSelectLicencee();
	        	  $('#dlpSaveResponseModal .modal-body').html(resultObj.actionsTaken + "<hr/><p class='bg-warning'>" + resultObj.messages + "</p>");
	        	  $('#dlpSaveResponseModal').modal('show');
	    		  $('#saveDlpLicence').removeClass('spinning');
	    		  $('#saveDlpLicence').attr('disabled',false);	    		 
	    		  $('#approvingManager').val('').trigger('change');
	    		  $('#hostname').val('');
	    		  $('#currentHostname').val('');
	    		  Dlp.table.ajax.reload();
	            },
	          error : function(jqXHR, textStatus, errorThrown){
	        	  console.log(textStatus);
	        	  console.log(errorThrown);
	        	  $('#dlpSaveResponseModal .modal-body').html(textStatus + "<hr/>" + errorThrown);
	        	  $('#dlpSaveResponseModal').modal('show');
	    		  $('#saveDlpLicence').removeClass('spinning');
	    		  $('#saveDlpLicence').attr('disabled',false);
	    		  $('#licensee').val(null).trigger('change');
	    		  $('#hostname').val('');
	    		  $('#currentHostname').val('');
	         	}
	         });  
	  },
	  
	  this.initialiseLicenseeReport = function(showType,withButtons){
		  showType = typeof(showType) == 'undefined'  ? 'active' : showType;
		  withButtons = typeof(withButtons) == 'undefined'  ? 'true' : withButtons;
		  // Setup - add a text input to each footer cell
		  $('#dlpLicensesTable tfoot th').each( function () {
			  var title = $(this).text();
		      $(this).html( title + '<br/><input type="text" id="footer'+ title + '" placeholder="Search '+title+'" />' );
		  });
		  // DataTable
		  Dlp.table = $('#dlpLicensesTable').DataTable({
			  ajax: {
				  url: 'ajax/populateDlpLicenseReport.php',
		          type: 'POST',
		          data: { showType: showType,
		        	      withButtons: withButtons}
			  },
		      columns: [
		                { "data": "CNUM" ,"defaultContent": "" },
		                { "data": "LICENSEE" ,"defaultContent": "" },
		                { "data": "HOSTNAME","defaultContent": ""},
		                { "data": "APPROVER", "defaultContent": "" },
		                { "data": "APPROVED", "defaultContent": "" },
		                { "data": "FM", "defaultContent": "" },
		                { "data": "CREATED", "defaultContent": "" },
		                { "data": "CODE", "defaultContent": "" },
		                { "data": "OLD_HOSTNAME", "defaultContent": "" },
		                { "data": "TRANSFERRED", "defaultContent": "" },
		                { "data": "TRANSFERRER", "defaultContent": "" },
		                { "data": "STATUS", "defaultContent": "" }
		                ],
		     autoWidth: true,
		     deferRender: true,
		     responsive: true,
		     processing: true,
		     language: {
		        	    "emptyTable": "No License Details to show"
		          		},
		     dom: 'Blfrtip',
	         buttons: [
	                    'colvis',
	                    'excelHtml5',
	                    'csvHtml5',
	                    'print'
	                ],
	      });
		  // Apply the search
		  Dlp.table.columns().every( function () {
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

	  this.listenForReportShowDlpActive = function(){
			$(document).on('click','#reportShowDlpActive', function(e){
				console.log('show active');
				Dlp.table.destroy();
				Dlp.initialiseLicenseeReport('active','true');
		    	Dlp.table.columns().visible(false,false);
		    	Dlp.table.columns([0,1,2,3,4,5,6,7,8,9,10]).visible(true);
				$('#portalTitle').text('Licenses - Active');
		  });
	  },
	  
	  
	  this.listenForReportShowDlpPending = function(){
			$(document).on('click','#reportShowDlpPending', function(e){
				console.log('show pending');
				Dlp.table.destroy();
				Dlp.initialiseLicenseeReport('pending','true');
		    	Dlp.table.columns().visible(false,false);
		    	Dlp.table.columns([0,1,2,3,4,5]).visible(true);
				$('#portalTitle').text('Licenses - Pending');
		  });
	  },
	  
	  this.listenForReportShowDlpRejected = function(){
			$(document).on('click','#reportShowDlpRejected', function(e){
				Dlp.table.destroy();
				Dlp.initialiseLicenseeReport('rejected','true');
		    	Dlp.table.columns().visible(false,false);
		    	Dlp.table.columns([0,1,2,3,4,5,6,7,8,9,10]).visible(true);
				$('#portalTitle').text('Licenses - Rejected');
		  });
	  },
	  
	  this.listenForReportShowDlpTransferred = function(){
			$(document).on('click','#reportShowDlpTransferred', function(e){
				Dlp.table.destroy();
				Dlp.initialiseLicenseeReport('transferred','false');
		    	Dlp.table.columns().visible(false,false);
		    	Dlp.table.columns([0,1,2,3,8,9,10]).visible(true);
				$('#portalTitle').text('Licenses - Transferred');
		  });
	  },  

	  this.listenForReportShowDlpAll = function(){
			$(document).on('click','#reportShowDlpAll', function(e){
				Dlp.table.destroy();
				Dlp.initialiseLicenseeReport('all','false');
		    	Dlp.table.columns().visible(false,false);
		    	Dlp.table.columns([0,1,2,3,8,9,10]).visible(true);
				$('#portalTitle').text('Licenses - All');
		  });
	  }, 
	  
	  
	  this.listenForDeleteDlp = function(){
			$(document).on('click','.btnDlpLicenseDelete', function(e){
				  var cnum = $(e.target).data('cnum');
				  var hostname = $(e.target).data('hostname');		
				  var transferred = $(e.target).data('transferred');
				  $.ajax({
					  url: "ajax/dlpDelete.php",
		   		      data : { cnum: cnum,
		   		               hostname: hostname,
		   		               transferred: transferred		   		               
		   		               },
					 type: 'POST',
					 success: function(result){
						 var resultObj = JSON.parse(result);
					     Dlp.table.ajax.reload();
					     delete licences[resultObj.cnum]; // Remove this entry from the licences object.
					     }
				  });
				
			});
	  },
	  
	  
	  this.listenForApproveDlp = function(){
			$(document).on('click','.btnDlpLicenseApprove', function(e){
				Dlp.approveRejectDlp(e,'approved');
			});
	  },
	  
	  this.listenForRejectDlp = function(){
			$(document).on('click','.btnDlpLicenseReject', function(e){
				Dlp.approveRejectDlp(e,'rejected');
			});
	  },
			
			
	  this.approveRejectDlp = function(e, approveReject){
		  var cnum = $(e.target).data('cnum');
		  var hostname = $(e.target).data('hostname');				
		  $.ajax({
			  url: "ajax/dlpApproveReject.php",
   		      data : { cnum: cnum,
   		               hostname: hostname,
   		               approveReject: approveReject
   		               },
			 type: 'POST',
			 success: function(result){
				 var resultObj = JSON.parse(result);
			     console.log(resultObj);
			     Dlp.table.ajax.reload();
			     }
		  });
	  },
	  
	  

	  
	  this.dummy = function(){
	  }
}