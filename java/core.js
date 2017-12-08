/**
 * This Core class contains javascript that is not specific to any php class ie code that would simply be duplicated in other js class files
 */

var core = {

	init : function() {
	},
	
	
	initialiseDataTables : function(callback) {
		console.log('+++ function core.initialiseDataTable +++');	
		console.log('*** javascript ***');
		
		//initialise all <table> elements on the page
		//requires the Datatables library		
		$('table').dataTable( {
			//basic "Responsive with Excel export" configuration - see the datatables documentation for many other initialisation options 			
	    	stateSave: true,
	    	processing: true,
	        paging: true,
	        responsive: true,    	            	           
	        dom: 'T<"clear">lfrBtip',
	        buttons: [
	                  'excel'
	              ]
	    } );
	    		      
		console.log('--- function core.initialiseDataTable ---');		    	  
	},
	
	initialiseTooltips: function(){		          
            $('[data-toggle="tooltip"]').tooltip();
            console.log("Tooltips initialised");
	},
	
	getCookie : function(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i=0; i<ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0)==' ') c = c.substring(1);
	        if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
	    }
	    return "";
	},
	
	setCookie: function(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + "; " + expires;
	}, 
	
	copyTextToClipboard: function(text) {
		  var textArea = document.createElement("textarea");

		  //
		  // *** This styling is an extra step which is likely not required. ***
		  //
		  // Why is it here? To ensure:
		  // 1. the element is able to have focus and selection.
		  // 2. if element was to flash render it has minimal visual impact.
		  // 3. less flakyness with selection and copying which **might** occur if
		  //    the textarea element is not visible.
		  //
		  // The likelihood is the element won't even render, not even a flash,
		  // so some of these are just precautions. However in IE the element
		  // is visible whilst the popup box asking the user for permission for
		  // the web page to copy to the clipboard.
		  //

		  // Place in top-left corner of screen regardless of scroll position.
		  textArea.style.position = 'fixed';
		  textArea.style.top = 0;
		  textArea.style.left = 0;

		  // Ensure it has a small width and height. Setting to 1px / 1em
		  // doesn't work as this gives a negative w/h on some browsers.
		  textArea.style.width = '2em';
		  textArea.style.height = '2em';

		  // We don't need padding, reducing the size if it does flash render.
		  textArea.style.padding = 0;

		  // Clean up any borders.
		  textArea.style.border = 'none';
		  textArea.style.outline = 'none';
		  textArea.style.boxShadow = 'none';

		  // Avoid flash of white box if rendered for any reason.
		  textArea.style.background = 'transparent';


		  textArea.value = text;

		  document.body.appendChild(textArea);

		  textArea.select();

		  try {
		    var successful = document.execCommand('copy');
		    var msg = successful ? 'successful' : 'unsuccessful';
		    console.log('Copying text command was ' + msg);
		  } catch (err) {
		    console.log('Oops, unable to copy');
		  }

		  document.body.removeChild(textArea);
		},
		
		
		showModal : function(modalName){
			$("#" + modalName).modal("show");
		},

	
		hideModal : function(modalName){
			$("#" + modalName).modal("hide");
			
		}
	
	
}

$(document).ready(function() {
	core.init();
	
});	