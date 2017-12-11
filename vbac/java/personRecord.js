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
		var name = document.getElementById['NAME'];
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

                   var uid =  document.getElementById('person_uid');
                   if(typeof(uid) !== 'undefined'){ uid.value = person['uid'];};

                   $('#personDetails').show();
//
//                   var uid = document.forms.displayBpDetails.elements['person_uid'];
//                   if(typeof(uid) !== 'undefined'){ uid.value = person['uid'];};
//
//                   var phone = document.forms.displayBpDetails.elements['person_phone'];
//                   if(typeof(phone) !== 'undefined'){ phone.value = person['phone'];};

                   return person['name'];
                   }
            }
        };
        FacesTypeAhead.init(
        		document.getElementById('NAME'),
        		config
        		);

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

	}





}

$( document ).ready(function() {
	var person = new personRecord();
    person.init();
});