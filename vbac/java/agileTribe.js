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


function agileTribe() {

  var table;
  var spinner =  '<div id="overlay"><i class="fa fa-spinner fa-spin spin-big"></i></div>';

  this.init = function(){
  },

  this.listenForLeader = function(){
	 	$('.typeahead').bind('typeahead:select', function(ev, suggestion) {
	 		$('.tt-menu').hide();
	 		$('#TRIBE_LEADER').val(suggestion.notesEmail);
		});

  },

  this.initialiseAgileTribeTable = function(version){	  
	 
    // Setup - add a text input to each footer cell
    $('#tribeTable tfoot th').each( function () {
    	 var title = $(this).text();
    	 $(this).html( '<input type="text" id="footer'+ title + '" placeholder="Search '+title+'" />' );
    } );
    // DataTable
    agileTribe.table = $('#tribeTable').DataTable({
        ajax: {
              url: 'ajax/populateAgileTribeTable.php',
              data: function(d){
            	  var version = $('#version').prop('checked') ? 'Original' : 'New';
                  d.version = version;                  
                  },
              type: 'POST',
          }	,
          columns: [
                      { "data": "TRIBE_NUMBER", render: { _:"display", sort:"sort" } },
                      { "data": "TRIBE_NAME"   },
                      { "data": "TRIBE_LEADER" },
                      { "data": "ITERATION_MGR", "defaultContent":"<i>To be assigned</i>"  },
                      { "data": "ORGANISATION" },
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
 							 $('c[r=A1] t', sheet).text( 'Ventus Tribes : ' + now );

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
		  var verData = $('#version').prop('checked') ? '&version=Original' : '&version=New';
		  
		  console.log(formData);
		  console.log(verData);
		  
		  
		  
		  $(disabledFields).attr('disabled',true);
		  $.ajax({
				type:'post',
			  	url: 'ajax/saveAgileTribeRecord.php',
			  	data:formData + verData,
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
		      		$('#ITERATION_MGR').val('');
		      		$('#radioTribeOrganisationManaged').prop('checked', true)
		      		agileTribe.table.ajax.reload();
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
  },
  
  
  this.listenForEditTribe = function(){
	  $(document).on('click','.btnEditTribe',function(){
		  $('#TRIBE_NUMBER').val($(this).data('tribenumber')).trigger('change').attr('disabled',true);
		  $('#TRIBE_NAME').val($(this).data('tribename'));
		  $('#TRIBE_LEADER').val($(this).data('tribeleader'));
		  $('#ITERATION_MGR').val($(this).data('iterationmgr'));

		  
		  console.log($(this));
		  console.log($(this).data('organisation'));
		  
		  
		  if($(this).data('organisation')=='Managed Services'){
			  $('#radioTribeOrganisationManaged').prop('checked', true)
		  } else {
			  $('#radioTribeOrganisationProject').prop('checked', true)
		  }
		  $('#mode').val('edit');
		  
	  });
  }
  
}

$( document ).ready(function() {
  var AgileTribe = new agileTribe();
  AgileTribe.init();
});
