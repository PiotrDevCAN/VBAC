
function TraceControlRecord() {
	
	this.typeSelect   = document.getElementById('TRACE_CONTROL_TYPE'),
	this.typeValue    = this.typeSelect.options[this.typeSelect.selectedIndex].value,
	this.classSelect  = document.getElementById('trace_class_name'),
	this.className    = this.classSelect.options[this.classSelect.selectedIndex].value,
	this.methodSelect = document.getElementById('trace_method_name'),
	
	
		

	this.populateMethodSelect = function() {
		this.methodSelect.options.length = 0;
		var option = document.createElement("option");
		option.value = '';
		option.text = 'Method...';
		this.methodSelect.add(option);
		this.methodSelect.disabled = true;
		this.methodSelect.attributes.required = '';
		this.methodSelect.selectedIndex = 0;

		if (this.typeValue.substring(0, 6) == 'method' && this.classSelect.selectedIndex > 0) {

			if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera,
				// Safari
				xmlhttp = new XMLHttpRequest();
			} else {// code for IE6, IE5
				xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			}
			xmlhttp.onreadystatechange = function() {
				if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
					var methodSelect = document.getElementById('trace_method_name'),
					RawXml = xmlhttp.responseText;
					xmlDoc = loadXMLString(RawXml);
					var methods = xmlDoc.getElementsByTagName("METHOD");
					var numberOfMethods = methods.length;
					// populateMessagePlaceHolder(0,numberOfMethods,0);
					for (var i = 0; i < methods.length; i++) {
						// populateMessagePlaceHolder(0,numberOfMethods,i);
						var method = methods[i].childNodes[0];
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
			var url = "ajax/getMethodsForClass.php?className=" + this.className;
			xmlhttp.open("GET", url, true);
			xmlhttp.send();
		} 
	}	
}