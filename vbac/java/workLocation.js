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

function workLocation() {

  var table;
  var spinner =  '<div id="overlay"><i class="fa fa-spinner fa-spin spin-big"></i></div>';

  this.init = function(){
  },

  this.initialiseWorkLocationTable = function(){	  
	 
    // Setup - add a text input to each footer cell
    $('#workLocationTable tfoot th').each( function () {
    	 var title = $(this).text();
    	 $(this).html( '<input type="text" id="footer'+ title + '" placeholder="Search '+title+'" />' );
    } );
    // DataTable
    workLocation.table = $('#workLocationTable').DataTable({
        ajax: {
            url: 'ajax/populateWorkLocationTable.php',
            data: function(d){},
            type: 'POST',
        }	,
        columns: [
            { "data": "COUNTRY", render: { _:"display", sort:"sort" } },
            { "data": "CITY" },
            { "data": "ADDRESS" },
            { "data": "ONSHORE" },
            { "data": "CBC_IN_PLACE" },
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
      workLocation.table.columns().every( function () {
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
  
  this.listenForSubmitLocationForm = function(){
	  $("#workLocationForm" ).submit(function( event ) {
		  console.log('submit clicked');
		  event.preventDefault();
		  $(':submit').addClass('spinning').attr('disabled',true);
	  	  var disabledFields = $(':disabled');
		  $(disabledFields).attr('disabled',false);
		  var formData = $("#workLocationForm").serialize();
		  
		  console.log(formData);
		  
		  $(disabledFields).attr('disabled',true);
		  $.ajax({
				type:'post',
			  	url: 'ajax/saveWorkLocationRecord.php',
			  	data:formData,
		      	success: function(response) {
		      		var responseObj = JSON.parse(response);
		      		console.log(responseObj);
		      		if(responseObj.success){
			      		$('.modalInfo-body').html("<p>Work Location Record Saved</p>");
			      		$('#modalInfo').modal('show');		      			
		      		} else {
			      		$('.modalInfo-body').html("<p>Save has encountered a problem</p><p>" + responseObj.message + "</p>");
			      		$('#modalInfo').modal('show');
		      		}
		      		$('.spinning').removeClass('spinning').attr('disabled',false);
                    $('#COUNTRY').val('');
		      		$('#CITY').val('');
		      		$('#ADDRESS').val('');	
		      		$('#ONSHORE').val('');
                    $('#CBC_IN_PLACE').val('');
		      		workLocation.table.ajax.reload();
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
  
  this.listenForEditLocation = function(){
	  $(document).on('click','.btnEditLocation',function(){

          $('#COUNTRY').val($(this).data('country'));
		  $('#CITY').val($(this).data('city'));
		  $('#ADDRESS').val($(this).data('address'));
		  $('#ONSHORE').val($(this).data('onshore'));
          $('#CBC_IN_PLACE').val($(this).data('cbcinplace'));

		  console.log($(this));

		  $('#mode').val('edit');		  
	  });
  }
}

$( document ).ready(function() {
    var location = new workLocation();
    location.init();
});
