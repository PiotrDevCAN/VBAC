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
  var boardingFormEnabledInputs;



  this.init = function(){
    console.log('+++ Function +++ personRecord.init');
    console.log('--- Function --- personRecord.init');
  },

  this.listenForName = function(){
	 	$('.typeahead').bind('typeahead:select', function(ev, suggestion) {
	 		$('.tt-menu').hide();
	 		$('#person_notesid').val(suggestion.notesEmail);
	 		$('#person_serial').val(suggestion.cnum).attr('disabled','disabled');
	 		$('#person_bio').val(suggestion.role);
	 		$('#person_intranet').val(suggestion.mail);
	 		console.log(suggestion.mail);

	 		var newCnum = suggestion.cnum;
	 		console.log(newCnum);
	 		var allreadyExists = ($.inArray(newCnum, knownCnum) >= 0 );

	 		console.log(allreadyExists);
	 		if(allreadyExists){ // comes back with Position in array(true) or false is it's NOT in the array.
	 			$('#saveBoarding').attr('disabled',true);
	 			$('#person_name').css("background-color","LightPink");
	 			alert('Person already defined to VBAC');
	 			return false;
	 		} else {
	 			$('#person_name').css("background-color","LightGreen");
	 			$('#saveBoarding').attr('disabled',false);
	 		}

	 		console.log(suggestion.cnum);

	 		var personObj = new personRecord();
	 		personObj.fetchBluepagesDetailsForCnum(suggestion.cnum);

	 		$('#personDetails').show();
	 		$('#person_contractor_id').select2();
	 		$('#person_functionalMgr').select2();
		});

  },


  this.listenForEmail = function(){
	  $(document).on('focusout','#resource_email',function(){
		  var newEmail = $('#resource_email').val();
		  console.log(newEmail);
		  var allreadyExists = ($.inArray(newEmail, knownEmail) >= 0 );
          console.log(allreadyExists);
          if(allreadyExists){ // comes back with Position in array(true) or false is it's NOT in the array.
            $('#saveBoarding').attr('disabled',true);
            $('#resource_email').css("background-color","LightPink");
            alert('Email address already defined to VBAC');
            return false;
          } else {
            $('#resource_email').css("background-color","LightGreen");
            $('#saveBoarding').attr('disabled',false);
          }
	  });

  },

  this.listenForOnBoarding = function() {
    $(document).on('click','#onBoardingBtn', function(){
      window.open('pb_onboard.php', '_self');
    });
  },

  this.listenForOffBoarding = function(){
    $(document).on('click','#offBoardingBtn', function(){
        window.open('pb_offboard.php', '_self');
    	// $('#selectOffboarderModal').modal('show');

    });

    $('#selectOffboarderModal').on('shown.bs.modal',function(){
 	   $.ajax({
		   url: "ajax/populateSelectOffboarder.php",
	       type: 'GET',
	       success: function(result){

	       }
	   });

    })


  },




  this.populateSelectOffboarderModal = function(){

  }


  this.listenForOffBoardingCompleted = function(){
	$(document).on('click','.btnOffboarded', function(e){
	   console.log(this);
		var data = $(this).data();
		console.log(data);
	   $.ajax({
		   url: "ajax/completeOffboarding.php",
	       type: 'POST',
	       data : {cnum:data.cnum},
	       success: function(result){
	    	   personRecord.table.ajax.reload();
	       }
	   });
	});
  }

  this.listenForBtnOffboarding = function(){
		$(document).on('click','.btnOffboarding', function(e){
		   console.log(this);
			var data = $(this).data();
			console.log(data);
		   $.ajax({
			   url: "ajax/initiateOffboardingFromPortal.php",
		       type: 'POST',
		       data : {cnum:data.cnum},
		       success: function(result){
		           console.log(result);
		           var resultObj = JSON.parse(result);
		           if(resultObj.initiated==true){
		        	   var message = "<div class=panel-heading><h3 class=panel-title>Success</h3>";
		               message += "<br/><h4>Offboarding has been initiated</h4></br>";
		               $('#confirmOffboardingModal  .panel').html(message);
		               $('#confirmOffboardingModal  .panel').addClass('panel-success');
		               $('#confirmOffboardingModal  .panel').removeClass('panel-danger');
		             } else {
			           var message = "<div class=panel-heading><h3 class=panel-title>Success</h3>";
			           message += "<br/><h4>Offboarding has <b>NOT</b> been initiated</h4></br>";
		               $('#confirmOffboardingModal  .panel').html(message);
		               $('#confirmOffboardingModal  .panel').addClass('panel-danger');
		               $('#confirmOffboardingModal  .panel').removeClass('panel-success');
		             };
                	 $('#confirmOffboardingModal').modal('show');
  		    	   personRecord.table.ajax.reload();
		       }
		   });
		});
  },


  this.listenForHasBpEntry = function(){
    $(document).on('change','#hasBpEntry', function(){
      console.log('clicked hasBpEntry');

      console.log($('#hasBpEntry').is(':checked'));



      console.log(this);
      $('#notAnIbmer').toggle();
      $('#existingIbmer').toggle();
      $('#linkToPreBoarded').toggle();

      if($('#notAnIbmer').is(":visible")){
    	$('.employeeTypeRadioBtn input[type=radio]').prop('required',true);
    	$('#open_seat').attr('placeholder','IBM Hiring Number');
        $('#notAnIbmer :input').attr('required',true);
        $('#existingIbmer :input').attr('required',false);
        $('#saveBoarding').attr('disabled',false);
        $('#resource_country').select2('destroy');
        $('#resource_country').select2();
        $('#person_preboarded').val('').trigger('change');  // incase they already selected a pre-boarder - we need to clear this field.
      } else {
    	$('.employeeTypeRadioBtn input[type=radio]').removeAttr('required');
    	$('#open_seat').attr('placeholder','Open Seat Number');
        $('#notAnIbmer :input').attr('required',false);
        $('#existingIbmer :input').attr('required',true);
      }
      var currentHeading = $('#employeeResourceHeading').text();
      var newHeading = currentHeading=='Employee Details' ? 'Resource Details' : 'Employee Details';
      $('#employeeResourceHeading').text(newHeading);

  	console.log($('.employeeTypeRadioBtn input[type=radio]'));


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
        $('#initiatePes').hide();
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
                   $(allEnabled).attr('disabled',false);
                   var $radios = $('input:radio[name=CTB_RTB]');
                   $($radios).attr('disabled',false);

                   if(resultObj.data.CTB_RTB != null){
                     var button =  $radios.filter('[value=' + resultObj.data.CTB_RTB.trim() + ']');
                     $(button).prop('checked',true);
                     $(button).trigger('click');
                   }

                   var $radios = $('.accountOrganisation');
                   $($radios).attr('disabled',false);

                   if(resultObj.data.TT_BAU != null){
                     var button = $radios.filter("[value='" + resultObj.data.TT_BAU.trim() + "']");
                     $(button).prop('checked',true);
                     $(button).trigger('click');
                   }

                   if(resultObj.data.CT_ID_REQUIRED != null){
                     if(resultObj.data.CT_ID_REQUIRED.trim().toUpperCase().substring(0,1)=='Y'){
                       var contractorIdReq = 'yes';
                     } else {
                       var contractorIdReq = 'no';
                     }
                   }else {
                     var contractorIdReq = 'no';
                   }
                   $('#person_contractor_id_required').attr('disabled',false);
                   $('#person_contractor_id_required').val(contractorIdReq).trigger('change');

                   if(resultObj.data.FM_CNUM != null){
                     $('#FM_CNUM').attr('disabled',false);
                     $('#FM_CNUM').val(resultObj.data.FM_CNUM.trim()).trigger('change');
                   }

                   if(resultObj.data.OPEN_SEAT_NUMBER != null ){
                     var openSeatNumber = resultObj.data.OPEN_SEAT_NUMBER;
                     $('#open_seat').attr('disabled',false);
                     if(openSeatNumber){
                       $('#open_seat').val(openSeatNumber.trim());
                     }
                   }

                   if(resultObj.data.CIO_ALIGNMENT != null ){
                     var cioAlignment = resultObj.data.CIO_ALIGNMENT.trim();
                     $('#cioAlignment').attr('disabled',false);
                     if(cioAlignment){
                       console.log(cioAlignment);
                       console.log( $('#cioAlignment'));
                       $('#cioAlignment').val(cioAlignment).trigger('change');
                     }
                   }

                   if(resultObj.data.LOB != null){
                     $('#lob').attr('disabled',false);
                     $('#lob').val(resultObj.data.LOB.trim()).trigger('change');
                   }

                   var workStream = resultObj.data.WORK_STREAM;
                   if(workStream){
                     $('#work_stream').val(workStream.trim()).trigger('change');
                   }


                   var roleOnAccount = resultObj.data.ROLE_ON_THE_ACCOUNT;
                   if(roleOnAccount){
                     $('#role_on_account').val(roleOnAccount.trim());
                   }
                   $('#role_on_account').attr('disabled',false);


                   var sDate = resultObj.data.START_DATE;
                   $('#start_date').attr('disabled',false);
                   if(sDate){
                     startPicker.setDate(sDate);
                   }

                   var eDate = resultObj.data.PROJECTED_END_DATE;
                   $('#end_date').attr('disabled',false);
                   if(eDate){
                     endPicker.setDate(eDate);
                   }

                   var pesDateReq = resultObj.data.PES_DATE_REQUESTED;
                   if(pesDateReq){
                     $('#pes_date_requested').val(pesDateReq.trim());
                   }

                   var pesDateResp = resultObj.data.PES_DATE_RESPONDED;
                   if(pesDateResp){
                     $('#pes_date_responded').val(pesDateResp.trim());
                   }

                   var pesRequestor = resultObj.data.PES_REQUESTOR;
                   if(pesRequestor){
                     $('#pes_requestor').val(pesRequestor.trim());
                   }

                   var pesStatus = resultObj.data.PES_STATUS;
                   if(pesStatus){
                     $('#pes_status').val(pesStatus.trim());
                   }

                   var pesStatusDet = resultObj.data.PES_STATUS_DETAILS;
                   if(pesStatusDet){
                     $('#pes_status_details').val(pesStatusDet.trim());
                   }

                   $(allEnabled).attr('disabled',false); // open up the fields we'd disabled.
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
      $('#saveBoarding').attr('disabled',true);

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
             $('#saveBoarding').attr('disabled',false);
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

  this.listenForCtbRtb = function(){
    $(document).on('click','.ctbRtb', function(){
      var ctbRtb = $(this).val();
      $('#cioAlignment').select2('destroy');
      if(ctbRtb=='CTB'){
        $('#cioAlignment').select2({
          placeholder:"Select CIO Alignment",
          })
          .attr('disabled',false)
          .attr('required',true);
      } else {
        $('#cioAlignment').select2({
          placeholder:"Not required",
          })
          .attr('disabled',true)
          .attr('required',false);
      }
    });
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
    console.log('saveBoarding mode:' + mode);
    $("#saveBoarding").addClass('spinning');
    $("#updateBoarding").addClass('spinning');
    var ibmer = $('#hasBpEntry').is(':checked');
    var form = $('#boardingForm');
    var formValid = form[0].checkValidity();
    if(formValid){
      personRecord.boardingFormEnabledInputs = ($("input:enabled"));
      var allDisabledFields = ($("input:disabled"));
      $(allDisabledFields).attr('disabled',false);
      var formData = form.serialize();
      formData += "&mode=" + mode + "&boarding=" + ibmer;
      $(allDisabledFields).attr('disabled',true);
      console.log(formData);
        $.ajax({
          url: "ajax/saveBoardingForm.php",
          type: 'POST',
            data : formData,
          success: function(result){
          $("#saveBoarding").removeClass('spinning');
          $('#updateBoarding').removeClass('spinning');
            console.log(result);
            var resultObj = JSON.parse(result);
            if(resultObj.success==true){
              $('#person_uid').val(resultObj.cnum);
              var message = "<div class=panel-heading><h3 class=panel-title>Success</h3>" + resultObj.messages
              message += resultObj.offboarding ? "<br/><h4>Offboarding has been initiated</h4></br>" : '';
              $('#savingBoardingDetailsModal  .panel').html(message);

              if(resultObj.offboarding){
                  $('#savingBoardingDetailsModal  .panel').removeClass('panel-success');
                  $('#savingBoardingDetailsModal  .panel').removeClass('panel-danger');
                  $('#savingBoardingDetailsModal  .panel').addClass('panel-warning');
              } else {
                  $('#savingBoardingDetailsModal  .panel').addClass('panel-success');
                  $('#savingBoardingDetailsModal  .panel').removeClass('panel-danger');
                  $('#savingBoardingDetailsModal  .panel').removeClass('panel-warning');
              }
              $('#boardingForm :input').attr('disabled',true);
              $('#saveBoarding').attr('disabled',true);
              $('#initiatePes').attr('disabled',false);
            } else {
              var message = "<div class=panel-heading><h3 class=panel-title>Error : Please inform vBAC Support</h3>" + resultObj.messages
              $('#savingBoardingDetailsModal  .panel').html(message);
              $('#savingBoardingDetailsModal  .panel').addClass('panel-danger');
              $('#savingBoardingDetailsModal  .panel').removeClass('panel-success');
              $('#savingBoardingDetailsModal  .panel').removeClass('panel-warning');
              $('#saveBoarding').attr('disabled',false);
              $('#initiatePes').attr('disabled',true);
            };
            $('#editPersonModal').modal('hide');
            $('#savingBoardingDetailsModal').modal('show');
            console.log(typeof(personRecord.table));
            if(typeof(personRecord.table) != "undefined") {
              personRecord.table.ajax.reload();
            }

          }
        });
    } else {
      $("#saveBoarding").removeClass('spinning');
      $("#updateBoarding").removeClass('spinning');
      console.log('invalid fields follow');
      console.log($(form).find( ":invalid" ));
    }
  },


  this.initialisePersonTable = function(){
      $.ajax({
        url: "ajax/createHtmlForPersonTable.php",
        type: 'POST',
        success: function(result){
          var Person = new personRecord();
          $('#personDatabaseDiv').html(result);
          $('#assetRequestsDatatablesDiv').html(result);
          Person.initialiseDataTable();
        }
      });

  },

  this.initialiseDataTable = function(){
      // Setup - add a text input to each footer cell
      $('#personTable tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input type="text" id="footer'+ title + '" placeholder="Search '+title+'" />' );
      } );
    // DataTable
      personRecord.table = $('#personTable').DataTable({
        ajax: {
              url: 'ajax/populatePersonDatatable.php',
              type: 'GET',
          }	,
//	        CNUM         0
//	        Email        4
//	        Notes ID     5
//	        fm_cnum		 8
//	        PES status   25

          "columns": [
                      { "data": "CNUM" , "defaultContent": "" },
                      { "data": "OPEN_SEAT_NUMBER" ,"defaultContent": "" },
                      { "data": "FIRST_NAME"       ,"defaultContent": "<i>unknown</i>"},
                      { "data": "LAST_NAME", "defaultContent": "<i>unknown</i>" },
                      { "data": "EMAIL_ADDRESS", "defaultContent": "<i>unknown</i>" },
                      { "data": "NOTES_ID", "defaultContent": "<i>unknown</i>" },
                      { "data": "LBG_EMAIL", "defaultContent": "<i>unknown</i>" },
                      { "data": "EMPLOYEE_TYPE", "defaultContent": "" },
                      { "data": "FM_CNUM", "defaultContent": "" },
                      { "data": "FM_MANAGER_FLAG", "defaultContent": "" },
                      { "data": "CTB_RTB", "defaultContent": "" },
                      { "data": "TT_BAU", "defaultContent": "" },
                      { "data": "LOB", "defaultContent": "" },
                      { "data": "ROLE_ON_THE_ACCOUNT", "defaultContent": "" },
                      { "data": "ROLE_TECHNOLOGY", "defaultContent": "" },
                      { "data": "START_DATE", "defaultContent": "" },
                      { "data": "PROJECTED_END_DATE", "defaultContent": "" },
                      { "data": "COUNTRY", "defaultContent": ""},
                      { "data": "IBM_BASE_LOCATION", "defaultContent": "" },
                      { "data": "LBG_LOCATION" , "defaultContent": ""},
                      { "data": "OFFBOARDED_DATE" , "defaultContent": ""},
                      { "data": "PES_DATE_REQUESTED" , "defaultContent": ""},
                      { "data": "PES_REQUESTOR", "defaultContent": "" },
                      { "data": "PES_DATE_RESPONDED", "defaultContent": "" },
                      { "data": "PES_STATUS_DETAILS", "defaultContent": "" },
                      { "data": "PES_STATUS", "defaultContent": "" },
                      { "data": "REVALIDATION_DATE_FIELD", "defaultContent": "" },
                      { "data": "REVALIDATION_STATUS", "defaultContent": "" },
                      { "data": "CBN_DATE_FIELD", "defaultContent": "" },
                      { "data": "CBN_STATUS", "defaultContent": "" },
                      { "data": "WORK_STREAM", "defaultContent": "" },
                      { "data": "CT_ID_REQUIRED" , "defaultContent": ""},
                      { "data": "CT_ID", "defaultContent": "" },
                      { "data": "CIO_ALIGNMENT", "defaultContent": "" },
                      { "data": "PRE_BOARDED", "defaultContent": "" },
                      { "data": "SECURITY_EDUCATION", "defaultContent": "" },
                  ],
          columnDefs: [
                         { "visible": false, "targets": [1,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,26,27,28,29,30,31,32,33,34] }
                  ] ,
//	        colReorder: {
//	            order: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34]
//	        },
          order: [[ 5, "asc" ]],
          autoWidth: false,
          deferRender: true,
          responsive: false,
          // scrollX: true,
          processing: true,
          responsive: true,
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
    	$('#portalTitle').text('Person Portal - PES Report');
    	$.fn.dataTableExt.afnFiltering.pop();
    	personRecord.table.columns().visible(false,false);
    	personRecord.table.columns([5,21,22,23,24,25,34]).visible(true);
    	console.log(personRecord.table);
    	personRecord.table.order([21,'desc'],[5,"asc"]).draw();
      });
  },

  this.listenForReportPerson = function(){
    $(document).on('click','#reportPerson', function(e){
      personRecord.table.columns([0,1,2,3,4,5,6,7,8,9,10,11,12,13,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34]).visible(true,false);
      personRecord.table.columns([2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,24]).visible(true);
      personRecord.table.columns.draw();
      });
  },

  this.listenForReportAction = function(){
    $(document).on('click','#reportAction', function(e){
    	$('#portalTitle').text('Person Portal - Action Mode');
    	$.fn.dataTableExt.afnFiltering.pop();
    	personRecord.table.columns().visible(false,false);
    	personRecord.table.columns([0,1,5,9,25]).visible(true);
    	personRecord.table.order([5,'asc']).draw();
      });
  },

  this.listenForReportRevalidation = function(){
    $(document).on('click','#reportRevalidation', function(e){
    	$('#portalTitle').text('Person Portal - Revalidation Report');
    	$.fn.dataTableExt.afnFiltering.pop();
    	personRecord.table.columns().visible(false,false);
    	personRecord.table.columns([5,8,15,16,26,27]).visible(true);
    	personRecord.table.search('').order([5,'asc']).draw();
    });
  },

  this.listenForReportOffboarding = function(){
	    $(document).on('click','#reportOffboarding', function(e){
	    	$('#portalTitle').text('Person Portal - Offboarding Report');
	    	$.fn.dataTableExt.afnFiltering.pop();
	        $.fn.dataTableExt.afnFiltering.push(
	    		    function(oSettings, aData, iDataIndex){
	    		    	var dat = new Date();
	    		    	dat.setDate(dat.getDate() +31);

	    		    	var month = "00".concat(dat.getMonth()+1).substr(-2);
	    		    	var day   = "00".concat(dat.getDate()).substr(-2);
	    		    	var thirtyDaysHence = dat.getFullYear() + "-" + month + "-" + day;
	    		        var dateEnd = thirtyDaysHence;
	    		        // aData represents the table structure as an array of columns, so the script access the date value
	    		        // in the first column of the table via aData[0]
	    		        var evalDate= aData[16];
	    		        var revalidationStatus = aData[27];

	    		        if (evalDate != '' && evalDate != '2000-01-01' &&  evalDate <= dateEnd && (revalidationStatus.trim() != 'preboarder' && revalidationStatus.trim() != 'offboarded')) {
	    		            return true;
	    		        }
	    		        else if(revalidationStatus.trim() == 'leaver' || revalidationStatus.trim() == 'offboarding'  ){
	    		        	return true;

	    		        } else {
	    		            return false;
	    		        }
	    		});

	      personRecord.table.columns().visible(false,false);
	      personRecord.table.columns([5,8,11,12,16,27]).visible(true);
	      personRecord.table.order([16,'asc'],[5,'asc']);

	      personRecord.table.draw();

//	      personRecord.table.column(27).data().each().function(){console.log(this)});
//	      $.fn.dataTableExt.afnFiltering.pop(); - if we pop off here - then when we sort on a column all the rows are back.
	      });
	  },

  this.listenForReportReset = function(){
    $(document).on('click','#reportReset', function(e){
    	$('#portalTitle').text('Person Portal');
    	$.fn.dataTableExt.afnFiltering.pop();
    	personRecord.table.columns().visible(false,false);
    	personRecord.table.columns([0,1,2,3,4,25]).visible(true);
    	personRecord.table.search('').order([5,"asc"]).draw();
    });
  },


  this.listenForReportAll = function(){
    $(document).on('click','#reportAll', function(e){
    	$('#portalTitle').text('Person Portal - All Columns');
    	$.fn.dataTableExt.afnFiltering.pop();
    	personRecord.table.columns().visible(true);
    	personRecord.table.columns().search('');
    	personRecord.table.order([5,"asc"]).draw();
      });
  },


  this.listenForReportReload = function(){
    $(document).on('click','#reportReload', function(e){
    	$('#portalTitle').text('Person Portal');
    	$.fn.dataTableExt.afnFiltering.pop();
    	personRecord.table.ajax.reload();
      });
  },

  this.listenForInitiatePesFromBoarding = function(){
    $(document).on('click','.btnPesInitiate', function(e){
      console.log('initiatePes PES from boarding');
      $(".btnPesInitiate").addClass('spinning');
      var cnum = $('#person_uid').val();
      var person = new personRecord;
      person.initiatePes(cnum);
    });
  },

  this.listenForInitiatePesFromPortal = function(){
    $(document).on('click','.btnPesInitiate', function(e){
    	$('#portalTitle').text('Person Portal - PES Report');
    	console.log('initiatePes PES from Portal');
    	$(".btnPesInitiate").addClass('spinning');
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
            var boardingForm = $('#boardingForm');
            console.log(boardingForm);

            console.log(personRecord.table);

            // location.reload();
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
        $(".btnPesInitiate").removeClass('spinning');
        $('#initiatePes').attr('disabled',true);
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
                $('.employeeTypeRadioBtn input[type=radio]').removeAttr('required');

                if(!resultObj.messages){
                    $('#editPersonModal .modal-body').html(resultObj.body);
                    var person = new personRecord();
                    person.initialisePersonFormSelect2();
                    $('#person_intranet').attr('disabled',false);
                    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
                    var accountOrganisation = resultObj.accountOrganisation;
                    if(accountOrganisation=='T&T'){
                      $('.accountOrganisation')[0].click();
                    }
                    if(accountOrganisation=='BAU'){
                      $('.accountOrganisation')[1].click();
                    }
                    var ctbRtb = resultObj.ctbRtb;
                    switch(ctbRtb){
                    case 'CTB':
                      $('.ctbRtb')[0].click();
                      break;
                    case 'RTB':
                      $('.ctbRtb')[1].click();
                      break;
                    default:
                      $('.ctbRtb')[2].click();
                      break;
                    }
                    person.initialiseStartEndDate();
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
           var email = ($(e.target).data('email'));

           console.log(notesid);
           console.log(email);

           notesid = notesid.trim() != "" ? notesid : email;

           console.log(notesid);

           var status  = ($(e.target).data('pesstatus'));
           $('#psm_notesid').val(notesid);
           $('#psm_cnum').val(cnum);
           $('#amendPesStatusModal').on('shown.bs.modal', { status: status}, function (e) {
               $('#psm_status').select2();
               $('#psm_status').val(e.data.status).trigger('change');
               $('#psm_detail').val('');
               $('#pes_date').datepicker({ dateFormat: 'dd M yy',
            	   						   altField: '#pes_date_db2',
               							   altFormat: 'yy-mm-dd' ,
               							   maxDate:0 }
               							  );
           });
             $('#amendPesStatusModal').on('hide.bs.modal', function (e) {
//            	 $("#pes_date").datepicker( "destroy" );
//            	 $("#pes_date").removeClass("hasDatepicker").removeAttr('id');


             });
             $('#amendPesStatusModal').modal('show');
      });
  },

  this.listenForSavePesStatus = function(){
    $(this).attr('disabled',true);
    $('#psmForm').submit(function(e){
    	$('#savePesStatus').attr('disabled',true);
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
                $('#savePesStatus').attr('disabled',false);
                personRecord.table.ajax.reload();
              $('#amendPesStatusModal').modal('hide');
              }
            });

        };
        return false;
      });
  },


  this.listenForReportSave = function(){
	  $(document).on('click','#reportSave', function(e){
		  var settings = personRecord.table.columns().visible().join(', ');
		  $('#saveReportModal').modal('show');
		  var searchBar = [];
		  $('#personTable tfoot th').each( function () {
			  var inputField = $(this).children()[0]
			  var placeHolder = $(inputField).attr('placeholder');
			  var searchValue = $(inputField).val();
			  var searchObject = {placeHolder:placeHolder , value:searchValue };
			  searchBar.push(searchObject);
		  });
		  var settingsJson = {settings:settings, searchBar:searchBar};
		  $('#reportSettings').val(JSON.stringify(settingsJson));
	  });
  },

  this.listenForReportSaveConfirm = function(){
	  $(document).on('click','#reportSaveConfirm', function(e){
		  $('#saveReportModal').modal('hide');
		  var form =  $('#reportSaveForm').serialize();
		  console.log(form);

	  });
  },


  this.listenForEmployeeTypeRadioBtn = function(){
	  $(document).on('click','.employeeTypeRadioBtn', function(e){
		  var employeeType = $('input[name=employeeType]:checked').val();
		  $('#resource_employee_type').val(employeeType);
	  });
  },



  this.initialisePersonFormSelect2 = function(){
    console.log($('.select2'));
     $('.select2').select2();
  },


  this.initialiseStartEndDate = function(){

      $('#start_date').datepicker({ dateFormat: 'dd M yy',
			   altField: '#start_date_db2',
			   altFormat: 'yy-mm-dd' ,
			   maxDate: +100,
		       onSelect: function( selectedDate ) {
		            $( "#end_date" ).datepicker( "option", "minDate", selectedDate );}
      	});

      var startDate = $('#start_date').datepicker('getDate');

      $('#end_date').datepicker({ dateFormat: 'dd M yy',
			   altField: '#end_date_db2',
			   altFormat: 'yy-mm-dd',
			   minDate: startDate }
			  );
  }


}

$( document ).ready(function() {
  var person = new personRecord();
    person.init();
});
