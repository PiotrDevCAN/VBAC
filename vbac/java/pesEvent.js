	/*
 *
 *
 *
 */

function pesEvent() {

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
		  
		  console.log( column + ":" +  cnum + ':'  + setPesTo + ":" + alertClass );
		  
		  $(this).parents('div').prev('div.pesStageDisplay').html(setPesTo);
		  $(this).parents('div').prev('div.pesStageDisplay').removeClass('alert-info').removeClass('alert-warning').removeClass('alert-success').addClass(alertClass);
		  $(this).addClass('spinning');
		  
		  var buttonObj = this;
		  
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
		        	   console.log(buttonObj);
		             } else {
		       		  $(this).parents('div').prev('div.pesStageDisplay').html(resultObj.message);	 
		             };
		           $(buttonObj).removeClass('spinning');
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
		  console.log(buttonObj);
		  
		  
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
		           console.log(result);
		           var resultObj = JSON.parse(result);
		           if(resultObj.success==true){
		        	   console.log(resultObj.formattedStatusField);
		        	   console.log(buttonObj);
		        	   console.log(buttonObj.parents('div:first'));		        	   
		        	   console.log(buttonObj.parents('div:first').siblings('div.pesProcessStatusDisplay:first').html());
		        	   buttonObj.parents('div:first').siblings('div.pesProcessStatusDisplay').html(resultObj.formattedStatusField);	
		           }
		           $(buttonObj).removeClass('spinning');
		       }
		   });
	  });
  },
  
  
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
  }  
}

$( document ).ready(function() {
	  var pesevent = new pesEvent();
	  pesevent.init();
	});



