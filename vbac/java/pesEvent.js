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

  
  
  
}

$( document ).ready(function() {
	  var pesevent = new pesEvent();
	  pesevent.init();
	});



