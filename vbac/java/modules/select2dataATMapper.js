/**
 *
 */

function map(data) {
	const mappedData = $.map(data, function (val, index) {
		return {
			id: val.TRIBE_NUMBER,
			text: val.TRIBE_NAME,
			tribeLeader: val.TRIBE_LEADER,
			tribeName: val.TRIBE_NAME,
			tribeNumber: val.TRIBE_NUMBER,
			organisation: val.ORGANISATION,
			iterationMgr: val.ITERATION_MGR
		};
	});
	return mappedData;
}

export { map as default };