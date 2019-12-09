 /*
 *
 *
 *
 */


function agileTribe() {

  var table;
  var spinner =  '<div id="overlay"><i class="fa fa-spinner fa-spin spin-big"></i></div>';




  this.init = function(){
    console.log('+++ Function +++ agileTribe.init');
    console.log('--- Function --- agileTribe.init');
  },

  this.listenForLeader = function(){
	 	$('.typeahead').bind('typeahead:select', function(ev, suggestion) {
	 		$('.tt-menu').hide();
	 		$('#TRIBE_LEADER').val(suggestion.notesEmail);
		});

  },


  this.initialiseAgileTribeTable = function(){	  
	console.log('initialiseAgileTribeTable');
	 
    // Setup - add a text input to each footer cell
    $('#tribeTable tfoot th').each( function () {
    	 var title = $(this).text();
    	 $(this).html( '<input type="text" id="footer'+ title + '" placeholder="Search '+title+'" />' );
    } );
    // DataTable
    agileTribe.table = $('#tribeTable').DataTable({
        ajax: {
              url: 'ajax/populateAgileTribeTable.php',
              type: 'GET',
          }	,
          columns: [
                      { "data": "TRIBE_NUMBER", render: { _:"display", sort:"sort" } },
                      { "data": "TRIBE_NAME"   },
                      { "data": "TRIBE_LEADER" },
                  ],
          order: [[1, "asc" ]],
          responsive: true,
          processing: true,          
          dom: 'Blfrtip',
          buttons: [
                    'colvis',
                    'excelHtml5',
                    'csvHtml5',
                    'print'
                ],
      });
      
      // Apply the search
      agileTribe.table.columns().every( function () {
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
  
  this.listenForSubmitTribeForm = function(){
	  $("#tribeForm" ).submit(function( event ) {
		  console.log('submit clicked');
		  event.preventDefault();
		  $(':submit').addClass('spinning').attr('disabled',true);
	  	  var disabledFields = $(':disabled');
		  $(disabledFields).attr('disabled',false);
		  var formData = $("#tribeForm").serialize();
		  $(disabledFields).attr('disabled',true);
		  $.ajax({
				type:'post',
			  	url: 'ajax/saveAgileTribeRecord.php',
			  	data:formData,
		      	success: function(response) {
		      		var responseObj = JSON.parse(response);
		      		console.log(responseObj);
		      		if(responseObj.success){
			      		$('.modalInfo-body').html("<p>Tribe Record Saved</p>");
			      		$('#modalInfo').modal('show');		      			
		      		} else {
			      		$('.modalInfo-body').html("<p>Save has encountered a problem</p><p>" + responseObj.message + "</p>");
			      		$('#modalInfo').modal('show');
		      		}
		      		$('.spinning').removeClass('spinning').attr('disabled',false);
		      		$('#TRIBE_NUMBER').val('').trigger('change').attr('disabled',false);
		      		$('#TRIBE_NAME').val('');
		      		$('#TRIBE_LEADER').val('');		
		      		agileTribe.table.ajax.reload();
		      	},
		      	fail: function(response){
			      	console.log('Failed');
					console.log(response);
		            $('.modalInfo-body').html("<h2>Json call to save record Failed.Tell Rob</h2>");
		            $('#modalInfo').modal('show');
				},
		      	error: function(error){
		            $('.modalInfo-body').html("<h2>Json call to save record Errored " + error.statusText + " Tell Rob</h2>");
		            $('#modalInfo').modal('show');
		      	}
		  });
	  });
  },
  
  
  this.listenForEditTribe = function(){
	  $(document).on('click','.btnEditTribe',function(){
		  $('#TRIBE_NUMBER').val($(this).data('tribenumber')).trigger('change').attr('disabled',true);
		  $('#TRIBE_NAME').val($(this).data('tribename'));
		  $('#TRIBE_LEADER').val($(this).data('tribeleader'));
		  $('#mode').val('edit');
		  
	  });
  }
  
}

$( document ).ready(function() {
  var AgileTribe = new agileTribe();
  AgileTribe.init();
});
