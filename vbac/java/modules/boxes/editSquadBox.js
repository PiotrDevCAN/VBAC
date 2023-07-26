/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editSquadBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editSquadBox.constructor');

        super(parent);
        this.listenForEditSquad();
        this.listenForSubmitSquadForm();
        this.listenForLeader();

        console.log('--- Function --- editSquadBox.constructor');
    }

    listenForEditSquad() {
        var $this = this;
        $(document).on("click", ".btnEditSquad", function () {
            $("#SQUAD_NUMBER")
                .val($(this).data("squadnumber"))
                .trigger("change")
                .attr("disabled", true);
            $("#SQUAD_TYPE").val($(this).data("squadtype"));
            $("#TRIBE_NUMBER").val("").trigger("change");
            if ($(this).data("organisation") == "Managed Services") {
                $("#radioTribeOrganisationManaged").prop("checked", true);
            } else {
                $("#radioTribeOrganisationProject").prop("checked", true);
            }
            $this.parent.initialiseTribeNumber($(this).data("tribenumber"));
            $("#SHIFT").val($(this).data("shift")).trigger("change");
            $("#SQUAD_LEADER").val($(this).data("squadleader"));
            $("#SQUAD_NAME").val($(this).data("squadname"));
            $("#mode").val("edit");
        });
    }

    listenForSubmitSquadForm() {
        var $this = this;
        $(document).on('submit', '#squadForm', function (event) {
            $(":submit").addClass("spinning").attr("disabled", true);
            console.log("submit clicked");
            event.preventDefault();
            var disabledFields = $(":disabled");
            $(disabledFields).attr("disabled", false);
            var formData = $("#squadForm").serialize();
            var verData = $("#version").prop("checked") ? "&version=Original" : "&version=New";
            $(disabledFields).attr("disabled", true);
            $.ajax({
                type: "post",
                url: "ajax/saveAgileSquadRecord.php",
                data: formData + verData,
                success: function (response) {
                    var responseObj = JSON.parse(response);
                    console.log(responseObj);
                    if (responseObj.success) {
                        $('#messageModalBody').html("<p>Squad Record Saved</p>");
                        $('#messageModal').modal('show');
                    } else {
                        $('#messageModalBody').html(
                            "<p>Save has encountered a problem</p><p>" +
                            responseObj.messages +
                            "</p>"
                        );
                        $('#messageModal').modal('show');
                    }
                    $(".spinning").removeClass("spinning").attr("disabled", false);
                    $("#SQUAD_NUMBER").val("").trigger("change").attr("disabled", false);
                    $("#SQUAD_TYPE").val("");
                    $("#SQUAD_NAME").val("");
                    $("#TRIBE_NUMBER").val("").trigger("change").attr("disabled", false);
                    $("#SHIFT").val("").trigger("change").attr("disabled", false);
                    $("#SQUAD_LEADER").val("");
                    $this.tableObj.table.ajax.reload();
                }
            });
        });
    }

    listenForLeader() {
        $(".typeahead").bind("typeahead:select", function (ev, suggestion) {
            $(".tt-menu").hide();
            $("#TRIBE_LEADER").val(suggestion.notesEmail);
        });
    }
}

export { editSquadBox as default };