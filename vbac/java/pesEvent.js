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
		
		  console.log($(this).data());
		  console.log($(this).parents('td').data());
		  console.log($(this).parents('tr').data());
		  
		  console.log($(this).parents('div').prev('div.pesStageDisplay').html());
		  
		  
		  $(this).parents('div').prev('div.pesStageDisplay').html()
		  
		  
		  
		  var setPesTo = $(this).data('setpesto');	
		  var column = $(this).parents('td').data('pescolumn');
		  var cnum = $(this).parents('tr').data('cnum');
		  
		  var pesevent = new pesEvent();
		  var alertClass = pesevent.getAlertClassForPesStage(setPesTo);
		   
		  
		  console.log( column + ":" +  cnum + ':'  + setPesTo + ":" + alertClass );
		  
		  $(this).parents('div').prev('div.pesStageDisplay').html(setPesTo);
		  $(this).parents('div').prev('div.pesStageDisplay').removeClass('alert-info').removeClass('alert-warning').removeClass('alert-success').addClass(alertClass);
		  $(this).addClass('spinning');
		  
		  
		  
		  
		  
		  
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



