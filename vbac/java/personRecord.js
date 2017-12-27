/*
 *
 *
 *
 */

function personRecord() {

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
		    	url: "https://bluepages.ibm.com/BpHttpApisv3/slaphapi?ibmperson/(uid=" + cnum + ").search/byjson",
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
			console.log(formValid);
			if(formValid){
				var allDisabledFields = ($("input:disabled"));
				$(allDisabledFields).attr('disabled',false);
				var formData = form.serialize();
				$(allDisabledFields).attr('disabled',true);
			    $.ajax({
			    	url: "ajax/saveBoardingForm.php",
			    	type: 'POST',
			        data : formData,
			    	success: function(result){
			    		console.log(result);
			    		var resultObj = JSON.parse(result);
			    		$('#savingBoardingDetailsModal  .modal-body').html(resultObj.messages);
			    		$('#savingBoardingDetailsModal').modal('show');
			    		if(resultObj.success==true){
			    			$('#savingBoardingDetailsModal  .modal-body').addClass('bg-success');
			    			$('#savingBoardingDetailsModal  .modal-body').removeClass('bg-danger');
			    		} else {
			    			$('#savingBoardingDetailsModal  .modal-body').addClass('bg-danger');
			    			$('#savingBoardingDetailsModal  .modal-body').removeClass('bg-success');
			    		}
			    		$('#boardingForm')[0].reset();
			    	}
			    });
			}
		});

	}
}

$( document ).ready(function() {
	var person = new personRecord();
    person.init();
});