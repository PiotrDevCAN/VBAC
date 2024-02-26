/**
 *
 */

function map(data) {
	const mappedData = $.map(data, function (val, index) {
		return {
			id: val.SQUAD_NUMBER,
			text: val.SQUAD_NAME,
			organisation: val.ORGANISATION,
			shift: val.SHIFT,
			squadLeader: val.SQUAD_LEADER,
			squadName: val.SQUAD_NAME,
			squadNumber: val.SQUAD_NUMBER,
			squadType: val.SQUAD_TYPE,
			tribeName: val.TRIBE_NAME,
			tribeNumber: val.TRIBE_NUMBER
		};
	});
	return mappedData;
}

export { map as default };