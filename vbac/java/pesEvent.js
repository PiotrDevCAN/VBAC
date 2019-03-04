	/*
 *
 *
 *
 */

$.expr[":"].contains = $.expr.createPseudo(function(arg) {
    return function( elem ) {
        return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
    };
});






function searchTable(){
	  var filter = $('#pesTrackerTableSearch').val().toUpperCase();

	  if(filter.length > 3){
		  $('#pesTrackerTable tr').hide();
		  $('#pesTrackerTable th').parent('tr').show();
		  
		  $('#pesTrackerTable tbody tr').children('td').not('.nonSearchable').each(function(){
			  var text = $(this).text().trim().replace(/[\xA0]/gi, ' ').replace(/  /g,'').toUpperCase();
			  if(text.indexOf(filter) > -1){
				  var tr = $(this).parent('tr').show();
			  }
		  });		  
	  } else {
		  $('#pesTrackerTable tr').show	()
	  }
}

function pesEvent() {
	
	var table;

  this.init = function(){
    console.log('+++ Function +++ pesEvent.init');
    
    $('.pesDateLastChased').datepicker({
    	dateFormat: 'dd M yy',
		maxDate:0,
        onSelect: function(dateText) {
        	console.log(this);
        	var cnum = $(this).data('cnum');
        	var pesevent = new pesEvent();
        	pesevent.saveDateLastChased(dateText, cnum, this);
          }		
		}
    ).on("change", function() {
        alert("Got change event from field");
    });
    console.log('--- Function --- pesEvent.init');
  },
  
  this.listenForBtnRecordSelection = function() {
	  $(document).on('click','.btnRecordSelection', function(){
		  $('.btnRecordSelection').removeClass('active');
		  $(this).addClass('active');
		  
		  var pesevent = new pesEvent();
		  pesevent.populatePesTracker($(this).data('pesrecords'));
	  });
  }, 
  
  
  
  
  
  this.populatePesTracker = function(records){
	  var buttons = $('.btnRecordSelection');	  
	  console.log(buttons);	  
	  
	  
	  $('#pesTrackerTableDiv').html('<i class="fa fa-spinner fa-spin" style="font-size:68px"></i>');

	  pesEvent.table = $.ajax({
		  	url: "ajax/populatePesTrackerTable.php",
		  	type: 'POST',
		  	data : { records: records,
		  			},
		    success: function(result){
		    	var resultObj = JSON.parse(result);
		    	
		    	console.log(resultObj.sucess);
		    	console.log(resultObj.messages);
		    	if(resultObj.sucess){
		    		$('#pesTrackerTableDiv').html(resultObj.table);	

		    		$('#pesTrackerTable thead th').each( function () {
		    	        var title = $(this).text();
		    	        $(this).html(title + '<input class="secondInput" type="hidden"  />' );
		    	    } );		    		
		    		
		    	    $('#pesTrackerTable thead td').each( function () {
		    	        var title = $(this).text();
		    	        $(this).html('<input class="firstInput" type="text" placeholder="Search '+title+'" />' );
		    	    });
		    		
		    	} else {
		    		$('#pesTrackerTableDiv').html(resultObj.messages);
		    	}
		    	
		    }
	  });
	  
	    // Apply the search
    
	        $(document).on( 'keyup change', '.firstInput', function (e) {
	        	var searchFor = this.value;
	        	var col = $(this).parent().index();      	
	        	var searchCol = col + 1;
	        	if(searchFor.length >= 3){
		        	$('#pesTrackerTable tbody tr').hide();	        	
		        	$('#pesTrackerTable tbody td:nth-child(' + searchCol + '):contains(' + searchFor + ')	').parent().show();	        		
	        	} else {
	        		$('#pesTrackerTable tbody tr').show();
	        	}

	       } );


	  
	  
	  
  }
  
  
  this.saveDateLastChased = function(date,cnum, field){
	  console.log(field);
	  console.log($(field));
	  var parentDiv = $(field).parent('div');
	  $.ajax({
		  	url: "ajax/savePesDateLastChased.php",
		  	type: 'POST',
		  	data : { cnum: cnum,
		  		     date: date
		  			},
		    success: function(result){
		    	var resultObj = JSON.parse(result);
		    	pesevent = new pesEvent();
		    	pesevent.getAlertClassForPesChasedDate(field);
		    	buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
		    }
	  });
  }
  
  this.listenForComment = function() {
	  $('textarea').on('input', function(){
		  console.log(this);		  
	  });
  }, 
  
  
  
  this.listenForSavePesComment = function() {
	  $(document).on('click','.btnPesSaveComment', function(){
		  
		  var cnum =  $(this).siblings('textarea').data('cnum');
		  var comment = $(this).siblings('textarea').val();
		  var button = $(this);
		  
		  console.log(button.siblings('div'));
		  console.log(button.siblings('div.pesComments'));
		  
		  button.addClass('spinning');
		  $.ajax({
			  	url: "ajax/savePesComment.php",
			  	type: 'POST',
			  	data : { cnum: cnum,
			  		     comment: comment,
			  			},
			  	success: function(result){
			        var resultObj = JSON.parse(result);
			  		button.removeClass('spinning');
			  		button.siblings('div.pesComments').html(resultObj.comment);
			  		button.siblings('textarea').val('');
		      		}
	        	});
	  	});
  }

  
  this.listenForPesStageValueChange = function(){
	  $(document).on('click','.btnPesStageValueChange', function(){  
		  var setPesTo = $(this).data('setpesto');	
		  var column   = $(this).parents('div').data('pescolumn');		  
		  var cnum     = $(this).parents('div').data('cnum');
		  
		  var pesevent = new pesEvent();
		  var alertClass = pesevent.getAlertClassForPesStage(setPesTo);
		  		  
		  $(this).parents('div').prev('div.pesStageDisplay').html(setPesTo);
		  $(this).parents('div').prev('div.pesStageDisplay').removeClass('alert-info').removeClass('alert-warning').removeClass('alert-success').addClass(alertClass);
		  $(this).addClass('spinning');
		  
		  var buttonObj = $(this);
		  
		   $.ajax({
			   url: "ajax/savePesStageValue.php",
		       type: 'POST',
		       data : {cnum:cnum,
		    	   	   stageValue:setPesTo,
		    	   	   stage:column,
		    	   	   },
		       success: function(result){
		           console.log(result);
		           var resultObj = JSON.parse(result);
		           if(resultObj.success==true){
		        	   buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
		           } else {
		       		  $(this).parents('div').prev('div.pesStageDisplay').html(resultObj.message);	 
		           };
		           buttonObj.removeClass('spinning');
		       }
		   });
	  });
  }
  
  
  this.getAlertClassForPesStage = function(pesStageValue){
      switch (pesStageValue) {
      case 'Yes':
          var alertClass = ' alert-success ';
          break;
      case 'Prov':
          var alertClass = ' alert-warning ';
          break;
      case 'N/A':
          var alertClass = ' alert-secondary ';
          break; 
      default:
          var alertClass = ' alert-info ';
          break;
  }
  return alertClass;
}
  
  this.listenForPesProcessStatusChange = function(){
	  $(document).on('click','.btnProcessStatusChange', function(){  
		  var buttonObj = $(this);
		  var processStatus = $(this).data('processstatus');					  
		  var cnum     = $(this).parents('div').data('cnum');
//		  $(this).parents('div').prev('div.pesProcessStatusDisplay').html(processStatus);
		  $(this).addClass('spinning');
		   $.ajax({
			   url: "ajax/savePesProcessStatus.php",
		       type: 'POST',
		       data : {cnum:cnum,
		    	       processStatus:processStatus,
		    	   	   },
		       success: function(result){
		           var resultObj = JSON.parse(result);
		           if(resultObj.success==true){
		        	   buttonObj.parents('div:first').siblings('div.pesProcessStatusDisplay').html(resultObj.formattedStatusField);	
		        	   buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
		           }
		           $(buttonObj).removeClass('spinning');
		       }
		   });
	  });
  },
  
  
  this.listenForPesPriorityChange = function(){
	  $(document).on('click','.btnPesPriority', function(){  
		  var buttonObj = $(this);
		  var pespriority = $(this).data('pespriority');					  
		  var cnum        = $(this).data('cnum');
//		  $(this).parents('div').prev('div.pesProcessStatusDisplay').html(processStatus);
		  $(this).addClass('spinning');
		   $.ajax({
			   url: "ajax/savePesPriority.php",
		       type: 'POST',
		       data : {cnum:cnum,
		    	       pespriority:pespriority,
		    	   	   },
		       success: function(result){
		           var resultObj = JSON.parse(result);
		           if(resultObj.success==true){
		        	   buttonObj.parent('span').siblings('div.priorityDiv:first').html("Priority:" + pespriority);
		        	   var pesevent = new pesEvent();
		        	   pesevent.setAlertClassForPesPriority(buttonObj.parent('span').siblings('div.priorityDiv:first'),pespriority);
		        	           	   
		        	   buttonObj.parents('td').parent('tr').children('td.pesCommentsTd').children('div.pesComments').html(resultObj.comment);
		        	   
		        	   
		           }
		           $(buttonObj).removeClass('spinning');
		       }
		   });
	  });
  },
  
  this.setAlertClassForPesPriority = function(priorityField, priority){
	  
	  $(priorityField).removeClass('alert-success');
	  $(priorityField).removeClass('alert-warning');
	  $(priorityField).removeClass('alert-danger');
	  $(priorityField).removeClass('alert-info'); 

	  
	  
	  switch(priority){
	  case 1:
		  console.log('danger');
		  $(priorityField).addClass('alert-danger');	  
		  break;
	  case 2:
		  console.log('warning');
		  $(priorityField).addClass('alert-warning');
		  break;
	  case 3:
		  $(priorityField).addClass('alert-success');
		  break;			  
	  default :
		  $(priorityField).addClass('alert-info');
		  break;
	  }			  
  } ,

  
  this.getAlertClassForPesChasedDate = function(dateField){
	  
	  $(dateField).parent('div').removeClass('alert-success');
	  $(dateField).parent('div').removeClass('alert-warning');
	  $(dateField).parent('div').removeClass('alert-danger');
	  $(dateField).parent('div').removeClass('alert-info');  
	  
	  var today = new Date();
//	  var date1 = new Date("7/13/2010");  
	  var dateValue = $(dateField).val();	  
	  var lastChased = new Date(dateValue);	  
	  
	  if(typeof(lastChased)=='object'){
		  var timeDiff = Math.abs(today.getTime() - lastChased.getTime());
		  var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 	  
		  
		  switch(true){
		  case diffDays < 7:
			  $(dateField).parent('div').addClass('alert-success');	  
			  break;
		  case diffDays < 14:
			  $(dateField).parent('div').addClass('alert-warning');
			  break;
		  default :
			  $(dateField).parent('div').addClass('alert-danger');
			  break;
		  }		  
		  
	  } else {
		  $(dateField).parent('div').removeClass('alert-info');		  
		  return;
	  } 
  },
  
  this.listenForFilterPriority = function(){
	  $(document).on('click','.btnSelectPriority', function(){
		  var priority = $(this).data('pespriority');
		  if(priority!=0){
			  $('tr').hide();
			  $(".priorityDiv:contains('" + priority + "')").parents('tr').show();
			  $('th').parent('tr').show();			  
		  } else {
			  $('tr').show();
		  }
	  });
  },
  
  this.listenForFilterProcess = function(){
	  $(document).on('click','.btnSelectProcess', function(){
		  var pesprocess = $(this).data('pesprocess');
		  $('tr').hide();
		  $(".pesProcessStatusDisplay:contains('" + pesprocess + "')").parents('tr').show();
		  $('th').parent('tr').show();			  
	  });
  }
  
}

$( document ).ready(function() {
	  var pesevent = new pesEvent();
	  pesevent.init();
	});