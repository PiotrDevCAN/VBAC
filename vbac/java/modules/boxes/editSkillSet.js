/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editSkillSet extends box {

    constructor(parent) {
        console.log('+++ Function +++ editSkillSet.constructor');

        super(parent);
        this.listenForEditSkillSet();
        this.listenForDeleteSkillSet();
        this.listenForConfirmSkillsetDelete();

        console.log('--- Function --- editSkillSet.constructor');
    }

    listenForEditSkillSet() {
        $(document).on("click", ".btnEditSkillset", function () {
            $("#SKILLSET").val($(this).data("skillset"));
            $("#SKILLSET_ID").val($(this).data("id"));

            $("#mode").val("edit");
        });
    }

    listenForDeleteSkillSet() {
        $(document).on("click", ".btnDeleteSkillset", function (e) {
            var id = $(this).data("id");
            var skillset = $(this).data("skillset");
            var message = "<p>Id: <b>" + id + "</b></p>";
            message += "<p>Skillset: <b>" + skillset + "</b></p>";
            message +=
                "<input id='dSsId' name='id' value='" +
                id +
                "' type='hidden' >";
            message +=
                "<input id='dSsSkillset' name='skillset' value='" +
                skillset +
                "'  type='hidden' >";

            $("#confirmDeleteSkillsetModal .panel").html(message);
            $("#confirmDeleteSkillset").attr("disabled", false);
            $("#confirmDeleteSkillsetModal").modal("show");
            return false;
        });
    }

    listenForConfirmSkillsetDelete() {
        var $this = this;
        $(document).on('submit', '#confirmDeleteSkillsetForm', function (e) {
            var form = document.getElementById("confirmDeleteSkillsetForm");
            var formValid = form.checkValidity();
            if (formValid) {
                var allDisabledFields = $("input:disabled");
                $(allDisabledFields).attr("disabled", false);
                var formData = $("#confirmDeleteSkillsetForm").serialize();
                $(allDisabledFields).attr("disabled", true);
                $("#confirmDeleteSkillset").attr("disabled", true);
                $.ajax({
                    url: "ajax/deleteSkillSet.php",
                    data: formData,
                    type: "POST",
                    success: function (result) {
                        var resultObj = JSON.parse(result);
                        var message = "";
                        var panelclass = "";
                        if (resultObj.success == true) {
                            message += "<div class=panel-heading><h3 class=panel-title>Success</h3>";
                            message += "<br/><h4>Skillset record has been deleted</h4></br>";
                            panelclass = "panel-success";
                        } else {
                            message += "<div class=panel-heading><h3 class=panel-title>Failure</h3>";
                            message += "<br/><h4>Skillset record has been not deleted</h4></br>";
                            panelclass = "panel-danger";
                        }
                        $("#confirmDeleteSkillsetModal .panel").html(message);
                        $("#confirmDeleteSkillsetModal .panel")
                            .removeClass("panel-danger")
                            .removeClass("panel-warning")
                            .removeClass("panel-success");
                        $("#confirmDeleteSkillsetModal .panel").addClass(panelclass);
                        $("#confirmDeleteSkillsetModal").modal("show");
                        $this.tableObj.table.ajax.reload();
                    },
                });
            }
            return false;
        });
    }
}

export { editSkillSet as default };