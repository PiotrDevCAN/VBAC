/*
 *
 *
 *
 */

function personWithSubPRecord() {
	
	var table;

  this.init = function(){
    console.log('+++ Function +++ personWithSubPRecord.init');
    console.log('--- Function --- personWithSubPRecord.init');
  },

  
  this.initialiseDataTable = function(preBoardersAction){
	  
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
                      { "data": "CTB_RTB", "defaultContent": "" },
                      { "data": "TT_BAU", "defaultContent": "" },
                      { "data": "LOB", "defaultContent": "" },
                      { "data": "ROLE_ON_THE_ACCOUNT", "defaultContent": "" },
                      { "data": "ROLE_TECHNOLOGY", "defaultContent": "" },
                      { "data": "START_DATE", "defaultContent": "" },
                      { "data": "PROJECTED_END_DATE", "defaultContent": "" },
                      { "data": "COUNTRY", "defaultContent": ""},
                      { "data": "IBM_BASE_LOCATION", "defaultContent": "" },
                      { "data": "LBG_LOCATION" , "defaultContent": ""},
                      { "data": "OFFBOARDED_DATE" , "defaultContent": ""},
                      { "data": "PES_DATE_REQUESTED" , "defaultContent": ""},
                      { "data": "PES_REQUESTOR", "defaultContent": "" },
                      { "data": "PES_DATE_RESPONDED", "defaultContent": "" },
                      { "data": "PES_STATUS_DETAILS", "defaultContent": "" },
                      { "data": "PES_STATUS", "defaultContent": "" },
                      { "data": "REVALIDATION_DATE_FIELD", "defaultContent": "" },
                      { "data": "REVALIDATION_STATUS", "defaultContent": "" },
                      { "data": "CBN_DATE_FIELD", "defaultContent": "" },
                      { "data": "CBN_STATUS", "defaultContent": "" },
                      { "data": "WORK_STREAM", "defaultContent": "" },
                      { "data": "SUBPLATFORM", "defaultContent": "" },
                      { "data": "CT_ID_REQUIRED" , "defaultContent": ""},
                      { "data": "CT_ID", "defaultContent": "" },
                      { "data": "CIO_ALIGNMENT", "defaultContent": "" },
                      { "data": "PRE_BOARDED", "defaultContent": "" },
                      { "data": "SECURITY_EDUCATION", "defaultContent": "" },
                      { "data": "PMO_STATUS", "defaultContent": "" },
                      { "data": "PES_DATE_EVIDENCE", "defaultContent": "" },
                      { "data": "RSA_TOKEN", "defaultContent": "" },
                      { "data": "CALLSIGN_ID", "defaultContent": "" },
                  ],
          columnDefs: [
                         { "visible": false, "targets": [1,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40] }
                  ] ,
//	        colReorder: {
//	            order: [ 0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34]
//	        },
          order: [[ 5, "asc" ]],
          autoWidth: true,
          deferRender: true,
          processing: true,
          responsive: true,
          dom: 'Blfrtip',
          buttons: [
                    'colvis',
                    'excelHtml5',
                    'csvHtml5',
                    'print'
                ],
      });
      // Apply the search
      personWithSubPRecord.table.columns().every( function () {
          var that = this;
          $( 'input', this.footer() ).on( 'keyup change', function () {
        	  console.log(this.value);
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
