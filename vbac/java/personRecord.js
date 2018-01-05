/*
 *
 *
 *
 */

function personRecord() {

	var table;
	var dataTableElements;
	var currentXmlDoc;

	this.init = function(){
		console.log('+++ Function +++ personRecord.init');
		console.log('--- Function --- personRecord.init');
	},

	this.listenForName = function(){
		var name = document.getElementById['person_name'];
        var config = {
            key: 'vbac;rob.daniel@uk.ibm.com',
            faces: {
                //The handler for clicking a person in the drop-down.
                onclick: function(person) {
             	   console.log(person);
             	   var intranet = document.getElementById('person_intranet');
                   if(typeof(intranet) !== 'undefined'){ intranet.value = person['email'];};

                   var notesId =  document.getElementById('person_notesid');
                   if(typeof(notesId) !== 'undefined'){ notesId.value = person['notes-id'];};

                   var bio =  document.getElementById('person_bio');
                   if(typeof(bio) !== 'undefined'){ bio.value = person['bio'];};

                   var uid = document.getElementById('person_serial');
                   if(typeof(uid) !== 'undefined'){ uid.value = person['uid'];};
                   $('#person_serial').attr('disabled','disabled');

                   var personObj = new personRecord();
                   personObj.fetchBluepagesDetailsForCnum(person['uid']);

                   $('#personDetails').show();
                   $('#person_contractor_id').select2();
                   $('#person_functionalMgr').select2();

                   return person['name'];
                   }
            }
        };

        if(typeof FacesTypeAhead !== 'object'){
        	alert('Faces Type Ahead not found, ensure you are connected to IBM network');
        	$('#person_name').attr('disabled',true);
        	$('#person_name').attr('placeholder','Please connect to IBM network');
        	$('#person_serial').attr('disabled',true);
        	$('#person_serial').attr('placeholder','Please connect to IBM network');
        } else {
            FacesTypeAhead.init(
            		document.getElementById('person_name'),
            		config
            		);
        }
	},

	this.listenForOnBoarding = function() {
		console.log('listening');
		$(document).on('click','#onBoardingBtn', function(){
			window.open('pb_onboard.php', '_self');
		});
	},

	this.listenForOffBoarding = function(){
		$(document).on('click','#offBoardingBtn', function(){
			 window.open('pb_offboard.php', '_self');
		});

	},

	this.listenForSerial = function(){
		$(document).on('keyup change','#person_serial',function(e){
			var cnum = $(e.target).val();
			console.log(cnum);
			if(cnum.length==9){
				console.log(this);
				var person = new personRecord;
				person.fetchBluepagesDetailsForCnum(cnum);
			}

		});
	},

	this.fetchBluepagesDetailsForCnum = function(cnum){
		console.log(cnum);
		if(cnum.length == 9){
		    $.ajax({
//		    	url: "https://bluepages.ibm.com/BpHttpApisv3/slaphapi?ibmperson/(uid=" + cnum + ").search/byjson",
		    	url: "http://bluepages.ibm.com/BpHttpApisv3/wsapi?byCnum=" + cnum,
		        type: 'GET',
		    	success: function(result){
		    		console.log(result);
		    		var personDetailsObj = JSON.parse(result);
		    		var attributes = personDetailsObj.search.entry[0].attribute;
		    		for(a=0;a<attributes.length;a++){
		    			var object = attributes[a];
		    			var value = object.value;
		    			var name = object.name;
		    			switch(name){
		    			case 'preferredidentity':
		    				var intranet = document.getElementById('person_intranet');
		    				if(typeof(intranet) !== 'undefined'){ intranet.value = value;};
		    				break;
		    			case 'jobresponsibilities':
		    				var bio =  document.getElementById('person_bio');
		                   if(typeof(bio) !== 'undefined'){ bio.value = value;};
		                   break;
		    			case 'notesemail':
		    				console.log(value[0]);
		    				console.log(typeof(value[0]));

		    					var Step1 = value[0].replace('CN=','');
		    					var Step2 = Step1.replace('OU=','');
		    					var Step3 = Step2.replace('O=','');
		    					var Split = Step3.split('@');
		    					var notesId = Split[0];
		    				    var notesIdElem =  document.getElementById('person_notesid');
			                   if(typeof(notesIdElem) !== 'undefined' && notesIdElem.value == '' ){ notesIdElem.value = notesId;};
		                   break;
		    			case 'uid':
			                   var uid =  document.getElementById('person_uid');
			                   if(typeof(uid) !== 'undefined'){ uid.value = value;};
			                   break;
		    			case 'preferredfirstname':
			                   var name =  document.getElementById('person_first_name');
			                   console.log(name + ":" + value);
			                   if(typeof(name) !== 'undefined'){ name.value = value;};
			                   break;
		    			case 'sn':
			                   var name =  document.getElementById('person_last_name');
			                   console.log(name + ":" + value);
			                   if(typeof(name) !== 'undefined'){ name.value = value ;};
			                   break;
		    			case 'ismanager':
		    				   var isMgr =  document.getElementById('person_is_mgr');
		    				   console.log(isMgr + ":" + value);
			                   if(typeof(isMgr) !== 'undefined'){ isMgr.value = value ;};
				               break;
		    			case 'phonemailnumber':
		    				   var phone =  document.getElementById('person_phone');
		    				   console.log(phone + ":" + value);
			                   if(typeof(phone) !== 'undefined'){ phone.value = value ;};
				               break;
		    			default:
		    				// console.log(name + ":" + value);
		    			}
		    		}
                   $('#personDetails').show();
                   $('#person_name').val($('#person_first_name').val() + " " + $('#person_last_name').val()).attr('disable',true);

		    	},
		        error: function (xhr, status) {
		            // handle errors
		        	console.log('error');
		        	console.log(xhr);
		        	console.log(status);
		        }
		    });
		};

	},

	this.listenForAccountOrganisation = function(){
		$(document).on('click','.accountOrganisation', function(){
			var accountOrganisation = $(this).val();
			var nullFirstEntry  = [''];
			for(i=0;i<workStream.length;i++){
				if(workStream[0][i]==accountOrganisation){
					var workStreamValues = nullFirstEntry.concat(workStream[i+1]);
				}
			}
			$('#work_stream').select2('destroy');
			$('#work_stream').html('');
			if(typeof(workStreamValues)!='undefined'){
				$('#work_stream').select2({
					data:workStreamValues,
					placeholder:"Select workstream",
					})
					.attr('disabled',false)
					.attr('required',true);
			} else {
				$('#work_stream').select2({
					data:[''],
					placeholder:"No workstream required",
					})
				.attr('disabled',true)
				.attr('required',false);
			};
		});
	},

	this.listenForSaveBoarding = function(){
		$(document).on('click','#saveBoarding', function(){

			var form = $('#boardingForm');
			var formValid = form[0].checkValidity();

			$("#saveBoarding").addClass('spinning');

			if(formValid){
				var allDisabledFields = ($("input:disabled"));
				$(allDisabledFields).attr('disabled',false);
				var formData = form.serialize();
				$(allDisabledFields).attr('disabled',true);
				console.log(formData);
			    $.ajax({
			    	url: "ajax/saveBoardingForm.php",
			    	type: 'POST',
			        data : formData,
			    	success: function(result){
						$("#saveBoarding").removeClass('spinning');
			    		console.log(result);
			    		var resultObj = JSON.parse(result);
			    		if(resultObj.success==true){
			    			var message = "<div class=panel-heading><h3 class=panel-title>Success</h3>" + resultObj.messages
			    			$('#savingBoardingDetailsModal  .panel').html(message);
			    			$('#savingBoardingDetailsModal  .panel').addClass('panel-success');
			    			$('#savingBoardingDetailsModal  .panel').removeClass('panel-danger');
			    		} else {
			    			var message = "<div class=panel-heading><h3 class=panel-title>Error</h3>" + resultObj.messages
			    			$('#savingBoardingDetailsModal  .panel').html(message);
			    			$('#savingBoardingDetailsModal  .panel').addClass('panel-danger');
			    			$('#savingBoardingDetailsModal  .panel').removeClass('panel-success');
			    		};
			    		$('#savingBoardingDetailsModal').modal('show');
			    		$('#boardingForm :input').attr('disabled',true);
			    		$('#saveBoarding').attr('disabled',true);
			    		$('#initiatePes').attr('disabled',false);
			    	}
			    });
			}
		});

	},


	this.initialisePersonTable = function(){
	    $.ajax({
	    	url: "ajax/createHtmlForPersonTable.php",
	    	type: 'POST',
	    	success: function(result){
	    		var Person = new personRecord();
	    		$('#personDatabaseDiv').html(result);
	    		Person.initialiseDataTable();
	    	}
	    });

	},

	this.initialiseDataTable = function(){
	    // Setup - add a text input to each footer cell
	    $('#personTable tfoot th').each( function () {
	        var title = $(this).text();
	        $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
	    } );
		// DataTable
	    personRecord.table = $('#personTable').DataTable({
	    	ajax: {
	            url: 'ajax/populatePersonDatatable.php',
	            type: 'POST',
	        }	,
	        order: [[ 5, "asc" ]],
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
	    personRecord.table.columns().every( function () {
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

	this.listenForReportPes = function(){
		$(document).on('click','#reportPes', function(e){
			personRecord.table.columns().visible(false,false);
			personRecord.table.columns([4,5,21,22,23,24,25]).visible(true);
			personRecord.table.order([21,'desc'],[5,"asc"]).draw();
			});
	},

	this.listenForReportPerson = function(){
		$(document).on('click','#reportPerson', function(e){
			personRecord.table.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29]).visible(true,false);
			personRecord.table.columns([2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29]).visible(true);
			personRecord.table.columns.draw();
			});
	},


	this.listenForReportReset = function(){
		$(document).on('click','#reportReset', function(e){
			personRecord.table.columns().visible(true);
			personRecord.table.order([5,"asc"]).draw();
			});
	},

	this.listenForInitiatePes = function(){
		$(document).on('click','#initiatePes', function(e){
			console.log('initiatePes PES');

			$("#initiatePes").addClass('spinning');

			var cnum = $('#person_uid').val();
			console.log(cnum);
		    $.ajax({
		    	url: "ajax/initiatePes.php",
		    	data : {cnum:cnum},
		    	type: 'POST',
		    	success: function(result){
		    		console.log(result);
		    		var resultObj = JSON.parse(result);
		    		$('#savingBoardingDetailsModal').on('hidden.bs.modal', function () { // When they close the modal this time, reload the page.
		    			$('#savingBoardingDetailsModal').off('hidden.bs.modal');  // only do this once.
		    			location.reload();
	    			});
		    		if(resultObj.success==true){
		    			var message = "<div class=panel-heading><h3 class=panel-title>Success</h3>" + resultObj.messages
		    			$('#savingBoardingDetailsModal  .panel').html(message);
		    			$('#savingBoardingDetailsModal  .panel').addClass('panel-success');
		    			$('#savingBoardingDetailsModal  .panel').removeClass('panel-danger');
		    		} else {
		    			var message = "<div class=panel-heading><h3 class=panel-title>Error</h3>" + resultObj.messages
		    			$('#savingBoardingDetailsModal  .panel').html(message);
		    			$('#savingBoardingDetailsModal  .panel').addClass('panel-danger');
		    			$('#savingBoardingDetailsModal  .panel').removeClass('panel-success');
		    		};
		    		$('#savingBoardingDetailsModal').modal('show');
					$("#initiatePes").removeClass('spinning');
		    	}
		    });
		});
	},

	this.listenForEditPesStatus = function(){
		$(document).on('click','.editPesStatus', function(e){
    		   var cnum = ($(e.target).data('cnum'));
    		   var notesid = ($(e.target).data('notesid'));
    		   var status  = ($(e.target).data('pesstatus'));
    		   $('#psm_notesid').val(notesid);
    		   $('#psm_cnum').val(cnum);
    		   $('#amendPesStatusModal').on('shown.bs.modal', { status: status}, function (e) {
        		   $('#psm_status').select2();
        		   $('#psm_status').val(e.data.status).trigger('change');
        		   $('#psm_detail').val('');
    			 })
      		   $('#amendPesStatusModal').modal('show');
			});
	},

	this.listenForSavePesStatus = function(){
		$('#psmForm').submit(function(e){
				var form = document.getElementById('psmForm');
				var formValid = form.checkValidity();
				if(formValid){
					var allDisabledFields = ($("input:disabled"));
					$(allDisabledFields).attr('disabled',false);
					var formData = $('#amendPesStatusModal form').serialize();
					console.log(formData);
					$(allDisabledFields).attr('disabled',true);
					$.ajax({
				    	url: "ajax/savePesStatus.php",
				    	data : formData,
				    	type: 'POST',
				    	success: function(result){
				    		console.log(result);
				    		personRecord.table.ajax.reload();
							$('#amendPesStatusModal').modal('hide');
				    	}
				    });

				};
				return false;
			});
	}


}

$( document ).ready(function() {
	var person = new personRecord();
    person.init();
});