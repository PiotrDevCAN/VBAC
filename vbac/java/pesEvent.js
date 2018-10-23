/*
 *
 *
 *
 */

function pesEvent() {

  this.init = function(){
    console.log('+++ Function +++ pesEvent.init');
    console.log('--- Function --- pesEvent.init');
  },
  
  this.listenForComment = function() {
	  $('textarea').on('input', function(){
		  console.log(this);		  
	  });
  }, 
  
  
  
  this.listenForSavePesComment = function() {
	  $(document).on('click','.btnSavePesEventComment', function(){
		  $.ajax({
			  	url: "ajax/savePesComment.php",
			  	type: 'POST',
			  	data : { cnum: $(this).data('cnum'),
 	        	         event:$(this).data('event'),
			  			},
			  	success: function(result){
			  		console.log(result);
		      		}
	        	});
	  	});
  }

  
  this.listenForPesStageValueChange = function(){
	  $(document).on('click','.btnPesStageValueChange', function(){  
		  var setPesTo = $(this).data('setpesto');	
		  var column = $(this).parents('td').data('pescolumn');
		  var cnum = $(this).parents('tr').data('cnum');
		  
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
  
  
  
}

$( document ).ready(function() {
	  var pesevent = new pesEvent();
	  pesevent.init();
	});



