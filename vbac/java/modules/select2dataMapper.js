/**
 *
 */

function map(data) {
	const mappedData = $.map(data, function (val, index) {
		return {
			id: val,
			text: val
		};
	});
	return mappedData;
}

export { map as default };