/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class setFmFlagBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ setFmFlagBox.constructor');

        super(parent);
        this.listenForToggleFmFlag();
        this.listenForConfirmFmFlag();

        console.log('--- Function --- setFmFlagBox.constructor');
    }

    listenForToggleFmFlag() {
        $(document).on("click", ".btnSetFmFlag", function (e) {
            var cnum = $(this).data("cnum");
            var workerId = $(this).data("workerid");
            var notesid = $(this).data("notesid");
            var flag = $(this).data("fmflag");
            var message = "<p>For: <b>" + notesid + "</b></p>";
            message += "<p>To: <b>" + flag + "</b></p>";
            message +=
                "<input id='cFmCnum' name='cnum' value='" +
                cnum +
                "' type='hidden' >";
            message +=
                "<input id='cFmWorkerId' name='workerid' value='" +
                workerId +
                "' type='hidden' >";
            message +=
                "<input id='cFmNotesid' name='notesid' value='" +
                notesid +
                "'  type='hidden' >";
            message +=
                "<input id='cFmFlag' name='flag' value='" +
                flag +
                "'  type='hidden' >";

            $("#confirmChangeFmFlagModal .modal-body").html(message);
            $("#confirmFmStatusChange").attr("disabled", false);
            $("#confirmChangeFmFlagModal").modal("show");
            return false;
        });
    }

    listenForConfirmFmFlag() {
        var $this = this;
        $(document).on('submit', '#confirmFmFlagChangeForm', function (e) {
            var form = document.getElementById("confirmFmFlagChangeForm");
            var formValid = form.checkValidity();
            if (formValid) {
                var allDisabledFields = $("input:disabled");
                $(allDisabledFields).attr("disabled", false);
                var formData = $("#confirmFmFlagChangeForm").serialize();
                $(allDisabledFields).attr("disabled", true);
                $("#confirmFmStatusChange").attr("disabled", true);
                $.ajax({
                    url: "ajax/changeFmFlag.php",
                    data: formData,
                    type: "POST",
                    success: function (result) {
                        $this.tableObj.table.ajax.reload();
                        var resultObj = JSON.parse(result);
                        if (!resultObj.messages) {
                            $("#confirmChangeFmFlagModal .modal-body").html(resultObj.body);
                        } else {
                            $("#confirmChangeFmFlagModal .modal-body").html(
                                resultObj.messages
                            );
                        }
                    },
                });
            }
            return false;
        });
    }
}

export { setFmFlagBox as default };