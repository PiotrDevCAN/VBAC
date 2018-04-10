
function loadScript(url, callback)
{
    // Adding the script tag to the head as suggested before
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;

    // Then bind the event to the callback function.
    // There are several events for cross browser compatibility.
    script.onreadystatechange = callback;
    script.onload = callback;

    // Fire the loading
    head.appendChild(script);
}


function dynamicLoadCSS(cssFileName){
	console.log('+++ dynamicLoadCSS +++');
	var fileref=document.createElement('link');
	fileref.setAttribute("rel","stylesheet");
	fileref.setAttribute("type","text/css");
	fileref.setAttribute("href", cssFileName);
	console.log(fileref);
	document.getElementsByTagName("head")[0].appendChild(fileref)

	console.log(document.getElementsByTagName("head"))

}

function dynamicLoadJS(JsfileName,callback){
	console.log('+++ dynamicLoadJS +++');
	loadScript(JsfileName,callback);
//	var fileref=document.createElement('script');
//	fileref.setAttribute("type","text/javascript");
//	fileref.setAttribute("src", JsfileName);
//	document.getElementsByTagName("head")[0].appendChild(fileref);
}

function dynamicLoadCssJs(cssFileName,JsFileName,callBack){
	console.log('+++ dynamicLoadCssJS +++');
	dynamicLoadCSS(cssFileName);
	dynamicLoadJS(JsFileName,callBack);
}


function saveAllOptions(selectId, savedSelectArray) {
	var select = document.getElementById(selectId);
	console.log(savedSelectArray);

	for (i = 0; i < select.length; i++) {
		savedSelectArray.push(select.options[i]);
	}

	console.log(savedSelectArray);

}

function restoreSelectFromClone(selectId, saveSelectArray) {
	var select = document.getElementById(selectId);

	select.length = 0;
	for (var i = 0; i < saveSelectArray.length; i++) {
		option = saveSelectArray[i];
		select.add(option);
	}
	console.log('restored the select');
	console.log(select);
}

function getSelectedChkBoxes(inputSelector='input') {
	// Return array of checkbox values from this form
	var arrayOfSelectedCheckBoxes = [];
//	var inputFields = document.getElementsByTagName('input');
//	console.log(inputFields);
	var inputFields = $(inputSelector);
	console.log(inputSelector);
	console.log(inputFields);
	var noOfInputFields = inputFields.length;
	var stringOfSelectedCheckBoxes = "";

	// add the value of selected checkboxes to the arrayOfSelectedCheckBoxes
	for (var i = 0; i < noOfInputFields; i++) {
		if (inputFields[i].type == 'checkbox' && inputFields[i].checked == true) {
			arrayOfSelectedCheckBoxes.push(inputFields[i].value);
		}
	}
	return arrayOfSelectedCheckBoxes;
}

function applyFilterToTable(tableId, columnNumber) {// called when one of the
	// search filter checkboxes
	// is clicked
	tableId = typeof tableId !== 'undefined' ? tableId : '#report';
	columnNumber = typeof columnNumber !== 'undefined' ? columnNumber : '2';
	var searchString = "";
	var chkBoxArray = getSelectedChkBoxes();
	var arrayLength = chkBoxArray.length;
	for (var i = 0; i < arrayLength; i++) {
		var searchString = searchString + chkBoxArray[i] + "|";
	}
	;
	var searchStringRegex = "(" + searchString + "xx)"; // this formats
	// the search in
	// RegEx format
	// (almost)
	var re = / /gi;
	var searchStringRegex = searchStringRegex.replace(re, '\\s'); // this
																	// fixes the
																	// string
	// so that spaces don't
	// break it

	console.log('searching for ' + searchString)
	console.log('searching for ' + searchStringRegex)

	$('#' + tableId).DataTable().column(columnNumber).search(searchStringRegex,
			true, false).draw(); // output the result to the page
}


function setAllInputElementsDisabledState(allInputElements, disabledState) {
	for (i = 0; i < allInputElements.length; i++) {
		var inputElement = allInputElements[i];
		$(inputElement).bootstrapSwitch('disabled', disabledState);
	}

}

function loadXMLString(txt) {
	if (window.DOMParser) {
		parser = new DOMParser();
		xmlDoc = parser.parseFromString(txt, "text/xml");
	} else // Internet Explorer
	{
		xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
		xmlDoc.async = false;
		xmlDoc.loadXML(txt);
	}
	return xmlDoc;
}

function populateMessagePlaceHolder(min, max, progress) {
	var reportDiv = document.getElementById('messagePlaceholder');
	reportDiv.innerHTML = "<div class='progress'>"
			+ "<div class='progress-bar progress-bar-info ' role='progressbar' aria-valuenow='"
			+ progress + "' aria-valuemin='" + min + "' aria-valuemax='" + max
			+ "' style='width: " + parseInt((progress / max) * 100) + "%'>"
			+ "<span class='sr-only'>" + parseInt((progress / max) * 100)
			+ "% Complete (success)</span>" + "</div>" + "</div>";
}

function clearMessagePlaceHolder() {
	var reportDiv = document.getElementById('messagePlaceholder');
	reportDiv.innerHTML = "";
}

//function applyFacesTypaheadToDynamicContent(divID) {
//	var config = {// set up the facesTypeahead
//		// API Key [REQUIRED]
//		key : 'cetarequest;tim.j.minter@uk.ibm.com',
//		sizeToInput : true,
//		resultsAlign : "left",
//		faces : {
//			// The handler for clicking a person in the drop-down.
//			onclick : function(person) {
//				return person.email;
//			}
//		}
//	};
//	FacesTypeAhead.init([ document.getElementById(divID) ], config);
//}


function setCookie(cname, cvalue, exdays) {
	var d = new Date();
	d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
	var expires = "expires=" + d.toUTCString();
	document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname) {
	var name = cname + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ')
			c = c.substring(1);
		if (c.indexOf(name) == 0)
			return c.substring(name.length, c.length);
	}
	return "";
}

function displayTime() {
	var str = "";

	var currentTime = new Date()
	var hours = currentTime.getHours()
	var minutes = currentTime.getMinutes()
	var seconds = currentTime.getSeconds()

	if (minutes < 10) {
		minutes = "0" + minutes
	}
	if (seconds < 10) {
		seconds = "0" + seconds
	}
	str += hours + ":" + minutes + ":" + seconds + " ";
	if (hours > 11) {
		str += "PM"
	} else {
		str += "AM"
	}
	return str;
}

function enableDatepicker() {
	$(document).ready(function() {
		$('.form_date').datepicker({
			format : 'yyyy-mm-dd',
			autoclose : true,
			daysOfWeekDisabled : [ 0, 6 ],
		}).on('changeDate', function(e) {
			showModal('changeDateModal');
//			showModal('changeDateModalHiden');
//			console.log($('changeDateModal'));
//			console.log($('changeDateModalHiden'));
			console.log(e);
		});
	});
}

function showModal(modalName) {
	$("#" + modalName).modal("show");
}

function hideModal(modalName) {
	$("#" + modalName).modal("hide");
}

function disableDatepicker() {
	$('#datetimepicker').datetimepicker('remove');

}

function showElement(elementId) {
	var obj = document.getElementById(elementId);
	obj.style.display = "block";
}

function hideElement(elementId) {
	var obj = document.getElementById(elementId);
	obj.style.display = "none";
}

function setElementReadOnly(elementId) {
	document.getElementById(elementId).attr('readonly', true);
}

function clearElementContents(elementId) {
	console.log('clearElementContents');
	console.log(elementId);
	var element = document.getElementById(elementId);
	console.log(element);

}