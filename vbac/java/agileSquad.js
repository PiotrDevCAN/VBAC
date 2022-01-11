 /*
 *
 *
 *
 */

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

function agileSquad() {

  var table;
  var spinner =  '<div id="overlay"><i class="fa fa-spinner fa-spin spin-big"></i></div>';

  this.init = function(){
  };

  this.listenForLeader = function(){
	 	$('.typeahead').bind('typeahead:select', function(ev, suggestion) {
	 		$('.tt-menu').hide();
	 		$('#TRIBE_LEADER').val(suggestion.notesEmail);
		});

  };


  this.initialiseAgileSquadTable = function(version){	  
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
              data: function(d){
            	  var version = $('#version').prop('checked') ? 'Original' : 'New';
                  d.version = version;                  
                  },
              type: 'POST'
          }	,
          columns: [
                      { "data": "SQUAD_NUMBER", render: { _:"display", sort:"sort" } },
                      { "data": "ORGANISATION"   },
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
                    $.extend( true, {}, buttonCommon, {
                        extend: 'excelHtml5',
                        exportOptions: {
                            orthogonal: 'sort',
                            stripHtml: true,
                            stripNewLines:false
                        },
                         customize: function( xlsx ) {
                             var sheet = xlsx.xl.worksheets['sheet1.xml'];
                             var now = new Date();
 							 $('c[r=A1] t', sheet).text( 'Ventus Squads : ' + now );

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
                $.extend( true, {}, buttonCommon, {
                    extend: 'print',
                    exportOptions: {
                        orthogonal: 'sort',
                        stripHtml: true,
                        stripNewLines:false
                    }
                }),
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
  };
  
  this.listenForSubmitSquadForm = function(){
	  $("#squadForm" ).submit(function( event ) {
		  $(':submit').addClass('spinning').attr('disabled',true);
		  console.log('submit clicked');
		  event.preventDefault();
	  	  var disabledFields = $(':disabled');
		  $(disabledFields).attr('disabled',false);
		  var formData = $("#squadForm").serialize();
		  var verData = $('#version').prop('checked') ? '&version=Original' : '&version=New';
		  
		  
		  $(disabledFields).attr('disabled',true);
		  $.ajax({
				type:'post',
			  	url: 'ajax/saveAgileSquadRecord.php',
			  	data:formData + verData,
		      	success: function(response) {
		      		var responseObj = JSON.parse(response);
		      		console.log(responseObj);
		      		if(responseObj.success){
			      		$('.modalInfo-body').html("<p>Squad Record Saved</p>");
			      		$('#modalInfo').modal('show');		      			
		      		} else {
			      		$('.modalInfo-body').html("<p>Save has encountered a problem</p><p>" + responseObj.messages + "</p>");
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
		            $('.modalInfo-body').html("<h2>Json call to save record Failed.Tell Piotr</h2>");
		            $('#modalInfo').modal('show');
				},
		      	error: function(error){
		            $('.modalInfo-body').html("<h2>Json call to save record Errored " + error.statusText + " Tell Piotr</h2>");
		            $('#modalInfo').modal('show');
		      	}
		  });
	  });
  };
  
  
  this.listenForEditSquad = function(){
	  $(document).on('click','.btnEditSquad',function(){
		  $('#SQUAD_NUMBER').val($(this).data('squadnumber')).trigger('change').attr('disabled',true);
		  $('#SQUAD_TYPE').val($(this).data('squadtype'));
		  $('#TRIBE_NUMBER').val('').trigger('change');
		  if($(this).data('organisation')=='Managed Services'){
			  $('#radioTribeOrganisationManaged').prop('checked',true);			  
		  } else {
			  $('#radioTribeOrganisationProject').prop('checked',true);		
		  }
		  initialiseTribeNumber($(this).data('tribenumber'));  
		  $('#SHIFT').val($(this).data('shift')).trigger('change');
		  $('#SQUAD_LEADER').val($(this).data('squadleader'));
		  $('#SQUAD_NAME').val($(this).data('squadname'));
		  $('#mode').val('edit');
	  });
  };
  
}

$( document ).ready(function() {
  var AgileSquad = new agileSquad();
  AgileSquad.init();
});
