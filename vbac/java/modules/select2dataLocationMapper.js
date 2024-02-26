/**
 *
 */

function map(data) {
	const mappedData = $.map(data, function (val, index) {
		return {
			// id: val.ID,
			id: val.ADDRESS + ',' + val.CITY + ',' + val.COUNTRY,
			text: val.ADDRESS,
			address: val.ADDRESS,
			cbcInPlace: val.CBC_IN_PLACE,
			city: val.CITY,
			cityId: val.CITY_ID,
			country: val.COUNTRY,
			countryId: val.COUNTRY_ID,
			onShore: val.ONSHORE
		};
	});
	return mappedData;
}

export { map as default };