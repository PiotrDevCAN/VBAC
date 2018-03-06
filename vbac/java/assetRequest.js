/*
 *
 *
 *
 */
var selectedAssets = [];


function assetRequest() {
	var table;

  this.init = function(){
    console.log('+++ Function +++ assetRequest.init');
    console.log('--- Function --- assetRequest.init');
  },

  this.showEducationConfirmationModal = function(){
	  $(document).on('select2:select','#requestees', function (e) {
		  var data = e.params.data;
		  console.log(data);
		  $('#educationNotesid').html(data.text);
		  $('#confirmEducationModal').modal('show');
	  });
  },

  this.listenForEducationConfirmation = function(){
	  $(document).on('click','#confirmedEducation', function(){
	        $('#confirmEducationModal').modal('hide');
	        $('#hideTillEducationConfirmed').show();
	        $('#person-1-educationConfirmed').attr('checked',true);
	        $('#saveAssetRequest').attr('disabled',false);
	  });
  },

  this.listenForNoEducation = function(){
	  $(document).on('click','#noEducation', function(){
		  	$('#saveAssetRequest').attr('disabled',false);
	        $('#confirmEducationModal').modal('hide');
	        $('#hideTillEducationConfirmed').hide();
	        $('#doTheEducation').show();
	  });
  },

  this.listenForSelectRequestee = function(){
	  $(document).on('select2:select','#requestees', function (e) {
		    var AssetRequest = new assetRequest();
		    var data = e.params.data;
		    console.log(data);
		    var cnum_id = data.id.trim();
		    var ctid_id = cnum2ctid[cnum_id];
		    if(!ctid_id){
		    	$('#requesteeName').val(data.text);
		    	$('#obtainCtid').modal('show');
		    } else {
				  AssetRequest.recordCtidOnForm(data.text, ctid_id);
				//  AssetRequest.cloneRequestDetails(data.text, ctid_id);
		    }
		    $('.locationFor').attr('disabled',false);
		    console.log(cnum_id);
		    console.log($("#approvingManager option"));
		    console.log($("#approvingManager option[value='001399866']"));
		    console.log("#approvingManager option[value='"+cnum_id+"']");
		    console.log($("#approvingManager option[value='"+cnum_id+"']"));
		    $("#approvingManager option[value='"+cnum_id+"']").remove();
		});
  },

  this.listenForSelectLocation = function(){
	  $(document).on('select2:select','.locationFor', function (e) {
		    var AssetRequest = new assetRequest();
		    var data = e.params.data;
		    console.log(data);
		    var location = data.text.trim();
		    console.log(location)
		    if(location=='All of UK,Any'){
		    	console.log('onshore');
		    	AssetRequest.checkAssetsForShore('on');
		    } else {
		    	console.log('offshore');
		    	AssetRequest.checkAssetsForShore('off');
		    }
		});
  },


  this.listenForSelectAsset = function(){
	  $(document).on('click','.requestableAsset', function (e) {
		  console.log(this);
		  console.log($(this).closest('.selectableThing'));
		  console.log($(this).closest('.selectableThing').find('.justification'));

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
		  var AssetRequest = new assetRequest();
		  console.log(ctid);

		  if(ctid){
			  console.log('record'+ ctid);
			  AssetRequest.recordCtidOnForm(requestee, ctid);
		      $.ajax({
		    	  url: "ajax/saveCtid.php",
		          type: 'POST',
		          data : {notesid:requestee,
		           	      ctid:ctid},
		          success: function(result){
		        	  console.log(result);
		          }
		      });
		  }	else {
			  console.log('record required');
			  AssetRequest.recordCtidOnForm(requestee, 'Required');
		  }

	  });
  },

  this.saveAssetRequestRecords = function(){
	  console.log('would save all the records now');
      var allDisabledFields = ($("input:disabled"));
      $(allDisabledFields).attr('disabled',false);
      var formData = $('#assetRequestForm').serialize();

      console.log(formData);

      $(allDisabledFields).attr('disabled',true);

      $.ajax({
    	  url: "ajax/saveAssetRequestRecords.php",
          data : formData,
          type: 'POST',
          success: function(result){
        	  var resultObj = JSON.parse(result);
        	  console.log(resultObj);
        	 //  $('#saveFeedbackModal .modal-body').hmtl('Requests Created');
        	  $('#saveFeedbackModal').modal('show');
            }
         });
	  $('#saveAssetRequest').attr('disabled',false).removeClass('spinning');
  }

  this.listenForSaveAssetRequest = function(){
	  $(document).on('click','#saveAssetRequest', function(){
		  console.log('they want to save');
		  console.log($('#saveAssetRequest'));
		  
		  console.log($('.btn'));
		  console.log($('.btn.spinning'));	  
		  
		  $('#saveAssetRequest').addClass('spinning');
		  
		  console.log($('.btn.spinning'));	
		  
		  $('#saveAssetRequest').attr('disabled',true);
		  console.log($('#saveAssetRequest'));
		  console.log('is form valid ?');
	      var form = document.getElementById('assetRequestForm');
	      console.log(form);
	      var formValid = form.checkValidity();
	      console.log(formValid);
	      if(formValid){
	    	  console.log('valid');
//	    	  var AssetRequest = new assetRequest();
	    	  AssetRequest.saveAssetRequestRecords();
	      } else {
	    	  alert('Please complete form');
			  $('#saveAssetRequest').removeClass('spinning');
			  $('#saveAssetRequest').attr('disabled',false);
	      }
		  console.log('were done processing the save request');
	  });

  },

  this.checkForAssetMissingPrereq = function() {
	  var prereqElement = false;
	  var selectedAssetsToInspect = $('.requestableAsset:checked').not('*[data-ignore="Yes"]');
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
			  var prereqElement = $('.requestableAsset').not('*[data-ignore="Yes"]').filter('*[data-asset="'+preReq+'"]')[0];
			  console.log(prereqElement);
			  break;
		  }
	  };
	  return prereqElement ? selectedAsset : false;


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

  this.recordCtidOnForm = function(email_address, ctid ){
	  console.log(ctid);
	  var lastCtidInput = $('#allCtidHereDiv > :input').last();
	//  $(lastCtidInput).clone().appendTo("#allCtidHereDiv").attr('name','ctid'+ctid).attr('id','ctid'+ctid).data('ctid',ctid).data('email',email_address);
	  $(lastCtidInput).val(ctid);
	  console.log(lastCtidInput);
	  var lastRequest = $('#requestDetailsDiv > .panel').last();
	  $(lastRequest).find('.panel-title').html('Request For : ' + email_address );
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
		if(this.value.length < $(this).attr('min') ){
			$(this).next('span').removeClass('bg-warning').removeClass('bg-success').removeClass('bg-danger').addClass('bg-warning');
			$(this).next('span').html(($(this).attr('min') - this.value.length)+' more chars required');
		} else if(this.value.length > $(this).attr('max')) {
			$(this).next('span').html('Justification too long, please keep to between'+$(this).attr('min')+' and '+$(this).attr('max') + ' characters' );
			$(this).next('span').removeClass('bg-warning').removeClass('bg-success').removeClass('bg-danger').addClass('bg-danger');
		} else {
			$(this).next('span').html(($(this).attr('max') - this.value.length)+' more chars allowed');
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
			  $(value).text(text + ' - not available onshore');
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
			  $(value).text(text + ' - not available offshore');
		  });
	  }
  }
  
 
}

var AssetRequest = new assetRequest();

$( document ).ready(function() {
	AssetRequest.init();
});
