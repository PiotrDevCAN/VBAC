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
        this.listenForDeleteSquad();
        this.listenForConfirmSquadDelete();
        this.listenForSubmitSquadForm();

        console.log('--- Function --- editSquadBox.constructor');
    }

    listenForEditSquad() {
        var $this = this;
        $(document).on("click", ".btnEditSquad", function () {
            $("#SQUAD_NUMBER")
                .val($(this).data("squadnumber"))
                .trigger("change")
                .attr("disabled", true);
            if ($(this).data("organisation") == "Managed Services") {
                $("#radioTribeOrganisationManaged").prop("checked", true);
            } else {
                $("#radioTribeOrganisationProject").prop("checked", true);
            }
            $("#SQUAD_TYPE").val($(this).data("squadtype"));
            $("#SQUAD_NAME").val($(this).data("squadname"));
            $("#TRIBE_NUMBER").val("").trigger("change");
            $this.parent.initialiseTribeNumber($(this).data("tribenumber"));
            $("#SHIFT").val($(this).data("shift")).trigger("change");
            $("#SQUAD_LEADER").typeahead('val', $(this).data("squadleader"));
            $("#mode").val("edit");
        });
    }

    listenForDeleteSquad() {
        $(document).on("click", ".btnDeleteSquad", function (e) {
            var id = $(this).data("squadnumber");
            var squad = $(this).data("squadname");
            var message = "<p>Id: <b>" + id + "</b></p>";
            message += "<p>Squad: <b>" + squad + "</b></p>";
            message +=
                "<input id='dSsId' name='id' value='" +
                id +
                "' type='hidden' >";
            message +=
                "<input id='dSsSquad' name='squad' value='" +
                squad +
                "'  type='hidden' >";

            $("#confirmDeleteSquadModal .panel").html(message);
            $("#confirmDeleteSquad").attr("disabled", false);
            $("#confirmDeleteSquadModal").modal("show");
            return false;
        });
    }

    listenForConfirmSquadDelete() {
        var $this = this;
        $(document).on('submit', '#confirmDeleteSquadForm', function (e) {
            var form = document.getElementById("confirmDeleteSquadForm");
            var formValid = form.checkValidity();
            if (formValid) {
                var allDisabledFields = $("input:disabled");
                $(allDisabledFields).attr("disabled", false);
                var formData = $("#confirmDeleteSquadForm").serialize();
                $(allDisabledFields).attr("disabled", true);
                $("#confirmDeleteSquad").attr("disabled", true);
                $.ajax({
                    url: "ajax/deleteSquad.php",
                    data: formData,
                    type: "POST",
                    success: function (result) {
                        var resultObj = JSON.parse(result);
                        var message = "";
                        var panelclass = "";
                        if (resultObj.success == true) {
                            message += "<div class=panel-heading><h3 class=panel-title>Success</h3>";
                            message += "<br/><h4>Squad record has been deleted</h4></br>";
                            panelclass = "panel-success";
                        } else {
                            message += "<div class=panel-heading><h3 class=panel-title>Failure</h3>";
                            message += "<br/><h4>Squad record has been not deleted</h4></br>";
                            panelclass = "panel-danger";
                        }
                        $("#confirmDeleteSquadModal .panel").html(message);
                        $("#confirmDeleteSquadModal .panel")
                            .removeClass("panel-danger")
                            .removeClass("panel-warning")
                            .removeClass("panel-success");
                        $("#confirmDeleteSquadModal .panel").addClass(panelclass);
                        $("#confirmDeleteSquadModal").modal("show");
                        $this.tableObj.table.ajax.reload();
                    },
                });
            }
            return false;
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
                    $("#SQUAD_LEADER").typeahead('val', '');
                    $this.tableObj.table.ajax.reload();
                }
            });
        });
    }
}

export { editSquadBox as default };