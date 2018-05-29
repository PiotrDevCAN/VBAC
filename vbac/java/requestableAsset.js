/**
 *
 */
function requestableAsset() {
	var table;

  this.init = function(){
    console.log('+++ Function +++ requestableAsset.init');
    console.log('--- Function --- requestableAsset.init');
  },

  this.initialiseTable = function(){
      $.ajax({
        url: "ajax/createHtmlForRequestableAssetTable.php",
        type: 'POST',
        success: function(result){
          var RequestableAsset = new requestableAsset();
          $('#requestableAssetListDiv').html(result);
          RequestableAsset.initialiseDataTable();
        }
      });
  },

  this.initialiseDataTable = function(){
      // Setup - add a text input to each footer cell
      $('#requestableAssetTable tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input type="text" placeholder="Search '+title+'" />' );
      } );
    // DataTable
      console.log($('#requestableAssetTable'));
      requestableAsset.table = $('#requestableAssetTable').DataTable({
        ajax: {
              url: 'ajax/populateRequestableAssetTable.php',
              type: 'POST',
          }	,
        columnDefs: [
                      { "visible": false, "targets": [6,7,8,9,10,12,13,14,15] }
          ] ,
          "columns": [
                      { "data": "ASSET_TITLE" , "defaultContent": "" },
                      { "data": "ASSET_PREREQUISITE" ,"defaultContent": "" },

                      { "data": "ASSET_PRIMARY_UID_TITLE","defaultContent": ""},
                      { "data": "ASSET_SECONDARY_UID_TITLE", "defaultContent": "" },

                      { "data": "APPLICABLE_ONSHORE", "defaultContent": "" },
                      { "data": "APPLICABLE_OFFSHORE", "defaultContent": "" },

                      { "data": "REQUEST_BY_DEFAULT", "defaultContent": "" },
                      { "data": "BUSINESS_JUSTIFICATION_REQUIRED", "defaultContent": "" },

                      { "data": "RECORD_DATE_ISSUED_TO_IBM", "defaultContent": "" },
                      { "data": "RECORD_DATE_ISSUED_TO_USER", "defaultContent": "" },
                      { "data": "RECORD_DATE_RETURNED", "defaultContent": "" },

                      { "data": "LISTING_ENTRY_CREATED", "defaultContent": "" },
                      { "data": "LISTING_ENTRY_CREATED_BY", "defaultContent": "" },

                      { "data": "LISTING_ENTRY_REMOVED", "defaultContent": "" },
                      { "data": "LISTING_ENTRY_REMOVED_BY", "defaultContent": "" },

                      { "data": "PROMPT", "defaultContent": "" },
                      { "data": "ORDER_IT_TYPE", "defaultContent": "" },

                      ],
        order: [[ 0, "asc" ]],
        autoWidth: false,
        deferRender: true,
        responsive: false,
        processing: true,
        responsive: true,
        dom: 'Blfrtip',
//	    	colReorder: true,
          buttons: [
                    'colvis',
                    'excelHtml5',
                    'csvHtml5',
                    'print'
                ],
      });
      // Apply the search
      requestableAsset.table.columns().every( function () {
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

  this.initialiseSelect2 = function(){
	  $('#asset_prerequisite').select2({
		  placeholder: "Select a pre-requisite asset(if appro.)",
//		  allowClear: true,
//		  ajax: {
//			    url: 'ajax/possiblePrerequisiteAssets.php',
//			    dataType: 'json',
//			    data:{assetTitle: 'Dummy',
//			    	  assetPrereq: 'CT ID'}
//			  }
	  });
  },

  this.listenForSaveRequestableAsset = function(){
	    $(document).on('click','#saveRequestableAsset', function(){
	      console.log($(this).val());
	      $('#saveRequestableAsset').attr('disabled',true);
          var RequestableAsset = new requestableAsset();
	      RequestableAsset.saveRequestableAsset($(this).val());
	    });
	  },

  this.saveRequestableAsset = function(mode){
		    console.log('saveRequestableAsset mode:' + mode);
		    $('#asset_title').attr('disabled',false); // So it can be passed to the ajax call.
		    var ibmer = $('#hasBpEntry').is(':checked');
		    var form = $('#requestableAssetListForm');
		    var formValid = form[0].checkValidity();
		    if(formValid){
		      $("#saveRequestableAsset").addClass('spinning');
		      var formData = form.serialize();
		      var inputData = $('input').serialize();
		      console.log(inputData);
		      formData += "&mode=" + mode ;
		      console.log(formData);
		        $.ajax({
		          url: "ajax/saveRequestableAsset.php",
		          type: 'POST',
		            data : formData,
		          success: function(result){
		        	  $("#saveRequestableAsset").removeClass('spinning');
		        	  $('#updateRequestableAsset').removeClass('spinning');
		        	  console.log(result);
		        	  var resultObj = JSON.parse(result);
		        	  if(resultObj.success==true){
		        		  $('#requestableAssetListForm')[0].reset();
		        	  }
		        	  console.log(typeof(requestableAsset.table));
		        	  $('.greyablePage').addClass('overlay');
		    		  location.reload();
		          }
		        });
		    } else {
		      console.log('invalid fields follow');
		      console.log($(form).find( ":invalid" ));
		    }
	  },

  this.listenForEditButton = function(){
		  $(document).on('click','.btnEditAsset', function(){
		        console.log(this);
		        $(this).data('dtetoibm') == 'Yes' ? $('#RecordDateToIbm').bootstrapToggle('on'): $('#RecordDateToIbm').bootstrapToggle('off');
		        $(this).data('dtetousr') == 'Yes' ? $('#RecordDateToUser').bootstrapToggle('on') : $('#RecordDateToUser').bootstrapToggle('off');
		        $(this).data('dteret')   == 'Yes' ? $('#RecordDateReturned').bootstrapToggle('on') : $('#RecordDateReturned').bootstrapToggle('off');
		        $(this).data('onshore')   == 'Yes' ? $('#applicableOnShore').bootstrapToggle('on') : $('#applicableOnShore').bootstrapToggle('off');
		        $(this).data('offshore')   == 'Yes' ? $('#applicableOffShore').bootstrapToggle('on') : $('#applicableOffShore').bootstrapToggle('off');
		        $(this).data('just')   == 'Yes' ? $('#businessJustification').bootstrapToggle('on') : $('#businessJustification').bootstrapToggle('off');
		        $(this).data('just')   == 'Yes' ? $('#promptDiv').show() : $('#promptDiv').hide();
		        $(this).data('just')   == 'Yes' ? $('#prompt').attr('required',true) : $('#prompt').attr('required',false);

		        var promptnat = $(this).data('prompt');
		        var promptdec = decodeURIComponent(promptnat);
		        var promptenc = encodeURIComponent(promptnat);
		        var type      =  $(this).data('type');

		        $('#prompt').val(promptnat);
		        $('#asset_primary_uid_title').val($(this).data('uidpri'));
		        $('#asset_secondary_uid_title').val($(this).data('uidsec'));
		        $('#asset_title').val($(this).data('asset')).attr('disabled',true);
		        $('#saveRequestableAsset').val('Update');
		        $('#ORDER_IT_TYPE').val(type);

		        var prereq = $(this).data('prereq')

		        $('option[value="' + prereq + '"]').prop('selected', true).trigger('change');

		     });
	  },

	  this.listenForJustificationButton = function(){
		  console.log('set listener for justification')
		  $(document).on('change','#businessJustification', function(){
		        $('#promptDiv').toggle();
		        $('#prompt').attr('required') ? $('#prompt').attr('required',false) : $('#prompt').attr('required',true);
		     });
	  },



	  this.listenForDeleteButton = function() {
		  $(document).on('click','.btnDeleteAsset', function(){
			  $.ajax({
				  	url: "ajax/deleteRequestableAsset.php",
				  	type: 'POST',
				  	data : { ASSET_TITLE: $(this).data('asset'),
     	        	       DELETED_BY:$(this).data('deleter')
				  			},
				  	success: function(result){
				  		console.log(result);
				  		requestableAsset.table.ajax.reload();
			      		}
		        	});
		  	});
	  }

}

$( document ).ready(function() {
	  var RequestableAsset = new requestableAsset();
	  RequestableAsset.init();
	});


