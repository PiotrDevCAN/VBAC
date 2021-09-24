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
            { "name": "CNUM", "data": "CNUM", "defaultContent": "", visible:true },							//00
            { "name": "OPEN_SEAT_NUMBER", "data": "OPEN_SEAT_NUMBER", "defaultContent": "", visible:false },				//01
            { "name": "FIRST_NAME", "data": "FIRST_NAME", "defaultContent": "<i>unknown</i>", visible:true},	//02
            { "name": "LAST_NAME", "data": "LAST_NAME", "defaultContent": "<i>unknown</i>", visible:true },		//03
            { "name": "EMAIL_ADDRESS", "data": "EMAIL_ADDRESS", "defaultContent": "<i>unknown</i>", visible:true },	//04
            { "name": "NOTES_ID", "data": "NOTES_ID", "defaultContent": "<i>unknown</i>", visible:true },			//05
            { "name": "LBG_EMAIL", "data": "LBG_EMAIL", "defaultContent": "<i>unknown</i>", visible:false },		//06
            { "name": "EMPLOYEE_TYPE", "data": "EMPLOYEE_TYPE", "defaultContent": "", visible:false },					//07
            { "name": "FM_CNUM", "data": "FM_CNUM", "defaultContent": "", visible:false },						//08
            { "name": "FM_MANAGER_FLAG", "data": "FM_MANAGER_FLAG", "defaultContent": "", visible:false },				//09	
            { "name": "TT_BAU", "data": "TT_BAU", "defaultContent": "", visible:false },						//10
            { "name": "LOB", "data": "LOB", "defaultContent": "", visible:false },							//11
            { "name": "ROLE_ON_THE_ACCOUNT", "data": "ROLE_ON_THE_ACCOUNT", "defaultContent": "", visible:false },			//12	
            { "name": "START_DATE", "data": "START_DATE", "defaultContent": "", visible:false },					//13
            { "name": "PROJECTED_END_DATE", "data": "PROJECTED_END_DATE", "defaultContent": "", visible:false },			//14
            { "name": "COUNTRY", "data": "COUNTRY", "defaultContent": "", visible:false},						//15
            { "name": "BASE_LOCATION", "data": "IBM_BASE_LOCATION", "defaultContent": "", visible:false },				//16	
            { "name": "LBG_LOCATION", "data": "LBG_LOCATION", "defaultContent": "", visible:false},					//17
            { "name": "PES_DATE_REQUESTED", "data": "PES_DATE_REQUESTED" , "defaultContent": "", visible:false},			//18
            { "name": "PES_REQUESTOR", "data": "PES_REQUESTOR", "defaultContent": "" , visible:false},					//19
            { "name": "PES_DATE_RESPONDED", "data": "PES_DATE_RESPONDED", "defaultContent": "" , visible:false},			//20	
            { "name": "PES_STATUS_DETAILS", "data": "PES_STATUS_DETAILS", "defaultContent": "" , visible:false},			//21
            { "name": "PES_STATUS", "data": "PES_STATUS",																//22
              "render": { _:'display', sort:'sort', visible:true },
            },
            { "name": "REVALIDATION_DATE_FIELD", "data": "REVALIDATION_DATE_FIELD", "defaultContent": "", visible:false },		//23
            { "name": "REVALIDATION_STATUS", "data": "REVALIDATION_STATUS", "defaultContent": "", visible:false },			//24
            { "name": "CBN_DATE_FIELD", "data": "CBN_DATE_FIELD", "defaultContent": "", visible:false },				//25
            { "name": "CBN_STATUS", "data": "CBN_STATUS", "defaultContent": "", visible:false },					//26
            { "name": "WORK_STREAM", "data": "WORK_STREAM", "defaultContent": "", visible:false },					//27
            { "name": "CT_ID", "data": "CT_ID", "defaultContent": "", visible:false },							//29	
            { "name": "PRE_BOARDED", "data": "PRE_BOARDED", "defaultContent": "" , visible:false},					//30
            { "name": "PES_DATE_EVIDENCE", "data": "PES_DATE_EVIDENCE", "defaultContent": "" , visible:false},				//31
            { "name": "RSA_TOKEN", "data": "RSA_TOKEN", "defaultContent": "", visible:false },						//32
            { "name": "CALLSIGN_ID", "data": "CALLSIGN_ID", "defaultContent": "" , visible:false},					//33
            { "name": "PROCESSING_STATUS", "data": "PROCESSING_STATUS", "defaultContent": "", visible:false },				//34
            { "name": "PROCESSING_STATUS_CHANGED", "data": "PROCESSING_STATUS_CHANGED", "defaultContent": "" , visible:false},  	//35	
            { "name": "PES_LEVEL", "data": "PES_LEVEL", "defaultContent": "", visible:false },						//36
            { "name": "PES_RECHECK_DATE", "data": "PES_RECHECK_DATE", "defaultContent": "" , visible:false},				//37
            { "name": "PES_CLEARED_DATE", "data": "PES_CLEARED_DATE", "defaultContent": "" , visible:false},				//38
            { "name": "SQUAD_NUMBER", "data": "SQUAD_NUMBER", "defaultContent": "", visible:false },             //39	    			
            { "name": "SQUAD_NAME", "data": "SQUAD_NAME", 
              "render": { _:'display', sort:'sort',  visible:false},                    //40
            },
            { "name": "SQUAD_LEADER", "data": "SQUAD_LEADER", "defaultContent": "", visible:false },					//41
            { "name": "TRIBE_NUMBER", "data": "TRIBE_NUMBER", "defaultContent": "", visible:false },					//42
            { "name": "TRIBE_NAME", "data": "TRIBE_NAME", "defaultContent": "", visible:false },					//43
            { "name": "TRIBE_LEADER", "data": "TRIBE_LEADER", "defaultContent": "", visible:false },					//44
            { "name": "ORGANISATION", "data": "ORGANISATION", "defaultContent": "", visible:false },					//45
            { "name": "ITERATION_MGR", "data": "ITERATION_MGR", "defaultContent": "", visible:false },					//46
            { "name": "PMO_STATUS", "data": "PMO_STATUS", "defaultContent": "", visible:false },					//47                    	
            { "name": "HAS_DELEGATES", "data": "HAS_DELEGATES", "defaultContent": "", visible:false },					//48
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
      // Apply the search
      personPortalLite.table.columns().every( function () {
          var that = this;

          $( 'input', this.footer() ).on( 'keyup change', function () {
              if ( that.search() !== this.value ) {
                  that
                      .search( this.value )
                      .draw();
              }
          } );
      });


  },

  this.listenForReportAction = function(){
    $(document).on('click','#reportAction', function(e){
     	$('#reportRemoveOffb').attr('disabled',false);
    	$('#portalTitle').text('Person Portal - Action Mode');
    	$.fn.dataTableExt.afnFiltering.pop();
    	 personPortalLite.table.columns().visible(false,false);
    	 personPortalLite.table.columns([0,1,5,9,22,40]).visible(true);
    	 personPortalLite.table.order([5,'asc']).draw();
      });
  },

  this.listenForReportOffboarding = function(){
	    $(document).on('click','#reportOffboarding', function(e){
	     	$('#reportRemoveOffb').attr('disabled',false);
	    	$('#portalTitle').text('Person Portal - Offboarding Report');
	    	$.fn.dataTableExt.afnFiltering.pop();
	        $.fn.dataTableExt.afnFiltering.push(
	    		    function(oSettings, aData, iDataIndex){
	    		    	var dat = new Date();
	    		    	dat.setDate(dat.getDate() +31);

	    		    	var month = "00".concat(dat.getMonth()+1).substr(-2);
	    		    	var day   = "00".concat(dat.getDate()).substr(-2);
	    		    	var thirtyDaysHence = dat.getFullYear() + "-" + month + "-" + day;
	    		        var dateEnd = thirtyDaysHence;
	    		        // aData represents the table structure as an array of columns, so the script access the date value
	    		        // in the first column of the table via aData[0]
	    		        var projectedEndDate= aData[16];
	    		        var revalidationStatus = aData[27];
	    		        
	    		        if (projectedEndDate != '' && projectedEndDate != '2000-01-01' &&  projectedEndDate <= dateEnd && (revalidationStatus.trim().substr(0,10) != 'preboarder' && revalidationStatus.trim().substr(0,10) != 'offboarded')) {
	    		            return true;
	    		        }
	    		        else if(revalidationStatus.trim().substr(0,6) == 'leaver' || revalidationStatus.trim().substr(0,11) == 'offboarding'  ){
	    		        	return true;

	    		        } else {
	    		            return false;
	    		        }
	    		});

	        personPortalLite.table.columns().visible(false,false);
	        personPortalLite.table.columns([5,9,10,11,14,24]).visible(true);
	        personPortalLite.table.order([14,'asc'],[5,'asc']);

	        personPortalLite.table.draw();
	      });
	  },

	  this.listenForReportOffboarded = function(){
		    $(document).on('click','#reportOffboarded', function(e){
		     	$('#reportRemoveOffb').attr('disabled',false);
		    	$('#portalTitle').text('Person Portal - Offboarded Report');
		    	$.fn.dataTableExt.afnFiltering.pop();
		        $.fn.dataTableExt.afnFiltering.push(
		    		    function(oSettings, aData, iDataIndex){
		    		        // aData represents the table structure as an array of columns, so the script access the date value
		    		        // in the first column of the table via aData[0]
		    		        var revalidationStatus = aData[27];
		    		        
		    		        if (revalidationStatus.trim().substr(0,10) == 'offboarded') {
		    		            return true;
		    		        } else {
		    		            return false;
		    		        }
		    		});

		        personPortalLite.table.columns().visible(false,false);
		        personPortalLite.table.columns([5,9,10,11,14,24]).visible(true);
		        personPortalLite.table.order([14,'asc'],[5,'asc']);

		        personPortalLite.table.draw();

//		      personRecord.table.column(27).data().each().function(){console.log(this)});
//		      $.fn.dataTableExt.afnFiltering.pop(); - if we pop off here - then when we sort on a column all the rows are back.
		      });
		  },



  this.listenForReportPes = function(){
    $(document).on('click','#reportPes', function(e){
     	$('#reportRemoveOffb').attr('disabled',false);
    	$('#portalTitle').text('Person Portal - PES Report');
    	$.fn.dataTableExt.afnFiltering.pop();
    	 personPortalLite.table.columns().visible(false,false);
    	 personPortalLite.table.columns([5,18,19,20,22,24,30,31]).visible(true);
    	 personPortalLite.table.order([18,'desc'],[5,"asc"]).draw();
      });
  },

  this.listenForReportRevalidation = function(){
    $(document).on('click','#reportRevalidation', function(e){
     	$('#reportRemoveOffb').attr('disabled',false);
    	$('#portalTitle').text('Person Portal - Revalidation Report');
    	$.fn.dataTableExt.afnFiltering.pop();
    	 personPortalLite.table.columns().visible(false,false);
    	 personPortalLite.table.columns([5,8,13,14,23,24]).visible(true);
    	 personPortalLite.table.search('').order([5,'asc']).draw();
    });
  },
  this.showReportMgrsCbn = function(){
	 	$('#reportRemoveOffb').attr('disabled',false);
	  	$('#portalTitle').text('Person Portal - Managers CBN Report');
		$.fn.dataTableExt.afnFiltering.pop();
		 personPortalLite.table.columns().visible(false,false);
		 personPortalLite.table.columns([0,5,9,14,22,24,40,43]).visible(true);
		 personPortalLite.table.search('').order([5,'asc']).draw();	
  },
  this.listenForReportMgrsCbn = function(){
	    $(document).on('click','#reportMgrsCbn', function(e){
			var person2 = new personPortalLite();
			person2.showReportMgrsCbn();
    });
  },
  this.listenForReportAll = function(){
    $(document).on('click','#reportAll', function(e){
    	$('#portalTitle').text('Person Portal - All Columns');
    	$('#reportRemoveOffb').attr('disabled',false);
    	$.fn.dataTableExt.afnFiltering.pop();
    	 personPortalLite.table.columns().visible(true);
    	 personPortalLite.table.columns().search('');
    	 personPortalLite.table.order([5,"asc"]).draw();
      });
  },
  this.listenForReportReload = function(){
    $(document).on('click','#reportReload', function(e){
    	$('#portalTitle').text('Person Portal');
    	$.fn.dataTableExt.afnFiltering.pop();
    	 personPortalLite.table.ajax.reload();
      });
  },
  this.listenForReportReset = function(){
    $(document).on('click','#reportReset', function(e){
    	$('#portalTitle').text('Person Portal');
    	$('#reportRemoveOffb').attr('disabled',false);
    	$.fn.dataTableExt.afnFiltering.pop();
    	 personPortalLite.table.columns().visible(false,false);
    	 personPortalLite.table.columns([0,2,3,4,5]).visible(true);
    	 personPortalLite.table.search('').order([5,"asc"]).draw();
    });
  },
  this.listenForReportRemoveOffb = function(){
	  $(document).on('click','#reportRemoveOffb', function(e){
		  	$('#portalTitle').html($('#portalTitle').text() + "<span style='color:red;font-size:14px'><br/>Offboarding & Offboarded hidden</span>");
		    $.fn.dataTable.ext.search.push(
		    	      function(settings, data, dataIndex) {
		    	          return data[24].trim().substring(0,3) != "off";
		    	        }
		    	    );
		    personPortalLite.table.draw();	
			$('#reportRemoveOffb').attr('disabled',true);
	  });
  },
  this.listenForReportSquads = function(){
    $(document).on('click','#reportSquads', function(e){
       	$('#portalTitle').text('Person Portal - Squad Details');
    	$.fn.dataTableExt.afnFiltering.pop();
    	 personPortalLite.table.columns().visible(false,false);
    	 personPortalLite.table.columns([5,8,40,41,43,44,45,46]).visible(true);
    	 personPortalLite.table.search('').order([5,'asc']).draw();
    });
  },


  this.dummy = function(){
	// so I can leave the comma at the end of all the other functions when I reorder them.
  }
}

$( document ).ready(function() {
  var PersonPortalLite = new personPortalLite();
  PersonPortalLite.init();
  
});
