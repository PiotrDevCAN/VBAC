/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editSquadAssignment extends box {

    constructor(parent) {
        console.log('+++ Function +++ editSquadAssignment.constructor');

        super(parent);
        this.listenForeditSquadAssignment();
        this.listenForDeleteSquadAssignment();
        this.listenForConfirmSquadAssignmentDelete();

        console.log('--- Function --- editSquadAssignment.constructor');
    }

    listenForeditSquadAssignment() {
        $(document).on("click", ".btnEditAssignment", function () {
            $("#person_name").val($(this).data("fullname"));

            $("#ID").val($(this).data("id"));
            $("#CNUM").val($(this).data("cnum"));
            $("#WORKER_ID").val($(this).data("workerid"));
            $("#EMAIL_ADDRESS").val($(this).data("email"));
            $("#KYN_EMAIL_ADDRESS").val($(this).data("email"));

            $("#originalTRIBE_NUMBER").val($(this).data("tribeid"));
            $("#TRIBE_NUMBER").val($(this).data("tribeid")).trigger('change');
            $("#originalSQUAD_NUMBER").val($(this).data("squadid"));
            $("#SQUAD_NUMBER").val($(this).data("squadid")).trigger('change');
            $("#TYPE").val($(this).data("type")).trigger('change');

            $("#mode").val("edit");
        });
    }

    listenForDeleteSquadAssignment() {
        $(document).on("click", ".btnDeleteAssignment", function (e) {
            var id = $(this).data("id");
            var email = $(this).data("email");
            var cnum = $(this).data("cnum");
            var workerId = $(this).data("workerid");
            var tribeId = $(this).data("tribeid");
            var tribeName = $(this).data("tribename");
            var squadId = $(this).data("squadid");
            var squadName = $(this).data("squadname");
            var message = "<p>Id: <b>" + id + "</b></p>";
            message += "<br>";
            message += "<p>Email Address: <b>" + email + "</b></p>";
            message += "<p>CNUM: <b>" + cnum + "</b></p>";
            message += "<p>Worker ID: <b>" + workerId + "</b></p>";
            message += "<br>";
            message += "<p>Tribe ID: <b>" + tribeId + "</b></p>";
            message += "<p>Tribe Name: <b>" + tribeName + "</b></p>";
            message += "<p>Squad ID: <b>" + squadId + "</b></p>";
            message += "<p>Squad Name: <b>" + squadName + "</b></p>";
            
            message +=
                "<input id='dSsId' name='id' value='" +
                id +
                "' type='hidden' >";

            $("#confirmDeleteSquadAssignmentModal .panel").html(message);
            $("#confirmDeleteSquadAssignment").attr("disabled", false);
            $("#confirmDeleteSquadAssignmentModal").modal("show");
            return false;
        });
    }

    listenForConfirmSquadAssignmentDelete() {
        var $this = this;
        $(document).on('submit', '#confirmDeleteSquadAssignmentForm', function (e) {
            var form = document.getElementById("confirmDeleteSquadAssignmentForm");
            var formValid = form.checkValidity();
            if (formValid) {
                var allDisabledFields = $("input:disabled");
                $(allDisabledFields).attr("disabled", false);
                var formData = $("#confirmDeleteSquadAssignmentForm").serialize();
                $(allDisabledFields).attr("disabled", true);
                $("#confirmDeleteSquadAssignment").attr("disabled", true);
                $.ajax({
                    url: "ajax/deleteSquadAssignment.php",
                    data: formData,
                    type: "POST",
                    success: function (result) {
                        var resultObj = JSON.parse(result);
                        var message = "";
                        var panelclass = "";
                        if (resultObj.success == true) {
                            message += "<div class=panel-heading><h3 class=panel-title>Success</h3>";
                            message += "<br/><h4>Squad Assignment record has been deleted</h4></br>";
                            panelclass = "panel-success";
                        } else {
                            message += "<div class=panel-heading><h3 class=panel-title>Failure</h3>";
                            message += "<br/><h4>Squad Assignment record has been not deleted</h4></br>";
                            panelclass = "panel-danger";
                        }
                        $("#confirmDeleteSquadAssignmentModal .panel").html(message);
                        $("#confirmDeleteSquadAssignmentModal .panel")
                            .removeClass("panel-danger")
                            .removeClass("panel-warning")
                            .removeClass("panel-success");
                        $("#confirmDeleteSquadAssignmentModal .panel").addClass(panelclass);
                        $("#confirmDeleteSquadAssignmentModal").modal("show");
                        $this.tableObj.table.ajax.reload();
                    },
                });
            }
            return false;
        });
    }
}

export { editSquadAssignment as default };