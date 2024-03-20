/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editTribeBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editTribeBox.constructor');

        super(parent);
        this.listenForEditTribe();
        this.listenForDeleteTribe();
        this.listenForConfirmTribeDelete();
        this.listenForSubmitTribeForm();

        console.log('--- Function --- editTribeBox.constructor');
    }

    listenForEditTribe() {
        var $this = this;
        $(document).on("click", ".btnEditTribe", function () {
            $("#TRIBE_NUMBER")
                .val($(this).data("tribenumber"))
                .trigger("change")
                .attr("disabled", true);
            if ($(this).data("organisation") == "Managed Services") {
                $("#radioTribeOrganisationManaged").prop("checked", true);
            } else {
                $("#radioTribeOrganisationProject").prop("checked", true);
            }
            $("#TRIBE_NAME").val($(this).data("tribename"));
            $("#TRIBE_LEADER").typeahead('val', $(this).data("tribeleader"));
            $("#ITERATION_MGR").typeahead('val', $(this).data("iterationmgr"));
            $("#mode").val("edit");
        });
    }

    listenForDeleteTribe() {
        $(document).on("click", ".btnDeleteTribe", function (e) {
            var id = $(this).data("tribenumber");
            var tribe = $(this).data("tribename");
            var message = "<p>Id: <b>" + id + "</b></p>";
            message += "<p>Tribe: <b>" + tribe + "</b></p>";
            message +=
                "<input id='dSsId' name='id' value='" +
                id +
                "' type='hidden' >";
            message +=
                "<input id='dSsTribe' name='tribe' value='" +
                tribe +
                "'  type='hidden' >";

            $("#confirmDeleteTribeModal .panel").html(message);
            $("#confirmDeleteTribe").attr("disabled", false);
            $("#confirmDeleteTribeModal").modal("show");
            return false;
        });
    }

    listenForConfirmTribeDelete() {
        var $this = this;
        $(document).on('submit', '#confirmDeleteTribeForm', function (e) {
            var form = document.getElementById("confirmDeleteTribeForm");
            var formValid = form.checkValidity();
            if (formValid) {
                var allDisabledFields = $("input:disabled");
                $(allDisabledFields).attr("disabled", false);
                var formData = $("#confirmDeleteTribeForm").serialize();
                $(allDisabledFields).attr("disabled", true);
                $("#confirmDeleteTribe").attr("disabled", true);
                $.ajax({
                    url: "ajax/deleteTribe.php",
                    data: formData,
                    type: "POST",
                    success: function (result) {
                        var resultObj = JSON.parse(result);
                        var message = "";
                        var panelclass = "";
                        if (resultObj.success == true) {
                            message += "<div class=panel-heading><h3 class=panel-title>Success</h3>";
                            message += "<br/><h4>Tribe record has been deleted</h4></br>";
                            panelclass = "panel-success";
                        } else {
                            message += "<div class=panel-heading><h3 class=panel-title>Failure</h3>";
                            message += "<br/><h4>Tribe record has been not deleted</h4></br>";
                            panelclass = "panel-danger";
                        }
                        $("#confirmDeleteTribeModal .panel").html(message);
                        $("#confirmDeleteTribeModal .panel")
                            .removeClass("panel-danger")
                            .removeClass("panel-warning")
                            .removeClass("panel-success");
                        $("#confirmDeleteTribeModal .panel").addClass(panelclass);
                        $("#confirmDeleteTribeModal").modal("show");
                        $this.tableObj.table.ajax.reload();
                    },
                });
            }
            return false;
        });
    }

    listenForSubmitTribeForm() {
        var $this = this;
        $(document).on('submit', '#tribeForm', function (event) {
            console.log("submit clicked");
            event.preventDefault();
            $(":submit").addClass("spinning").attr("disabled", true);
            var disabledFields = $(":disabled");
            $(disabledFields).attr("disabled", false);
            var formData = $("#tribeForm").serialize();
            var verData = $("#version").prop("checked")
                ? "&version=Original"
                : "&version=New";

            console.log(formData);
            console.log(verData);

            $(disabledFields).attr("disabled", true);
            $.ajax({
                type: "post",
                url: "ajax/saveAgileTribeRecord.php",
                data: formData + verData,
                success: function (response) {
                    var responseObj = JSON.parse(response);
                    console.log(responseObj);
                    if (responseObj.success) {
                        $('#messageModalBody').html("<p>Tribe Record Saved</p>");
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
                    $("#TRIBE_NUMBER")
                        .val("")
                        .trigger("change")
                        .attr("disabled", false);
                    $("#TRIBE_NAME").val("");
                    $("#TRIBE_LEADER").typeahead('val', '');
                    $("#ITERATION_MGR").typeahead('val', '');
                    $("#radioTribeOrganisationManaged").prop("checked", true);
                    $this.tableObj.table.ajax.reload();
                }
            });
        });
    }
}

export { editTribeBox as default };