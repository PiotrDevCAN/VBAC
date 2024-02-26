
let StaticAgileTribes = await cacheBustImport('./modules/dataSources/staticAgileTribes.js');
let formatTribeName = await cacheBustImport('./modules/functions/formatTribeName.js');

function agileTribe(id, originalId) {

    // agile tribe promise
    var selectedTribe = $("#" + originalId).val();
    let agileTribesPromise = StaticAgileTribes.getTribes().then((response) => {
        $("#" + id).select2({
            data: response,
            templateResult: formatTribeName
        })
            .val(selectedTribe)
            .trigger('change');
    });
}

export { agileTribe as default };