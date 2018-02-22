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
	  $('#confirmEducationModal').modal('show');

  },

  this.listenForEducationConfirmation = function(){
	  $(document).on('click','#confirmedEducation', function(){
	        $('#confirmEducationModal').modal('hide');
	        $('#hideTillEducationConfirmed').show();
	        $('#educationConfirmed').attr('checked',true);
	  });
  },

  this.listenForNoEducation = function(){
	  $(document).on('click','#noEducation', function(){
	        $('#confirmEducationModal').modal('hide');
	        $('#doTheEducation').show();
	  });
  },

  this.listenForSelectRequestee = function(){
	  $(document).on('select2:select','#requestees', function (e) {
		    var AssetRequest = new assetRequest();
		    var data = e.params.data;
		    console.log(data);
		    var cnum_id = data.id.trim();
		    var ctid_id = cnum2ctid[cnum_id].trim();

//		    console.log(cnum_id);
//		    console.log(ctid_id);
//		    console.log(ctid_id.length);
//		    console.log(typeof(ctid_id));

		    if(!ctid_id){
		    	$('#requesteeName').val(data.text);
		    	$('#obtainCtid').modal('show');
		    } else {
				  AssetRequest.recordCtidOnForm(data.text, ctid_id);
				//  AssetRequest.cloneRequestDetails(data.text, ctid_id);
		    }
		    $('#locationFor').attr('disabled',false);

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

  this.listenForSaveAssetRequest = function(){
	  $(document).on('click','#saveAssetRequest', function(){
		  $.each($('.requestableAsset:checked'),function(key,value){
			  var AssetRequest = new assetRequest();
			  AssetRequest.checkAllPreReqs();
		  });
	  });
  },

  this.listenForAddPrereq = function(){
	  $(document).on('click','#addPreReq', function(){
		  console.log('they want to add the prereq');
		  var preReqTitle =  $('#prereqAssetTitle').html();
		  var preReqElement = $('.requestableAsset').filter('*[data-asset="'+preReqTitle+'"]');
		  $(preReqElement).prop('checked',true);
		  $('#missingPrereqModal').modal('hide');
	  });
  },

  this.listenForNewPrereq = function(){
	  $('#missingPrereqModal').on('hidden.bs.modal', function (e) {
		  var AssetRequest = new assetRequest();
		  AssetRequest.checkAllPreReqs();
	  });
  }

  this.checkAllPreReqs = function(){
	  $.each($('.requestableAsset:checked'),function(key,value){
		  var AssetRequest = new assetRequest();
		  AssetRequest.checkPrereqIsChecked(this);
	  });
  },

  this.checkPrereqIsChecked = function(checkedElement){
	  console.log('checking prereq for ');
	  console.log(checkedElement);
	  var asset  = $(checkedElement).data('asset');
	  var preReq = $(checkedElement).data('prereq');
	  var requestee = $('#requesteeName').val();
	  console.log(asset + ' has a prereq of ' + preReq);
	  if(preReq){
		  console.log('filter:' + '*[data-asset="'+preReq+'"]' );
		  var isPreReqChecked = $('.requestableAsset:checked').filter('*[data-asset="'+preReq+'"]');
		  console.log(isPreReqChecked.length);
		  if(isPreReqChecked.length==0){
			  console.log('pre req not selected')
			  $('#requestedAssetTitle').html(asset);
			  $('#prereqAssetTitle').html(preReq);
			  $('#requesteeNotesid').html(requestee);
			  $('#missingPrereqModal').modal('show');
		  };
	  }


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
  		placeholder: 'Location',
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


$( document ).ready(function() {
	var AssetRequest = new assetRequest();
	AssetRequest.init();
});
