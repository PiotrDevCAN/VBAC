/*
 *
 *
 *
 */

function personPortalLite() {
	
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
      personPortalLite.table = $('#personTable').DataTable({
	        ajax: {
              url: 'ajax/populatePersonPortalLite.php',
              data: { preBoardersAction:preBoardersAction },
              type: 'GET',
          }	,

         "columns": [
                      { "data": "CNUM" , "defaultContent": "" },
                      { "data": "OPEN_SEAT_NUMBER" ,"defaultContent": "" },
                      { "data": "FIRST_NAME"       ,"defaultContent": "<i>unknown</i>"},
                      { "data": "LAST_NAME", "defaultContent": "<i>unknown</i>" },
                      { "data": "EMAIL_ADDRESS", "defaultContent": "<i>unknown</i>" },
                      { "data": "NOTES_ID", "defaultContent": "<i>unknown</i>" },
                      { "data": "LBG_EMAIL", "defaultContent": "<i>unknown</i>" },
                      { "data": "EMPLOYEE_TYPE", "defaultContent": "" },
                      { "data": "FM_CNUM", "defaultContent": "" },
                      { "data": "FM_MANAGER_FLAG", "defaultContent": "" },
                      { "data": "TT_BAU", "defaultContent": "" },
                      { "data": "LOB", "defaultContent": "" },
                      { "data": "ROLE_ON_THE_ACCOUNT", "defaultContent": "" },
                      { "data": "START_DATE", "defaultContent": "" },
                      { "data": "PROJECTED_END_DATE", "defaultContent": "" },
                      { "data": "COUNTRY", "defaultContent": ""},
                      { "data": "IBM_BASE_LOCATION", "defaultContent": "" },
                      { "data": "LBG_LOCATION" , "defaultContent": ""},
                      { "data": "PES_DATE_REQUESTED" , "defaultContent": ""},
                      { "data": "PES_REQUESTOR", "defaultContent": "" },
                      { "data": "PES_DATE_RESPONDED", "defaultContent": "" },
                      { "data": "PES_STATUS_DETAILS", "defaultContent": "" },
                      { data: "PES_STATUS",
                   	  render: { _:'display', sort:'sort' },
                      },
                      { "data": "REVALIDATION_DATE_FIELD", "defaultContent": "" },
                      { "data": "REVALIDATION_STATUS", "defaultContent": "" },
                      { "data": "CBN_DATE_FIELD", "defaultContent": "" },
                      { "data": "CBN_STATUS", "defaultContent": "" },
                      { "data": "WORK_STREAM", "defaultContent": "" },
                      { "data": "SUBPLATFORM", "defaultContent": "" },
                      { "data": "CT_ID", "defaultContent": "" },
                      { "data": "PRE_BOARDED", "defaultContent": "" },
                      { "data": "PES_DATE_EVIDENCE", "defaultContent": "" },
                      { "data": "RSA_TOKEN", "defaultContent": "" },
                      { "data": "CALLSIGN_ID", "defaultContent": "" },
                      { "data": "PROCESSING_STATUS", "defaultContent": "" },
                      { "data": "PROCESSING_STATUS_CHANGED", "defaultContent": "" },  
                      { "data": "PES_LEVEL", "defaultContent": "" },
                      { "data": "PES_RECHECK_DATE", "defaultContent": "" },
                      { "data": "PES_CLEARED_DATE", "defaultContent": "" },
                      { "data": "SQUAD_NUMBER", "defaultContent": "" },                      
					  { "data": "SQUAD_NAME", render: { _:'display', sort:'sort' },},
			          { "data": "SQUAD_LEADER", "defaultContent": "" },
         			  { "data": "TRIBE_NUMBER", "defaultContent": "" },
					  { "data": "TRIBE_NAME", "defaultContent": "" },
			          { "data": "TRIBE_LEADER", "defaultContent": "" },	
         			  { "data": "ORGANISATION", "defaultContent": "" },	
                    
                      
                  ],

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

  }
}

$( document ).ready(function() {
  var PersonPortalLite = new personPortalLite();
  PersonPortalLite.init();
  
});
