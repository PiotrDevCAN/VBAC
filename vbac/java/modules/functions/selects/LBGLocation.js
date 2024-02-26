
let StaticLocations = await cacheBustImport('./modules/dataSources/staticLocationsIds.js');
let formatLocation = await cacheBustImport('./modules/functions/formatLBGLocation.js');

function LBGLocation(id, originalId) {

    // location promise
    var selectedLocation = $("#" + originalId).val();
    let locationsPromise = StaticLocations.getLocations().then((response) => {
        $("#" + id).select2({
            data: response,
            templateResult: formatLocation,
            tags: true,
            createTag: function (params) {
                return undefined;
            }
        })
            .val(selectedLocation)
            .trigger('change');
    });
}

export { LBGLocation as default };