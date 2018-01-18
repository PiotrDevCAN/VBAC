/*
 *
 *
 *
 */

function personRecord() {

	var table;
	var dataTableElements;
	var currentXmlDoc;
	var spinner =  '<div id="overlay"><i class="fa fa-spinner fa-spin spin-big"></i></div>';

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

	this.listenForHasBpEntry = function(){
		$(document).on('change','#hasBpEntry', function(){
			console.log('clicked hasBpEntry');
			console.log(this);
			$('#notAnIbmer').toggle();
			$('#existingIbmer').toggle();
			$('#linkToPreBoarded').toggle();

			var currentHeading = $('#employeeResourceHeading').text();
			var newHeading = currentHeading=='Employee Details' ? 'Resource Details' : 'Employee Details';
			$('#employeeResourceHeading').text(newHeading);
		});
	},

	this.listenForLinkToPreBoarded = function(){
		$(document).on('select2:select','#person_preboarded', function(e){
			var data = e.params.data;
			if(data.id!=''){
				// They have selected an entry
				console.log(data.id);
				var allEnabled = $('form :enabled');
				console.log(allEnabled);
				$(allEnabled).attr('disabled',true);
				$("#saveBoarding").addClass('spinning');
				$.ajax({
			    	url: "ajax/prePopulateFromLink.php",
			    	type: 'POST',
			        data : {cnum:data.id},
			    	success: function(result){
						$("#saveBoarding").removeClass('spinning');
			    		console.log(result);
			    		var resultObj = JSON.parse(result);
			    		if(resultObj.success==true){
			    			console.log(resultObj.data);
			    			console.log(resultObj.data.CTB_RTB.trim());

			    			   var $radios = $('input:radio[name=CTB_RTB]');
			    			   $($radios).attr('disabled',false);
			    			   var button =  $radios.filter('[value=' + resultObj.data.CTB_RTB.trim() + ']');
			    			   $(button).prop('checked',true);
			    			   $(button).trigger('click');

			    			   var $radios = $('.accountOrganisation');
			    			   $($radios).attr('disabled',false);
			    			   var button = $radios.filter("[value='" + resultObj.data.TT_BAU.trim() + "']");
			    			   $(button).prop('checked',true);
			    			   $(button).trigger('click');

			    			   $('#FM_CNUM').val(resultObj.data.FM_CNUM.trim()).trigger('change');
			    			   $('#FM_CNUM').attr('disabled',false);

			    			   var openSeatNumber = resultObj.data.OPEN_SEAT_NUMBER;
			    			   if(openSeatNumber){
				    			   $('#open_seat').val(openSeatNumber.trim());
			    			   }
			    			   $('#open_seat').attr('disabled',false);

			    			   $('#lob').val(resultObj.data.LOB.trim()).trigger('change');
			    			   $('#lob').attr('disabled',false);

			    			   var workStream = resultObj.data.WORK_STREAM;
			    			   if(workStream){
			    				   $('#work_stream').val(workStream.trim()).trigger('change');
			    			   }


			    			   var roleOnAccount = resultObj.data.ROLE_ON_THE_ACCOUNT;
			    			   if(roleOnAccount){
			    				   $('#role_on_account').val(roleOnAccount.trim());
			    			   }
			    			   $('#role_on_account').attr('disabled',false);



			    		} else {

			    		};
			    	}
			    });


			};
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

		var urlOptions = "preferredidentity&jobresponsibilities&notesemail&uid&preferredfirstname&hrfirstname&sn&hrfamilyname&ismanager&phonemailnumber&employeetype&co&ibmloc";

		if(cnum.length == 9){
		    $.ajax({
//		    	url: "https://bluepages.ibm.com/BpHttpApisv3/slaphapi?ibmperson/(uid=" + cnum + ").search/byjson?" + urlOptions ,
//		    	url: "http://bluepages.ibm.com/BpHttpApisv3/wsapi?byCnum=" + cnum,
		    	url: "api/bluepages.php?ibmperson/(uid=" + cnum + ").search/byjson?" + urlOptions ,
		        type: 'GET',
		    	success: function(result){
		    		console.log(result);
		    		var personDetailsObj = JSON.parse(result);
		    		var attributes = personDetailsObj.search.entry[0].attribute;
		    		for(a=0;a<attributes.length;a++){
		    			var object = attributes[a];
		    			var value = object.value;
		    			var name = object.name;

		    			console.log("Name:" + name);
		    			console.log("Value:" + value);
		    			console.log(typeof(value));

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
		    			case 'hrfirstname':
			                   var fname =  document.getElementById('person_first_name');
			                   console.log(name.value);
			                   var firstName = value[0];
			                   if(typeof(fname) !== 'undefined'){
			                	   	initialLetter = firstName.substring(0,1).toUpperCase();
			                	   	restOfName    = firstName.substring(1).toLowerCase();
			                	   	capitalizedName = initialLetter + restOfName;
		                	   		fname.value = capitalizedName ;
		                	   };
		                	   console.log($(fname));
			                   break;
		    			case 'sn':
		    			case 'hrfamilyname':
			                   var lname =  document.getElementById('person_last_name');
			                   console.log(name + ":" + value);
			                   var lastName = value[0];
			                   if(typeof(lname) !== 'undefined'){
			                	   initialLetter = lastName.substring(0,1).toUpperCase();
			                	   restOfName    = lastName.substring(1).toLowerCase();
			                	   capitalizedName = initialLetter + restOfName;
			                	   lname.value = capitalizedName ;
			                   };
			                   console.log($(lname));
			                   break;
		    			case 'ismanager':
		    				   var isMgr =  document.getElementById('person_is_mgr');
		    				   console.log($(isMgr) + ":" + value);
			                   if(typeof(isMgr) !== 'undefined'){ isMgr.value = value ;};
				               break;
		    			case 'phonemailnumber':
		    				   var phone =  document.getElementById('person_phone');
		    				   console.log($(phone) + ":" + value);
			                   if(typeof(phone) !== 'undefined'){ phone.value = value ;};
				               break;
		    			case 'employeetype':
		    				   var employeeeType =  document.getElementById('person_employee_type');
		    				   console.log($(employeeeType) + ":" + value);
			                   if(typeof(employeeeType) !== 'undefined'){ employeeeType.value = value ;};
				               break;
		    			case 'co':
		    				   var country =  document.getElementById('person_country');
		    				   console.log($(country) + ":" + value);
			                   if(typeof(country) !== 'undefined'){ country.value = value ;};
				               break;
		    			case 'ibmloc':
		    				   var location =  document.getElementById('person_ibm_location');
		    				   console.log($(location) + ":" + value);
			                   if(typeof(location) !== 'undefined'){ location.value = value ;};
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
		// var workStream is created in PHP in the personRecord class and loaded to javascript using Javascript::buildSelectArray
		$(document).on('click','.accountOrganisation', function(){
			var accountOrganisation = $(this).val();
			var nullFirstEntry  = [''];
			for(i=0;i<workStream.length;i++){
				if(workStream[0][i]==accountOrganisation){
					var workStreamValues = nullFirstEntry.concat(workStream[i+1]);
				}
			}
			console.log($('#work_stream'));
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

			var currentWorkstream = $('#currentWorkstream').val();
			console.log(currentWorkstream);

			if(currentWorkstream!=''){
				console.log('changing to' + currentWorkstream);
				$('#work_stream').val(currentWorkstream); // Select the option with a value of currentWorkstream
				$('#work_stream').trigger('change');
			}

		});
	},

	this.listenForSaveBoarding = function(){
		$(document).on('click','#saveBoarding', function(){
			var person = new personRecord();
			person.saveBoarding('Save');
		});
	},

	this.listenforUpdateBoarding= function () {
		$(document).on('click','#updateBoarding', function(){
			var person = new personRecord();
			person.saveBoarding('Update');
		});
	},

	this.saveBoarding = function(mode){
		var form = $('#boardingForm');
		var formValid = form[0].checkValidity();
		if(formValid){
			$("#saveBoarding").addClass('spinning');
			var allDisabledFields = ($("input:disabled"));
			$(allDisabledFields).attr('disabled',false);
			var formData = form.serialize();
			formData += "&mode=" + mode;
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
		    			$('#person_uid').val(resultObj.cnum);
		    			var message = "<div class=panel-heading><h3 class=panel-title>Success</h3>" + resultObj.messages
		    			$('#savingBoardingDetailsModal  .panel').html(message);
		    			$('#savingBoardingDetailsModal  .panel').addClass('panel-success');
		    			$('#savingBoardingDetailsModal  .panel').removeClass('panel-danger');
			    		$('#boardingForm :input').attr('disabled',true);
			    		$('#saveBoarding').attr('disabled',true);
			    		$('#initiatePes').attr('disabled',false);
		    		} else {
		    			var message = "<div class=panel-heading><h3 class=panel-title>Error</h3>" + resultObj.messages
		    			$('#savingBoardingDetailsModal  .panel').html(message);
		    			$('#savingBoardingDetailsModal  .panel').addClass('panel-danger');
		    			$('#savingBoardingDetailsModal  .panel').removeClass('panel-success');
			    		$('#saveBoarding').attr('disabled',false);
			    		$('#initiatePes').attr('disabled',true);
		    		};
		    		$('#editPersonModal').modal('hide');
		    		$('#savingBoardingDetailsModal').modal('show');
		    		if(personRecord.table != "undefined") {
			    		personRecord.table.ajax.reload();
		    		}

		    	}
		    });
		}
	},


	this.initialisePersonTable = function(){
	    $.ajax({
	    	url: "ajax/createHtmlForPersonTable.php",
	    	type: 'POST',
	    	success: function(result){
	    		var Person = new personRecord();
	    		$('#personDatabaseDiv').html(result);
	    		console.log($('#personTable'));
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
	    console.log($('#personTable'));
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
			console.log(e);
			personRecord.table.columns().visible(false,false);
			personRecord.table.columns([5,21,22,23,24,25]).visible(true);
			console.log(personRecord.table);
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

	this.listenForReportAction = function(){
		$(document).on('click','#reportAction', function(e){
			personRecord.table.columns().visible(false,false);
			personRecord.table.columns([0,1,5,9,25]).visible(true);
			personRecord.table.order([5,'asc']).draw();
			});
	},

	this.listenForReportReset = function(){
		$(document).on('click','#reportReset', function(e){
			personRecord.table.columns().visible(true);
			personRecord.table.order([5,"asc"]).draw();
			});
	},

	this.listenForInitiatePesFromBoarding = function(){
		$(document).on('click','.btnPesInitiate', function(e){
			console.log('initiatePes PES from boarding');
			$("#initiatePes").addClass('spinning');
			var cnum = $('#person_uid').val();
			var person = new personRecord;
			person.initiatePes(cnum);
		});
	},

	this.listenForInitiatePesFromPortal = function(){
		$(document).on('click','.btnPesInitiate', function(e){
			console.log('initiatePes PES from Portal');
			console.log(this);
			var cnum = $(this).data('cnum');
			var person = new personRecord;
			person.initiatePes(cnum);
		});
	},

	this.initiatePes = function(cnum){
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
				personRecord.table.ajax.reload();
	    	}
	    });
	},

	this.listenForEditPerson = function(){
		$(document).on('click','.btnEditPerson', function(e){
    		   var cnum = ($(e.target).data('cnum'));
    		   var spinner =  '<div id="overlay"><i class="fa fa-spinner fa-spin spin-big"></i></div>';
    		   $('#editPersonModal .modal-body').html(spinner);
    		   $('#editPersonModal').modal('show');
    		   $.ajax({
    		    	url: "ajax/getEditPersonModalBody.php",
    		    	data : {cnum:cnum},
    		    	type: 'POST',
    		    	success: function(result){
    		    		var resultObj = JSON.parse(result);
    		    		console.log(resultObj);
       		    		if(!resultObj.messages){
        		    		$('#editPersonModal .modal-body').html(resultObj.body);
        		    		var person = new personRecord();
        		    	    person.initialisePersonFormSelect2();
        		    		var accountOrganisation = resultObj.accountOrganisation;
        		    		console.log(accountOrganisation);
    		    		    console.log($('.accountOrganisation'));
    		    		    console.log($('.accountOrganisation')[0]);
        		    		if(accountOrganisation=='T&T'){
        		    			$('.accountOrganisation')[0].click();
        		    		    console.log($('.accountOrganisation'));
        		    		    console.log($('.accountOrganisation')[0]);
        		    		}
        		    		if(accountOrganisation=='BAU'){
        		    			$('.accountOrganisation')[1].click();
        		    		}
    		    		} else {
        		    		$('#editPersonModal .modal-body').html(resultObj.messages);
    		    		}

    		    	}
    		    });
			});
	},

	this.listenForToggleFmFlag = function(){
		$(document).on('click','.btnSetFmFlag', function(e){
			var cnum = $(this).data('cnum');
			var notesid = $(this).data('notesid');
			var flag = $(this).data('fmflag');
			var message = "<p>For:<b>" + notesid + "</b></p>";
			    message += "<p>To:<b>" + flag + "</b></p>";
			    message += "<input id='cFmCnum' name='cnum' value='" + cnum + "' type='hidden' >";
			    message += "<input id='cFmNotesid' name='notesid' value='" + notesid + "'  type='hidden' >";
			    message += "<input id='cFmFlag' name='flag' value='" + flag + "'  type='hidden' >";

			$('#confirmChangeFmFlagModal .modal-body').html(message);
			$('#confirmFmStatusChange').attr('disabled',false);
			$('#confirmChangeFmFlagModal').modal('show');
			return false;
		});
	},

	this.listenForConfirmFmFlag = function(){
		$('#confirmFmFlagChangeForm').submit(function(e){
			console.log('submit hit');
			var form = document.getElementById('confirmFmFlagChangeForm');
			var formValid = form.checkValidity();
			if(formValid){
				var allDisabledFields = ($("input:disabled"));
				$(allDisabledFields).attr('disabled',false);
				var formData = $('#confirmFmFlagChangeForm').serialize();
				$(allDisabledFields).attr('disabled',true);
				$('#confirmFmStatusChange').attr('disabled',true);
	 		    $.ajax({
			    	url: "ajax/changeFmFlag.php",
			    	data : formData,
			    	type: 'POST',
			    	success: function(result){
	  		    		personRecord.table.ajax.reload();
			    		var resultObj = JSON.parse(result);
			    		console.log(resultObj);
	  		    		if(!resultObj.messages){
	  		    			$('#confirmChangeFmFlagModal .modal-body').html(resultObj.body);
			    		} else {
			    			$('#confirmChangeFmFlagModal .modal-body').html(resultObj.messages);
			    		}
			    	}
			    });
			};
			return false;
		});

	},




	this.listenForEditPesStatus = function(){
		$(document).on('click','.btnPesStatus', function(e){
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
		$(this).attr('disabled',true);
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
	},

	this.initialisePersonFormSelect2 = function(){
		console.log($('.select2'));
	 	$('.select2').select2();
	}


}

$( document ).ready(function() {
	var person = new personRecord();
    person.init();
});