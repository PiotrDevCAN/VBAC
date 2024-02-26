
let Squads = await cacheBustImport('./modules/dataSources/squadsByTribe.js');
let formatSquadName = await cacheBustImport('./modules/functions/formatSquadName.js');
let squadMapper = await cacheBustImport('./modules/select2dataASMapper.js');

function agileSquad(tribeId, squadId) {

    $('#' + tribeId).on('select2:select', function (e) {
        var tribeSelected = $(e.params.data)[0].id;
        Squads.getSquadsByTribe().then((response) => {
            var dataRaw = response[tribeSelected];
            var data = squadMapper(dataRaw);
            if ($('#' + squadId).hasClass("select2-hidden-accessible")) {
                // Select2 has been initialized
                $('#' + squadId).val("").trigger("change");
                $('#' + squadId).empty().select2('destroy').attr('disabled', true);
            }
            $("#" + squadId).select2({
                data: data,
                templateResult: formatSquadName
            }).attr('disabled', false).val('').trigger('change');

            if (data.length == 2) {
                $("#" + squadId).val(data[1].text).trigger('change');
            }
        });
    });
}

export { agileSquad as default };