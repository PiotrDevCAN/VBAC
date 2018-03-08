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
          columns: [
                      { "data": "REFERENCE" , "defaultContent": "" },
                      { "data": "CT_ID" ,"defaultContent": "<i>no ctid</i>" },
                      { "data": "PERSON" ,"defaultContent": "" },
                      { "data": "ASSET","defaultContent": "<i>unknown</i>"},
                      { "data": "STATUS", "defaultContent": "" },
                      { "data": "JUSTIFICATION", "defaultContent": "" },
                      { "data": "REQUESTOR", "defaultContent": "" },
                      { "data": "APPROVER", "defaultContent": "" },                                           
                      { "data": "USER_LOCATION", "defaultContent": "<i>unknown</i>" },
                      { "data": "PRIMARY_UID", "defaultContent": "<i>unknown</i>" },
                      { "data": "SECONDARY_UID", "defaultContent": "<i>unknown</i>" },
                      { "data": "DATE_ISSUED_TO_IBM", "defaultContent": "<i>unknown</i>" },
                      { "data": "DATE_ISSUED_TO_USER", "defaultContent": "" },
                      { "data": "DATE_RETURNED", "defaultContent": "" },
                      { "data": "EDUCATION_CONFIRMATION", "defaultContent": "" },                  
                      { "data": "ORDERIT_VBAC_REF", "defaultContent": ""},
                      { "data": "ORDERIT_NUMBER", "defaultContent": "" },
                      { "data": "ORDERIT_STATUS" , "defaultContent": ""},
                      { "data": "ORDERIT_TYPE" , "defaultContent": ""}
                  ],
          columnDefs: [
                         { visible: false, targets: [8,9,10,11,12,13,14,15,16,17,18] }
                  ] ,
          order: [[ 5, "asc" ]],
          autoWidth: true,
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
                      .adjust()
                      .draw();
              }
          } );
      } );
  },
  
  this.listenForReportReset = function(){
	    $(document).on('click','#reportReset', function(e){
	    	$('#portalTitle').text('Asset Request Portal');
	    	$.fn.dataTableExt.afnFiltering.pop();
	    	assetPortal.table.columns().visible(false,false);
	    	assetPortal.table.columns([0,1,2,3,4,5,6,7]).visible(true);
	    	assetPortal.table.search('').order([0,"asc"]).draw();
	    });
  },  
  
  this.listenForReportReload = function(){
	    $(document).on('click','#reportReload', function(e){
	    	$('#portalTitle').text('Asset Request Portal');
	    	$.fn.dataTableExt.afnFiltering.pop();
	    	assetPortal.table.ajax.reload();
	      });
  },
  
  this.listenForExportButton = function(){
	  $(document).on('click','#exportForOrderIt', function(e){
		 alert('export');
	  });
  }
  
  

};


var AssetPortal = new assetPortal();

$( document ).ready(function() {
	AssetPortal = new assetPortal();
	AssetPortal.init();
});