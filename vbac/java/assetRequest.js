/*
 *
 *
 *
 */
var selectedAssets = [];

function sleep(ms) {
	  return new Promise(resolve => setTimeout(resolve, ms));
	}


	async function demo() {
	  	  console.log('Taking a break...');
	  	  await sleep(2000);
	  	  console.log('Two second later');
	  	}



function assetRequest() {
	var table;

  this.init = function(){
    console.log('+++ Function +++ assetRequest.init');
    console.log('--- Function --- assetRequest.init');
  },

  this.showEducationConfirmationModal = function(){
	  $(document).on('select2:select','#requestees', function (e) {
		  $('.educationConfirmedCheckbox:last').attr('checked',false);
		  $('#saveAssetRequest').attr('disabled',true);
		  var data = e.params.data;
		  console.log(data);
		  var cnum_id = data.id.trim();
		  $('#educationNotesid').html(data.text);
		  $.ajax({
			  url: "ajax/getSecurityEducationForCnum.php",
		      type: 'GET',
		      data: { cnum:cnum_id},
		      success: function(result){
		    	  var resultObj = JSON.parse(result);
		    	  var securityEducation = resultObj.securityEducation;
		    	  if(securityEducation!='Yes'){
		    		  $('#cnumForSecurityModal').val(cnum_id);
		    		  $('#confirmEducationModal').modal('show');
		    	  } else {
		    		  $('#person-1-educationConfirmed').attr('checked',true);
		    		  $('#saveAssetRequest').attr('disabled',false);
		    	  }
		      }
		  });
	  });
  },

  this.listenForEducationConfirmation = function(){
	  $(document).on('click','#confirmedEducation', function(){
		    var cnum = $('#cnumForSecurityModal').val();
	        $('#confirmEducationModal').modal('hide');
	        $('#hideTillEducationConfirmed').show();
	        $('#person-1-educationConfirmed').attr('checked',true);
	        $('#saveAssetRequest').attr('disabled',false);
	        $.ajax({
				  url: "ajax/updateSecurityEducationForCnum.php",
			      type: 'POST',
			      data: { cnum:cnum,
			    	      securityEducation: 'Yes'},
			      success: function(result){
			    	  var resultObj = JSON.parse(result);

			      }
			  });
	  });
  },

  this.listenForNoEducation = function(){
	  $(document).on('click','#noEducation', function(){
		    var cnum = $('#cnumForSecurityModal').val();
		  	$('#saveAssetRequest').attr('disabled',false);
	        $('#confirmEducationModal').modal('hide');
	        $('#hideTillEducationConfirmed').hide();
	        $('#doTheEducation').show();
	        $.ajax({
				  url: "ajax/updateSecurityEducationForCnum.php",
			      type: 'POST',
			      data: { cnum:cnum,
			    	      securityEducation: 'No'},
			      success: function(result){
			    	  var resultObj = JSON.parse(result);
			      }
			  });
	  });
  },

  this.listenForSelectRequestee = function(){
	  $(document).on('select2:select','#requestees', function (e) {
		    console.log('fired listenForSelectRequestee');
		    var AssetRequest = new assetRequest();
		    var data = e.params.data;
		    var cnum_id = data.id.trim();
		    var ctid_id = cnum2ctid[cnum_id];
		    var ctbFlag = cnum2ctbflag[cnum_id];
		    if(!ctid_id){
		    	console.log('prompt for CT ID');
		    	$('.locationFor').val('').trigger('change');
		    	$('#requesteeName').val(data.text);
		    	$('#ctbflag').val(ctbFlag);
		    	$('#obtainCtid').modal('show');
		    } else {
		    	console.log('DONT rompt for CT ID');
				AssetRequest.recordCtidOnForm(data.text, ctid_id, ctbFlag);
		    }
		    console.log('now ajax get location for cnum');

		    $.ajax({
		    	  url: "ajax/checkForOpenRequests.php",
		          data : { cnum:cnum_id },
		          type: 'POST',
		          success: function(result){
		        	  var resultObj = JSON.parse(result);
		        	  console.log(resultObj);
		        	  var assetTitles = resultObj.assetTitles;
		        	  console.log(assetTitles);
		        	  console.log(typeof(assetTitles));
		        	  for (var asset in assetTitles) {
		        		    if (assetTitles.hasOwnProperty(asset)) {
		        		    	$("[data-asset='" + asset + "']").addClass('subjectToOpenRequest')
		        		    	console.log(asset);
		        		    	console.log($("[data-asset='" + asset + "']"));
		        		    }
		        		}
		        	  console.log($(".subjectToOpenRequest"));

		            },
		          complete : function(xhr, status){
		        	  $.ajax({
		  		        url: "ajax/getLbgLocationForCnum.php",
		  		        type: 'GET',
		  		        data: { cnum:cnum_id},
		  		        success: function(result){
		  		        	console.log('did we get a location?');
		  		        	var resultObj = JSON.parse(result);
		  		        	var lbgLocation = resultObj.lbgLocation;
		  				    $('.locationFor').attr('disabled',false);
		  				    if(lbgLocation){
		  				    	console.log('yes, we got a location');
		  				    	$('.locationFor').val(lbgLocation).trigger('change').trigger({
		  				    	    type: 'select2:select',
		  				    	    params: {
		  				    	        data:{
		  				    	        	  "id": lbgLocation,
		  				    	        	  "text": lbgLocation
		  				    	        	}
		  				    	    }
		  				    	});
		  				    } else {
		  				    	console.log('no, we did not get a location');
		  				    	$('.locationFor').val('').trigger('change');
		  				    }
		  		        }
		  		    });
		          }
		      });
		    $("#approvingManager option[value='"+cnum_id+"']").remove();
		});
  },

  this.listenForSelectLocation = function(){
	  $(document).on('select2:select','.locationFor', function (e) {
		    console.log('fired listenForSelectLocation');
		    console.log(e);

			  console.log('is form valid NOW listenForSelectLocation ?');
		      var form = document.getElementById('assetRequestForm');
		      var formValid = form.checkValidity();
		      console.log(formValid);

		    var AssetRequest = new assetRequest();

		    $('#requestableAssetDetailsDiv').show();
		    console.log($('.requestableAsset'));

		    $('.requestableAsset').not(':first').prop('checked',false); // uncheck all the ticks
		    $('.justificationDiv').hide();								// Close all the Justification boxes.

		    var data = e.params.data;
		    var location = data.text.trim();
		    if(location.includes('UK')){
		    	AssetRequest.checkAssetsForShore('on');
		    } else {
		    	AssetRequest.checkAssetsForShore('off');
		    }

			  console.log('is form valid NOW NOW listenForSelectLocation ?');
		      var form = document.getElementById('assetRequestForm');
		      var formValid = form.checkValidity();
		      console.log(formValid);

		      console.log('finished listenForSelectLocation');

		});
  },


  this.listenForSelectAsset = function(){
	  $(document).on('click','.requestableAsset', function (e) {
		  var id = (this.id);
		  var justificationDivId = id.replace("-asset-", "-justification-div-");
		  $('#'+justificationDivId).toggle();

		  var justificationState = $(this).is(':checked') ? true : false;
		  $(this).closest('.selectableThing').find('.justification').attr('required',justificationState);
		  console.log($(this).closest('.selectable').find('.justification'));


		  var AssetRequest = new assetRequest();
		  var assetMissingPrereq = AssetRequest.checkForAssetMissingPrereq();
		  console.log('do we have an assetmissingprereq?');
		  console.log(assetMissingPrereq);
		  if(assetMissingPrereq){
			  AssetRequest.promptForMissingPrereq(assetMissingPrereq);
		  };
		});
  },


  this.listenForEnteringCtid = function(){
	  $('#obtainCtid').on('hidden.bs.modal', function (e) {
		  var requestee =$('#requesteeName').val();
		  var ctid =$('#requesteeCtid').val().trim();
		  var ctbflag = $('#ctbflag').val().trim();
		  var AssetRequest = new assetRequest();
		  console.log(ctid);

		  if(ctid){
			  console.log('record'+ ctid);
			  AssetRequest.recordCtidOnForm(requestee, ctid, ctbflag);
		      $.ajax({
		    	  url: "ajax/saveCtid.php",
		          type: 'POST',
		          data : {notesid:requestee,
		           	      ctid:ctid
		           	      },
		          success: function(result){
		        	  console.log('we have saved their CT ID');
		        	  console.log(result);
	    			  console.log('record required');
	    			  AssetRequest.recordCtidOnForm(requestee, ctid, ctbflag);
		    		  },
		      });
		  } else {
			  var doubleCheck = confirm('STOP : Please confirm the indivual DOES NOT ALREADY HAVE a CT ID')
			  if(!doubleCheck){
				  $('#obtainCtid').modal('show');
				  }
			  console.log('they did not provide a CT ID');
			  AssetRequest.recordCtidOnForm(requestee, 'Required',ctbflag);
		  };
	  });
  },

  this.saveAssetRequestRecords = function(){
	  console.log('would save all the records now');
	  var allDisabledFields = ($("#assetRequestForm input:disabled"));
      $(allDisabledFields).attr('disabled',false);
      var formData = $('#assetRequestForm').serialize();
      $(allDisabledFields).attr('disabled',true);
      console.log(formData);
      $.ajax({
    	  url: "ajax/createAssetRequestRecords.php",
          data : formData,
          type: 'POST',
          success: function(result){
        	  var resultObj = JSON.parse(result);
        	  console.log(resultObj);
        	  var assetRequests = resultObj.requests;
        	  $('#saveFeedbackModal .modal-body').html("<h3>Requests Created</h3>" + assetRequests);
        	  $('#saveFeedbackModal').modal('show');
            },
          complete : function(xhr, status){
         	}
         });
  }

  this.listenForSaveAssetRequest = function(){
	  $(document).on('click','#saveAssetRequest', function(){
		  console.log('they want to save');

		  $('#saveAssetRequest').addClass('spinning');
		  $('#saveAssetRequest').attr('disabled',true);

	      var form = document.getElementById('assetRequestForm');
	      var formValid = form.checkValidity();
	      if(formValid){
//	    	  var AssetRequest = new assetRequest();
	    	  AssetRequest.saveAssetRequestRecords();
	      } else {
	    	  alert('Please complete form');
    		  $('#saveAssetRequest').removeClass('spinning');
    		  $('#saveAssetRequest').attr('disabled',false);
	      }
	  });

  },

  this.checkForAssetMissingPrereq = function() {
	  var prereqElement = false;
	  var selectedAssetsToInspect = $('.requestableAsset:checked').not('*[data-ignore="Yes"]');
	  var prereqsRequiredFor = [];
	  for(i=0;i<selectedAssetsToInspect.length;i++){
		  var selectedAsset = selectedAssetsToInspect[i];
		  var asset = $(selectedAsset).data('asset');
		  var preReq = $(selectedAsset).data('prereq');
		  /*
		   * Basically. Get all the checked requestable assets
		   * then filter for the asset named 'preReq'.
		   *
		   * If the list we get back is empty - then we know the pre-req is not amongst the checked assets, so prompt the user how they
		   * want to handle it.
		   *
		   */
		  var isPreReqAmongstTheChecked = $('.requestableAsset:checked').filter('*[data-asset="'+preReq+'"]');
		  if(isPreReqAmongstTheChecked.length==0){
			  /*
			   * Seek the pre-req amongst all the requestableAssets that we've not been told to ignore.
			   * If it turns up - return it from this Function and stop looking.
			   * If it doesn't then carry one, we've been told to ignore it.
			   */
			  var prereqElement = $('.requestableAsset:enabled').not('*[data-ignore="Yes"]').filter('*[data-asset="'+preReq+'"]')[0];
			  if(prereqElement){
			  	prereqsRequiredFor.push(selectedAsset);
		  	}
		  }
	  };

	  // If we found a prereqElement, (ie a pre-req that isn't checked - then return the Owning selectedAsset;
	  return prereqsRequiredFor.length >0 ? prereqsRequiredFor[0] : false;


  },

  this.promptForMissingPrereq  = function(element){
	  var asset = $(element).data('asset');
	  var preReq = $(element).data('prereq');
	  var requestee = $('#requesteeName').val();
	  $('#requestedAssetTitle').html(asset);
	  $('#prereqAssetTitle').html(preReq);
	  $('#requesteeNotesid').html(requestee);
	  $('#missingPrereqModal').modal('show');
  },

  this.listenForAddPrereq = function(){
	  $(document).on('click','#addPreReq', function(){
		  console.log('they want to add the prereq');
		  var preReqTitle =  $('#prereqAssetTitle').html();
		  var preReqElement = $('.requestableAsset').filter('*[data-asset="'+preReqTitle+'"]');
//		  $(preReqElement).prop('checked',true);
		  $(preReqElement).trigger('click');
		  $('#missingPrereqModal').modal('hide');
	  });
  },

  this.listenForIgnorePrereq = function(){
	  $(document).on('click','#ignorePreReq', function(){
		  console.log('they want to ignore the prereq');
		  var preReqTitle =  $('#prereqAssetTitle').html();
		  var preReqElement = $('.requestableAsset').filter('*[data-asset="'+preReqTitle+'"]')[0];
		  $(preReqElement).attr('data-ignore', 'Yes')
		  $('#missingPrereqModal').modal('hide');
	  });
  },

  this.listenForClosingPrereqModal = function(){
	  $('#missingPrereqModal').on('hidden.bs.modal', function (e) {
		  var AssetRequest = new assetRequest();
		  var assetMissingPrereq = AssetRequest.checkForAssetMissingPrereq();
		  if(assetMissingPrereq){
			  AssetRequest.promptForMissingPrereq(assetMissingPrereq);
		  }
	  });
  },

  this.listenForClosingSaveFeedbackModal = function(){
	  $('#saveFeedbackModal').on('hidden.bs.modal', function (e) {
		  $('.greyablePage').addClass('overlay');
		  location.reload();
	  });
  },

  this.listenForToggleReturnRequest = function(){
	  var saveCtidState='notRecorded';
	  var saveCtidTick ='notRecorded';
	  $(document).on('change','#returnRequest', function(){
		  ctidAsset = $('*[data-asset="CT ID"]');
		  console.log('Toggling between a return and a request');
		  var isReturn = $('#returnRequest:checked').length > 0;

		  console.log(isReturn);
		  console.log(saveCtidState);

		  if(isReturn){
			  var reallyReturn = confirm('You have indicated you wish to RETURN an asset, is this correct ? If this is NOT what you intend to do, please click "Cancel" for this prompt and re-click the Toggle so it reads "Request New"');
			  if(!reallyReturn){
				  console.log('they dont want to return it');
				  return;
			  }
		  }

		  console.log('they really want to return it');

		  if(isReturn && saveCtidState=='notRecorded' ){
			  saveCtidState = $(ctidAsset).attr('disabled');
			  saveCtidTick = $(ctidAsset).attr('checked');
			  $(ctidAsset).attr('disabled',false);
		  } else if(isReturn) {
			  $(ctidAsset).attr('disabled',false);
		  } else {
			  $(ctidAsset).attr('disabled',saveCtidState);
			  $(ctidAsset).attr('checked',saveCtidTick);
		  }
	  });
  },


  this.recordCtidOnForm = function(email_address, ctid,ctbflag ){
	  var ctb = ctbflag ?  ctbflag :  'unknown';
	  var lastCtidInput = $('#allCtidHereDiv > :input').not('.select2').last();
 	  $(lastCtidInput).val(ctid);

	  var lastRequest = $('#requestDetailsDiv > .panel').last();
	  $(lastRequest).find('.panel-title').html('Request For : ' + email_address + "(" + ctb + ")" );
	  ctidAsset = $('*[data-asset="CT ID"]');
	  if(ctid=='Required'){
		$(ctidAsset).attr('disabled',true).prop('checked',true);
	  } else {
		$(ctidAsset).attr('disabled',true).prop('checked',false);
	  }
	  $(ctidAsset).next('label').text('CT ID ('+ ctid +')');

  },


  this.cloneRequestDetails = function(email_address, ctid){
	  console.log(email_address + ":" + ctid);
	  var lastRequest = $('#requestDetailsDiv > .panel').last();
	  console.log($(lastRequest));
	  console.log(lastRequest);
	  $(lastRequest).clone().appendTo("#requestDetailsDiv");
	  $(lastRequest).attr('id','requestFor'+ctid).data('ctid',ctid).data('email',email_address);
	  $(lastRequest).find('.panel-title').html('Request For : ' + email_address );
	  $(lastRequest).find('#locationFor')
  },

  this.initialiseLocationSelect2 = function(ctid){
  	$('#locationFor'+ctid).select2({
    	width:'100%',
  		placeholder: 'Approved Location',
		allowClear: true,
		ajax: {
		    url: '/ajax/select2Locations.php',
		    dataType: 'json'
		    }
    });
  },

  this.countCharsInTextarea = function(){
	  $('textarea').keypress(function(){
		if((this.value.length + 1) > $(this).attr('max')) {
			$(this).next('span').html('Justification too long, please keep to between'+$(this).attr('min')+' and '+$(this).attr('max') + ' characters' );
			$(this).next('span').removeClass('bg-warning').removeClass('bg-success').removeClass('bg-danger').addClass('bg-danger');
		} else {
			$(this).next('span').html(($(this).attr('max') - (this.value.length+1))+' more chars allowed');
			$(this).next('span').removeClass('bg-warning').removeClass('bg-success').removeClass('bg-danger').addClass('bg-success');
		}
	  });
  },

  this.checkAssetsForShore = function(shore){

	  if(shore=='on'){
		  console.log('dissallow onshore=no');
		  console.log($('*[data-onshore="No"]'));
		  $('*[data-onshore="No"]').not('*[data-asset="CT ID"]').prop('checked',false).attr('disabled',true);
		  $('*[data-onshore="Yes"]').not('*[data-asset="CT ID"]').attr('disabled',false);
		  $.each($('*[data-onshore="No"]').not('*[data-asset="CT ID"]').next('label'),function(key,value){
			  var text = $(value).text();
			  var alreadyAmended = text.includes("not available onshore");
			  if(!alreadyAmended) {
				  $(value).text(text + ' - not available onshore');
			  }

		  });
		  $.each($('*[data-onshore="Yes"]').not('*[data-asset="CT ID"]'),function(key,value){
			  var label = $(this).next('label');
			  var assetTitle = $(this).data('asset');
			  $(label).text(assetTitle);
		  });
	  } else {
		  console.log('dissallow offshore=no')
		  console.log($('*[data-offshore="No"]'));
		  $('*[data-offshore="No"]').not('*[data-asset="CT ID"]').prop('checked',false).attr('disabled',true);
		  $('*[data-offshore="Yes"]').not('*[data-asset="CT ID"]').attr('disabled',false);
		  $.each($('*[data-offshore="No"]').not('*[data-asset="CT ID"]').next('label'),function(key,value){
			  var text = $(value).text();
			  var alreadyAmended = text.includes("not available offshore");
			  if(!alreadyAmended) {
				  $(value).text(text + ' - not available offshore');
			  }
		  });
	  }
	  /*
	   * Now disable anything subject to an open request.
	   */
	  $('.subjectToOpenRequest').attr('disabled',true);

	  $.each($('.subjectToOpenRequest'), function( index, value ) {
		  console.log(value);
		  var asset = $(value).data('asset');
		  alert( asset + " is currently subject to an open request so cannot be selected at this time." );
		});


  }

}

var AssetRequest = new assetRequest();

$( document ).ready(function() {
	AssetRequest.init();
});
