 /*
 *
 *
 *
 */


function agileSquad() {

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


  this.initialiseAgileSquadTable = function(){	  
	console.log('initialiseAgileSquadTable');
	 
    // Setup - add a text input to each footer cell
    $('#tribeTable tfoot th').each( function () {
    	 var title = $(this).text();
    	 $(this).html( '<input type="text" id="footer'+ title + '" placeholder="Search '+title+'" />' );
    } );
    // DataTable
    agileSquad.table = $('#squadTable').DataTable({
        ajax: {
              url: 'ajax/populateAgileSquadTable.php',
              type: 'GET',
          }	,
          columns: [
                      { "data": "SQUAD_NUMBER", render: { _:"display", sort:"sort" } },
                      { "data": "SQUAD_TYPE"   },
                      { "data": "SQUAD_NAME" },
                      { "data": "TRIBE_NUMBER" },
                      { "data": "SHIFT" },
                      { "data": "SQUAD_LEADER" },
                     
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
      agileSquad.table.columns().every( function () {
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
  
  this.listenForSubmitSquadForm = function(){
	  $("#squadForm" ).submit(function( event ) {
		  $(':submit').addClass('spinning').attr('disabled',true);
		  console.log('submit clicked');
		  event.preventDefault();
	  	  var disabledFields = $(':disabled');
		  $(disabledFields).attr('disabled',false);
		  var formData = $("#squadForm").serialize();
		  $(disabledFields).attr('disabled',true);
		  $.ajax({
				type:'post',
			  	url: 'ajax/saveAgileSquadRecord.php',
			  	data:formData,
		      	success: function(response) {
		      		var responseObj = JSON.parse(response);
		      		console.log(responseObj);
		      		if(responseObj.success){
			      		$('.modalInfo-body').html("<p>Squad Record Saved</p>");
			      		$('#modalInfo').modal('show');		      			
		      		} else {
			      		$('.modalInfo-body').html("<p>Save has encountered a problem</p><p>" + responseObj.message + "</p>");
			      		$('#modalInfo').modal('show');
		      		}
		      		$('.spinning').removeClass('spinning').attr('disabled',false);
		      		$('#SQUAD_NUMBER').val('').trigger('change').attr('disabled',false);
		      		$('#SQUAD_TYPE').val('');
		      		$('#SQUAD_NAME').val('');
		      		$('#TRIBE_NUMBER').val('').trigger('change').attr('disabled',false);
		      		$('#SHIFT').val('').trigger('change').attr('disabled',false);
		      		$('#SQUAD_LEADER').val('');		
		      		agileSquad.table.ajax.reload();
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
  
  
  this.listenForEditSquad = function(){
	  $(document).on('click','.btnEditSquad',function(){
		  
		  populateTribeDropDown();
		  
		  $('#SQUAD_NUMBER').val($(this).data('squadnumber')).trigger('change').attr('disabled',true);
		  $('#SQUAD_TYPE').val($(this).data('squadtype'));
		  $('#TRIBE_NUMBER').val($(this).data('tribenumber')).trigger('change');
		  $('#SHIFT').val($(this).data('shift')).trigger('change');
		  $('#SQUAD_LEADER').val($(this).data('squadleader'));
		  $('#SQUAD_NAME').val($(this).data('squadname'));
		  $('#mode').val('edit');
		  
		  var organisation =  $('#TRIBE_NUMBER').find(':selected').data('organisation');
		  
		  console.log(organisation);
		  
		  console.log($("input[name='Organisation'][value='" + organisation + "']"));
		  
		  $("input[name='Organisation'][value='" + organisation + "']").prop('checked', true);
		  
	      $('#TRIBE_NUMBER > option[data-organisation!="' + organisation + '"]').remove();
		  
	  });
  }
  
}

$( document ).ready(function() {
  var AgileSquad = new agileSquad();
  AgileSquad.init();
});
