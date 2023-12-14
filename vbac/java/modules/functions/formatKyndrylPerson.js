function formatKyndrylPerson(person) {

    if (!person.id) {
        return person.text;
    }

    var $el = $(person.element);
    var data = $el.data();

    var text = "<span style='color:black'>&nbsp;" + data.email + "</span>";
    text += "<br/>&nbsp;&nbsp;<span style='color:silver'>CNUM: " + data.cnum + "<span>";
    text += "<br/>&nbsp;&nbsp;<span style='color:silver'>Worker Id: " + data.workerid + "<span>";
	var textObj = $(text);
	return textObj;
};

export { formatKyndrylPerson as default };