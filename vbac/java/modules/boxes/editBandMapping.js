/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editBandMapping extends box {

    constructor(parent) {
        console.log('+++ Function +++ editBandMapping.constructor');

        super(parent);
        this.listenForEditBandMapping();
        this.listenForDeleteBandMapping();
        this.listenForConfirmBandMappingDelete();

        console.log('--- Function --- editBandMapping.constructor');
    }

    listenForEditBandMapping() {
        $(document).on("click", ".btnEditBandMapping", function () {
            $("#BUSINESS_TITLE").val($(this).data("id"));
            $("#BAND").val($(this).data("band"));

            $("#mode").val("edit");
        });
    }

    listenForDeleteBandMapping() {
        $(document).on("click", ".btnDeleteBandMapping", function (e) {
            var id = $(this).data("id");
            var band = $(this).data("band");
            var message = "<p>Business Title: <b>" + id + "</b></p>";
            message += "<p>Band: <b>" + band + "</b></p>";
            message +=
                "<input id='dSsId' name='id' value='" +
                id +
                "' type='hidden' >";
            message +=
                "<input id='dSsBand' name='band' value='" +
                band +
                "'  type='hidden' >";

            $("#confirmDeleteBandMappingModal .panel").html(message);
            $("#confirmDeleteBandMapping").attr("disabled", false);
            $("#confirmDeleteBandMappingModal").modal("show");
            return false;
        });
    }

    listenForConfirmBandMappingDelete() {
        var $this = this;
        $(document).on('submit', '#confirmDeleteBandMappingForm', function (e) {
            var form = document.getElementById("confirmDeleteBandMappingForm");
            var formValid = form.checkValidity();
            if (formValid) {
                var allDisabledFields = $("input:disabled");
                $(allDisabledFields).attr("disabled", false);
                var formData = $("#confirmDeleteBandMappingForm").serialize();
                $(allDisabledFields).attr("disabled", true);
                $("#confirmDeleteBandMapping").attr("disabled", true);
                $.ajax({
                    url: "ajax/deleteBandMapping.php",
                    data: formData,
                    type: "POST",
                    success: function (result) {
                        var resultObj = JSON.parse(result);
                        var message = "";
                        var panelclass = "";
                        if (resultObj.success == true) {
                            message += "<div class=panel-heading><h3 class=panel-title>Success</h3>";
                            message += "<br/><h4>Band Mapping record has been deleted</h4></br>";
                            panelclass = "panel-success";
                        } else {
                            message += "<div class=panel-heading><h3 class=panel-title>Failure</h3>";
                            message += "<br/><h4>Band Mapping record has been not deleted</h4></br>";
                            panelclass = "panel-danger";
                        }
                        $("#confirmDeleteBandMappingModal .panel").html(message);
                        $("#confirmDeleteBandMappingModal .panel")
                            .removeClass("panel-danger")
                            .removeClass("panel-warning")
                            .removeClass("panel-success");
                        $("#confirmDeleteBandMappingModal .panel").addClass(panelclass);
                        $("#confirmDeleteBandMappingModal").modal("show");
                        $this.tableObj.table.ajax.reload();
                    },
                });
            }
            return false;
        });
    }
}

export { editBandMapping as default };