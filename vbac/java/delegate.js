/*
 *
 *
 *
 */



function delegate() {

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
				  $(e.target).addClass('spinning');
				  var cnum   = $(e.target).data('cnum');
				  var delegateCnum = $(e.target).data('delegate');

			       $.ajax({
						  url: "ajax/deleteDelegate.php",
					      type: 'POST',
					      data: { cnum:cnum,
					    	      delegateCnum: delegateCnum
					    	    },
					      success: function(result){
					    	  $(e.target).removeClass('spinning');
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
			      });
			  }


}