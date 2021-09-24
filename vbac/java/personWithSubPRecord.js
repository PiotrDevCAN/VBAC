/*
 *
 *
 *
 */

function personWithSubPRecord() {
	
	var table;

  this.init = function(){

  },

  
  this.initialiseDataTable = function(preBoardersAction){
	  
	  $('#personTable').on('draw.dt', function () {
		    $('[data-toggle="popover"]').popover();
		} );
	  
	  $('#personTable').on('column-visibility.dt', function () {
		    $('[data-toggle="popover"]').popover();
    	} );
	  
	  
	  
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
	  
	  
	  
	  
	  preBoardersAction = typeof(preBoardersAction) == 'undefined' ? null : preBoardersAction;	  
      // Setup - add a text input to each footer cell
      $('#personTable tfoot th').each( function () {
          var title = $(this).text();
          var titleCondensed = title.replace(' ','');
          $(this).html( '<input type="text" id="footer'+ titleCondensed + '" placeholder="Search '+title+'" />' );
      } );
    // DataTable
      personWithSubPRecord.table = $('#personTable').DataTable({
        ajax: {
              url: 'ajax/populatePersonWithSubPDatatable.php',
              data: { preBoardersAction:preBoardersAction },
              type: 'GET',
          }	,
//	        CNUM         0
//	        Email        4
//	        Notes ID     5
//	        fm_cnum		 8
//	        PES status   25

          "columns": [
            { "title": "CNUM", "data": "CNUM" , "defaultContent": "" },
            { "title": "OPEN_SEAT_NUMBER", "data": "OPEN_SEAT_NUMBER" ,"defaultContent": "" },
            { "title": "FIRST_NAME", "data": "FIRST_NAME","defaultContent": "<i>unknown</i>"},
            { "title": "LAST_NAME", "data": "LAST_NAME", "defaultContent": "<i>unknown</i>" },
            { "title": "EMAIL_ADDRESS", "data": "EMAIL_ADDRESS", "defaultContent": "<i>unknown</i>" },
            { "title": "NOTES_ID", "data": "NOTES_ID", "defaultContent": "<i>unknown</i>" },
            { "title": "LBG_EMAIL", "data": "LBG_EMAIL", "defaultContent": "<i>unknown</i>" },
            { "title": "EMPLOYEE_TYPE", "data": "EMPLOYEE_TYPE", "defaultContent": "" },
            { "title": "FM_CNUM", "data": "FM_CNUM", "defaultContent": "" },
            { "title": "FM_MANAGER_FLAG", "data": "FM_MANAGER_FLAG", "defaultContent": "" },
            { "title": "CTB_RTB", "data": "CTB_RTB", "defaultContent": "" },
            { "title": "TT_BAU", "data": "TT_BAU", "defaultContent": "" },
            { "title": "LOB", "data": "LOB", "defaultContent": "" },
            { "title": "ROLE_ON_THE_ACCOUNT", "data": "ROLE_ON_THE_ACCOUNT", "defaultContent": "" },
            { "title": "ROLE_TECHNOLOGY", "data": "ROLE_TECHNOLOGY", "defaultContent": "" },
            { "title": "START_DATE", "data": "START_DATE", "defaultContent": "" },
            { "title": "CNPROJECTED_END_DATEM", "data": "PROJECTED_END_DATE", "defaultContent": "" },
            { "title": "COUNTRY", "data": "COUNTRY", "defaultContent": ""},
            { "title": "BASE_LOCATION", "data": "IBM_BASE_LOCATION", "defaultContent": "" },
            { "title": "LBG_LOCATION", "data": "LBG_LOCATION" , "defaultContent": ""},
            { "title": "OFFBOARDED_DATE", "data": "OFFBOARDED_DATE" , "defaultContent": ""},
            { "title": "PES_DATE_REQUESTED", "data": "PES_DATE_REQUESTED" , "defaultContent": ""},
            { "title": "PES_REQUESTOR", "data": "PES_REQUESTOR", "defaultContent": "" },
            { "title": "PES_DATE_RESPONDED", "data": "PES_DATE_RESPONDED", "defaultContent": "" },
            { "title": "PES_STATUS_DETAILS", "data": "PES_STATUS_DETAILS", "defaultContent": "" },
            { "title": "PES_STATUS", "data": "PES_STATUS",
                "render": { _:'display', sort:'sort' },
            },
            { "title": "REVALIDATION_DATE_FIELD", "data": "REVALIDATION_DATE_FIELD", "defaultContent": "" },
            { "title": "REVALIDATION_STATUS", "data": "REVALIDATION_STATUS", "defaultContent": "" },
            { "title": "CBN_DATE_FIELD", "data": "CBN_DATE_FIELD", "defaultContent": "" },
            { "title": "CBN_STATUS", "data": "CBN_STATUS", "defaultContent": "" },
            { "title": "WORK_STREAM", "data": "WORK_STREAM", "defaultContent": "" },
            { "title": "SUBPLATFORM", "data": "SUBPLATFORM", "defaultContent": "" },
            { "title": "CT_ID_REQUIRED", "data": "CT_ID_REQUIRED" , "defaultContent": ""},
            { "title": "CT_ID", "data": "CT_ID", "defaultContent": "" },
            { "title": "CIO_ALIGNMENT", "data": "CIO_ALIGNMENT", "defaultContent": "" },
            { "title": "PRE_BOARDED", "data": "PRE_BOARDED", "defaultContent": "" },
            { "title": "SECURITY_EDUCATION", "data": "SECURITY_EDUCATION", "defaultContent": "" },
            { "title": "PMO_STATUS", "data": "PMO_STATUS", "defaultContent": "" },
            { "title": "PES_DATE_EVIDENCE", "data": "PES_DATE_EVIDENCE", "defaultContent": "" },
            { "title": "RSA_TOKEN", "data": "RSA_TOKEN", "defaultContent": "" },
            { "title": "CALLSIGN_ID", "data": "CALLSIGN_ID", "defaultContent": "" },
            { "title": "PROCESSING_STATUS", "data": "PROCESSING_STATUS", "defaultContent": "" },
            { "title": "PROCESSING_STATUS_CHANGED", "data": "PROCESSING_STATUS_CHANGED", "defaultContent": "" },  
            { "title": "PES_LEVEL", "data": "PES_LEVEL", "defaultContent": "" },
            { "title": "PES_RECHECK_DATE", "data": "PES_RECHECK_DATE", "defaultContent": "" },
            { "title": "PES_CLEARED_DATE", "data": "PES_CLEARED_DATE", "defaultContent": "" },
            { "title": "SQUAD_NAME", "data": "SQUAD_NAME", 
                "render": { _:'display', sort:'sort' },
            },
            { "title": "OLD_SQUAD_NAME", "data": "OLD_SQUAD_NAME", 
                "render": { _:'display', sort:'sort' },
            },
          ],
          columnDefs: [
                         { "visible": false, "targets": [1,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47] }
                  ] ,
//	        colReorder: {
//	            order: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34]
//	        },
          order: [[ 5, "asc" ]],
          
          drawCallback: function( settings ) {
              $('[data-toggle="popover"]').popover();
          },        
          
          
          autoWidth: true,
          deferRender: true,
          processing: true,
          responsive: true,
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
                })
                ],
      });
      // Apply the search
      personWithSubPRecord.table.columns().every( function () {
          var that = this;
          $( 'input', this.footer() ).on( 'keyup change', function () {
              if ( that.search() !== this.value ) {
                  that
                      .search( this.value )
                      .draw();
              }
          } );
      } );
  }
}

$( document ).ready(function() {
  var personWithSubP = new personWithSubPRecord();
  personWithSubP.init();
  
});
