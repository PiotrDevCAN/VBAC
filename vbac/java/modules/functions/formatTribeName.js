function formatTribeName(tribe) {
	if (tribe.id == '') {
		var text = "<span style='color:black'>&nbsp;" + tribe.text + "</span>";
	} else {
		var text = "<span style='color:black'><b>&nbsp;" + tribe.text + "</b></span>";
		text += "<br/>&nbsp;&nbsp;Organisation: " + tribe.organisation;
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Tribe Number: " + tribe.id + "<span>";
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Tribe Leader: " + tribe.leader + "<span>";
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Iteration mgr: " + tribe.iterationMgr + "<span>";
	}
	var textObj = $(text);
	return textObj;
}

export { formatTribeName as default };