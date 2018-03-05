/**
 *
 */


function assetPortal() {
	var table;

  this.init = function(){
    console.log('+++ Function +++ assetPortal.init');
    console.log('--- Function --- assetPortal.init');
  },

  this.initialiseAssetRequestPortal = function(){
      $.ajax({
	        url: "ajax/createHtmlForAssetPortal.php",
	        type: 'GET',
	        success: function(result){
	          var AssetPortal = new assetPortal();
	          $('#assetRequestsDatatablesDiv').html(result);
	          AssetPortal.initialiseAssetRequestDataTable();
	        }
      });
  },
  
  
  this.initialiseAssetRequestDataTable = function(){
	     // Setup - add a text input to each footer cell
    $('#assetPortalTable tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input type="text" id="footer'+ title + '" placeholder="Search '+title+'" />' );
      } );
    // DataTable
    assetPortal.table = $('#assetPortalTable').DataTable({
        ajax: {
              url: 'ajax/populateAssetRequestPortal.php',
              type: 'POST',
          }	,
          "columns": [
                      { "data": "REQUEST_REFERENCE" , "defaultContent": "" },
                      { "data": "CNUM" ,"defaultContent": "" },
                      { "data": "ASSET_TITLE","defaultContent": "<i>unknown</i>"},
                      { "data": "USER_LOCATION", "defaultContent": "<i>unknown</i>" },
                      { "data": "PRIMARY_UID", "defaultContent": "<i>unknown</i>" },
                      { "data": "SECONDARY_UID", "defaultContent": "<i>unknown</i>" },
                      { "data": "DATE_ISSUED_TO_IBM", "defaultContent": "<i>unknown</i>" },
                      { "data": "DATE_ISSUED_TO_USER", "defaultContent": "" },
                      { "data": "DATE_RETURNED", "defaultContent": "" },
                      { "data": "BUSINESS_JUSTIFICATION", "defaultContent": "" },
                      { "data": "PRE_REQ_REQUEST", "defaultContent": "" },
                      { "data": "REQUESTOR_EMAIL", "defaultContent": "" },
                      { "data": "REQUESTED", "defaultContent": "" },
                      { "data": "APPROVER_EMAIL", "defaultContent": "" },
                      { "data": "APPROVED_DATE", "defaultContent": "" },
                      { "data": "EDUCATION_CONFIRMATION", "defaultContent": "" },
                      { "data": "STATUS", "defaultContent": "" },
                      { "data": "ORDERIT_VBAC_REF", "defaultContent": ""},
                      { "data": "ORDERIT_NUMBER", "defaultContent": "" },
                      { "data": "ORDERIT_STATUS" , "defaultContent": ""}                   
                  ],
          columnDefs: [
                         { "visible": false, "targets": [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19] }
                  ] ,
          order: [[ 5, "asc" ]],
          autoWidth: false,
          deferRender: true,
          responsive: false,
          // scrollX: true,
          processing: true,
          responsive: true,
          dom: 'Blfrtip',
//	      colReorder: true,
          buttons: [
                    'colvis',
                    'excelHtml5',
                    'csvHtml5',
                    'print'
                ],
      });
      // Apply the search
      assetPortal.table.columns().every( function () {
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

};


var AssetPortal = new assetPortal();

$( document ).ready(function() {
	AssetPortal = new assetPortal();
	AssetPortal.init();
});