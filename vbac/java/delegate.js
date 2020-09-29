/*
 *
 *
 *
 */



function delegate() {
	
	var buttonCommon = {
		exportOptions: {
	    format: {
		         body: function ( data, row, column, node ) {
	             //   return data ?  data.replace( /<br\s*\/?>/ig, "\n") : data ;
	             return data ? data.replace( /<br\s*\/?>/ig, "\n").replace(/(&nbsp;|<([^>]+)>)/ig, "") : data ;
	             //    data.replace( /[$,.]/g, '' ) : data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
                 }
              }
           }
	};
	
	

	var myDelegatesTable;

	this.sleep = function(ms) {
		  return new Promise(resolve => setTimeout(resolve, ms));
		},

	  this.init = function(){
		    console.log('+++ Function +++ delegate.init');
		    console.log('--- Function --- delegate.init');
		  },


	  this.listenForSaveDelegate = function(){
			  $(document).on('click','#saveDelegate', function(){
				  $('#saveDelegate').addClass('spinning');
				    var cnum = $('#delegate').val();
				    var requestorCnum = $('#requestorCnum').val();
				    var requestorEmail = $('#requestorEmail').val();

			        $.ajax({
						  url: "ajax/saveDelegate.php",
					      type: 'POST',
					      data: { cnum:cnum,
					    	      requestorCnum: requestorCnum,
					    	      requestorEmail : requestorEmail },
					      success: function(result){
					    	  var resultObj = JSON.parse(result);
					    	  console.log(resultObj);
					    	  $('#resultHere').html('Delegate Saved');
					    	  delegate.myDelegatesTable.ajax.reload();
					    	  var promise = sleep(4000);
					    	  promise.then(function (result){
						    	  console.log('slept');
						    	  $('#resultHere').html('');
						    	  $('#saveDelegate').removeClass('spinning');
					    	  })
					      }
					  });
			  });
		  },

		  this.listenForDeleteDelegate = function(){
			  $(document).on('click','.btnDeleteDelegate', function(e){
				  $(this).addClass('spinning');
				  var cnum   = $(this).data('cnum');
				  var delegateCnum = $(this).data('delegate');

			       $.ajax({
						  url: "ajax/deleteDelegate.php",
					      type: 'POST',
					      data: { cnum:cnum,
					    	      delegateCnum: delegateCnum
					    	    },
					      success: function(result){
					    	  $('.btnDeleteDelegate').removeClass('spinning');
					    	  var resultObj = JSON.parse(result);
					    	  console.log(resultObj);
					    	  delegate.myDelegatesTable.ajax.reload();
					    	  }
					  });
			  });
		  },


		  this.initialiseMyDelegatesDataTable = function(){
			  var requestorCnum = $('#requestorCnum').val();
			  // DataTable
			    delegate.myDelegatesTable = $('#myDelegatesTable').DataTable({
			        ajax: {
			              url: 'ajax/populateMyDelegates.php',
			              type: 'POST',
			              data: {requestorCnum:requestorCnum}
			          }	,
			          autoWidth: true,
			          processing: true,
			          responsive: true,
			          language: {
			        	    "emptyTable": "No Delegates Found"
			          		},
        			  dom: 'Blfrtip',
          			  buttons: [
                    	$.extend( true, {}, buttonCommon, {
	                        extend: 'excelHtml5',
                        	exportOptions: {
	                            orthogonal: 'sort',
                            	stripHtml: true,
                            	stripNewLines:false
                        	},
                         	customize: function( xlsx ) {
                             	var sheet = xlsx.xl.worksheets['sheet1.xml'];
                         	}
                	}),
                	$.extend( true, {}, buttonCommon, {
	                    extend: 'csvHtml5',
                    	exportOptions: {
	                        orthogonal: 'sort',
                        	stripHtml: true,
                        	stripNewLines:false
                    	}
                	}),
                ],
		 });
	}


}