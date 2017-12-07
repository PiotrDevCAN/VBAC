/**
 * 
 */
function SortableList() {
	
	
	this.init = function(){
		console.log('+++ Function +++ SortableList.init');
		
		console.log('--- Function --- SortableList.init');

	},
	
	
	this.getSelectedChkBoxes = function () {
		// Return array of checkbox values from this form
		  var arrayOfSelectedCheckBoxes = [];
		  var inputFields = document.getElementsByTagName('input');
		  var noOfInputFields = inputFields.length;
		  var stringOfSelectedCheckBoxes = "";

		  //add the value of selected checkboxes to the arrayOfSelectedCheckBoxes
		  for(var i=0; i<noOfInputFields; i++) {
		    if(inputFields[i].type == 'checkbox' && inputFields[i].checked == true) {
			    arrayOfSelectedCheckBoxes.push(inputFields[i].value);
			    //stringOfSelectedCheckBoxes = stringOfSelectedCheckBoxes + inputFields[i].value;
		    }
		  }
		  setCookie("selectedCheckBoxes", arrayOfSelectedCheckBoxes, "30")
		  return arrayOfSelectedCheckBoxes;
		}

	this.applyFilterToTable =  function (){//called when one of the search filter checkboxes is clicked
		var searchString = "";
		var chkBoxArray = getSelectedChkBoxes();
		var arrayLength = chkBoxArray.length;
		for (var i = 0; i < arrayLength; i++) {
		    	var searchString = searchString + chkBoxArray[i] + "|";
		};
		var searchString = "(\W|^)("+searchString+")(\W|$)"; //this formats the search in RegEx format (almost)
		var re = / /gi;
		var searchString = searchString.replace(re, '\\s'); //this fixes the string so that spaces don't break it
		//document.getElementById("searchfilterstring").innerHTML = '<p>' + searchString + '</p>';
		$('#report').DataTable().column( 2 ).search(
				searchString,
		        true,
		        false
		    ).draw(); //output the result to the page
		}
	
	
}

$( document ).ready(function() { 
	var sortableList = new SortableList();
	sortableList.init();
});