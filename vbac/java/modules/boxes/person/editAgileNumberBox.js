/*
 *
 *
 *
 */

let spinner = await cacheBustImport('./modules/functions/spinner.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class editAgileNumberBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editAgileNumberBox.constructor');

        super(parent);
        this.listenForEditAgileSquadModalShown();
        this.listenForEditAgileSquadModalHidden();
        this.listenForEditAgileNumber();
        this.listenForSaveAgileNumber();

        console.log('--- Function --- editAgileNumberBox.constructor');
    }

    listenForSelectAgileNumber() {
        $(document).on("select2:select", "#agileSquad", function (e) {
            $("#agileTribeNumber").val("");
            $("#agileTribeName").val("");
            $("#agileTribeLeader").val("");
            $("#agileSquadType").val("");
            $("#agilesquadName").val("");
            $("#agilesquadLeader").val("");
            $("#updateSquad").attr("disabled", true);
            var data = e.params.data;
            var squadNumber = e.params.data.id;
            $.ajax({
                url: "ajax/getSquadDetails.php",
                data: { squadNumber: squadNumber },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    if (resultObj.success) {
                        $("#agileTribeNumber").val(resultObj.squadDetails.TRIBE_NUMBER);
                        $("#agileTribeName").val(resultObj.squadDetails.TRIBE_NAME);
                        $("#agileTribeLeader").val(resultObj.squadDetails.TRIBE_LEADER);
                        $("#agileSquadType").val(resultObj.squadDetails.SQUAD_TYPE);
                        $("#agilesquadName").val(resultObj.squadDetails.SQUAD_NAME);
                        $("#agilesquadLeader").val(resultObj.squadDetails.SQUAD_LEADER);
                        $("#updateSquad").attr("disabled", false);
                    } else {
                        alert(resultObj.messages);
                    }
                },
            });
        });
    }

    listenForEditAgileSquadModalShown() {
        var $this = this;
        $(document).on('shown.bs.modal', '#editAgileSquadModal', function (e) {
            $this.listenForSelectAgileNumber();
        });
    }

    listenForEditAgileSquadModalHidden() {
        $(document).on('hidden.bs.modal', '#editAgileSquadModal', function (e) {

        });
    }

    listenForEditAgileNumber() {
        $(document).on("click", ".btnEditAgileNumber", function (e) {
            $(this).addClass("spinning").attr("disabled", true);
            $("#updateSquad").attr("disabled", true);
            var cnum = $(this).data("cnum");
            var version = $(this).data("version");
            $("#editAgileSquadModal .modal-body").html(spinner);
            $("#editAgileSquadModal").modal("show");
            $.ajax({
                url: "ajax/getEditAgileNumberModalBody.php",
                data: { cnum: cnum, version: version },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    if (resultObj.success) {
                        $(".spinning").removeClass("spinning").attr("disabled", false);
                        $("#editAgileSquadModal .modal-body").replaceWith(
                            $(resultObj.body).find(".modal-body")
                        );
                        $.fn.modal.Constructor.prototype.enforceFocus = function () { };
                        $("#agileSquad").select2({
                            width: "100%",
                            placeholder: "Select Squad",
                            allowClear: true,
                        });
                    } else {
                        $("#editAgileSquadModal .modal-body").html(resultObj.messages);
                    }
                },
            });
        });
    }

    listenForSaveAgileNumber() {
        var $this = this;
        $(document).on("click", "#updateSquad", function (e) {
            $(this).addClass("spinning").attr("disabled", true);
            e.preventDefault();
            var form = document.getElementById("editAgileSquadForm");
            var formValid = form.checkValidity();
            if (formValid) {
                var allDisabledFields = $("input:disabled");
                $(allDisabledFields).attr("disabled", false);
                var formData = $("#editAgileSquadForm").serialize();
                $(allDisabledFields).attr("disabled", true);
                $.ajax({
                    url: "ajax/updateAgileSquadNumber.php",
                    data: formData,
                    type: "POST",
                    success: function (result) {
                        var resultObj = JSON.parse(result);
                        if (resultObj.success) {
                            $(".spinning").removeClass(".spinning").attr("disabled", false);
                            $("#editAgileSquadModal").modal("hide");
                            $this.tableObj.table.ajax.reload();
                        } else {
                            $("#editAgileSquadModal .modal-body").html(resultObj.messages);
                        }
                    },
                });
            }
        });
    }
}

export { editAgileNumberBox as default };