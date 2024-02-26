
let StaticAgileSquads = await cacheBustImport('./modules/dataSources/staticAgileSquads.js');
let formatSquadName = await cacheBustImport('./modules/functions/formatSquadName.js');

function agileSquad(id, originalId) {

    // agile squad promise
    var selectedSquad = $("#" + originalId).val();
    let agileSquadsPromise = StaticAgileSquads.getSquads().then((response) => {
        $("#" + id).select2({
            data: response,
            templateResult: formatSquadName
        })
            .val(selectedSquad)
            .trigger('change');
    });
}

export { agileSquad as default };