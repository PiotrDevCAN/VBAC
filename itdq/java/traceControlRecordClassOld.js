/**
 * 
 */

var TraceControlRecordClass = {
		
	init : function(){		
		
	},	
	
	populateMethodSelect : function() {
		var typeSelect = document.getElementById('TRACE_CONTROL_TYPE');
		var typeValue = typeSelect.options[typeSelect.selectedIndex].value;
		var classSelect = document.getElementById('trace_class_name');
		var className = classSelect.options[classSelect.selectedIndex].value;
		var methodSelect = document.getElementById('trace_method_name');

		methodSelect.options.length = 0;
		var option = document.createElement("option");
		option.value = '';
		option.text = 'Method...';
		methodSelect.add(option);
		methodSelect.disabled = true;
		methodSelect.attributes.required = '';
		methodSelect.selectedIndex = 0;

		if (typeValue.substring(0, 6) == 'method' && classSelect.selectedIndex > 0) {

			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera,
				// Safari
				xmlhttp = new XMLHttpRequest();
			} else {// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					RawXml = xmlhttp.responseText;
					xmlDoc = loadXMLString(RawXml);
					methods = xmlDoc.getElementsByTagName("METHOD");
					var numberOfMethods = methods.length;
					// populateMessagePlaceHolder(0,numberOfMethods,0);
					for (var i = 0; i < methods.length; i++) {
						// populateMessagePlaceHolder(0,numberOfMethods,i);
						method = methods[i].childNodes[0];
						option = document.createElement("option");
						option.value = method.nodeValue;
						option.text = method.nodeValue;
						methodSelect.add(option);
					}
					methodSelect.disabled = false;
					methodSelect.attributes.required = 'required';
					// clearMessagePlaceHolder();
				}

			}
			var url = "ajax/getMethodsForClass.php?className=" + className;
			xmlhttp.open("GET", url, true);
			xmlhttp.send();
		} else {

		}
	},
	
}

$( document ).ready(function() {
    TraceControlRecordClass.init();
});