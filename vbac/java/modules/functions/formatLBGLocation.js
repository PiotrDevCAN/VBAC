function formatLBGLocation(location) {
	if (location.id == '' || location.id == '0') {
		var text = "<span style='color:black'>&nbsp;" + location.text + "</span>";
	} else {
		var text = "<span style='color:black'><b>&nbsp;" + location.text + "</b></span>";
		text += "<br/>&nbsp;&nbsp;CBC in place: " + location.cbcInPlace;
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Location Country: " + location.country + "<span>";
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Location City: " + location.city + "<span>";
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>On Shore: " + location.onShore + "<span>";
	}
	var textObj = $(text);
	return textObj;
}

export { formatLBGLocation as default };