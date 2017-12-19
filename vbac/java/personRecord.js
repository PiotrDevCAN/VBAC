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
		console.log($(name));

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
                   console.log(uid);
                   if(typeof(uid) !== 'undefined'){ uid.value = person['uid'];};
                   $('#person_serial').attr('disabled','disabled');

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
		$(document).on('keyup change',function(e){
			var cnum = $(e.target).val();
			console.log($(e.target).val());
			if(cnum.length == 9){
			    $.ajax({
			    	url: "https://bluepages.ibm.com/BpHttpApisv3/slaphapi?ibmperson/(uid=" + cnum + ").search/byjson",
			        type: 'GET',
			    	success: function(result){
			    		console.log('success');
			    		console.log(result);
			    		var personDetailsObj = JSON.parse(result);
			    		console.log(personDetailsObj);

			    		console.log(personDetailsObj.search.entry[0]);
			    		var attributes = personDetailsObj.search.entry[0].attribute;
			    		console.log(attributes);

			    		console.log(attributes.length);

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
				                   var notesId =  document.getElementById('person_notesid');
				                   if(typeof(notesId) !== 'undefined'){ notesId.value = value;};
			                   break;
			    			case 'uid':
				                   var uid =  document.getElementById('person_uid');
				                   if(typeof(uid) !== 'undefined'){ uid.value = value;};
				                   break;
			    			case 'preferredfirstname':
				                   var name =  document.getElementById('NAME');
				                   if(typeof(name) !== 'undefined'){ name.value = value;};
				                   $('#NAME').attr('disabled','disabled');
				                   break;
			    			case 'sn':
				                   var name =  document.getElementById('NAME');
				                   if(typeof(name) !== 'undefined'){ name.value = name.value + " " + value ;};
				                   $('#NAME').attr('disable','disabled');
				                   break;
			    			default:
			    				console.log(name + ":" + value);
			    			}
			    		}
	                   $('#personDetails').show();
	                   $('#NAME').attr('disable',true);

			    	},
			        error: function (xhr, status) {
			            // handle errors
			        	console.log('error');
			        	console.log(xhr);
			        	console.log(status);
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