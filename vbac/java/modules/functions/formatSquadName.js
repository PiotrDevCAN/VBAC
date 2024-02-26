function formatSquadName(squad) {
	if (squad.id == '' || squad.id == '0') {
		var text = "<span style='color:black'>&nbsp;" + squad.text + "</span>";
	} else {
		var text = "<span style='color:black'><b>&nbsp;" + squad.text + "</b></span>";
		text += "<br/>&nbsp;&nbsp;Organisation: " + squad.organisation;
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Squad Number: " + squad.id + "<span>";
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Squad Leader: " + squad.squadLeader + "<span>";
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Squad Type: " + squad.squadType + "<span>";
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Tribe Number: " + squad.tribeNumber + "<span>";
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Tribe Name: " + squad.tribeName + "<span>";
		text += "<br/>&nbsp;&nbsp;";
		text += "<span style='color:silver'>Shift: " + squad.shift + "<span>";
	}
	var textObj = $(text);
	return textObj;
}

export { formatSquadName as default };