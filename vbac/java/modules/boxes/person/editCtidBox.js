/*
 *
 *
 *
 */

let validateDigits = await cacheBustImport('./modules/functions/validateDigits.js');

let box = await cacheBustImport('./modules/boxes/box.js');

class editCtidBox extends box {

    field;
    status;
    errorCode;

    modalTitle = 'Edit CT ID';

    statusDefault = undefined;
    statusSuccess = 'success';
    statusError = 'error';

    codeDefault = undefined;
    codeInvalid = 'invalid';
    codeNotDigits = 'not digits';

    constructor(parent) {
        console.log('+++ Function +++ editCtidBox.constructor');

        super(parent);
        this.listenForSubmitForm();
        this.listenForEditModallShown();
        this.listenForEditModallHidden();

        this.listenForEditCtId(document.isFm, document.isCdi);

        this.listenForValueChange();
        this.listenForValueFocusOut();

        console.log('--- Function --- editCtidBox.constructor');
    }

    setupModal(title, cnum, workerId, ctId) {
        $("#editCtIdModal .modal-title").text(title);
        $("#ectid_cnum").val(cnum);
        $("#ectid_workerid").val(workerId);
        $("#ectid_ctid").val(ctId);
    }

    showModal() {
        $("#editCtIdModal").modal("show");
    }

    hideModal() {
        $("#editCtIdModal").modal("hide");
    }

    greenField() {
        this.status = this.statusSuccess;
        this.errorCode = this.codeDefault;
        $("#ectid_ctid").css("background-color", "LightGreen");
    }

    errorField() {
        this.status = this.statusError;
        // this.errorCode = this.codeDefault;
        $("#ectid_ctid").css("background-color", "Red");
    }

    clearField() {
        this.status = this.statusDefault;
        this.errorCode = this.codeDefault;
        $("#ectid_ctid").val("");
        $("#ectid_ctid").css("background-color", "");
    }

    // CT ID change
    listenForValueChange() {
        var $this = this;
        $(document).on("change", "#ectid_ctid", function () {
            var newValue = $(this).val();
            var trimmedValue = newValue.trim();
            if (trimmedValue !== "") {
                // validate length
                if (trimmedValue.length == 7) {
                    // validate digits only
                    if (validateDigits(trimmedValue)) {
                        // can proceed
                        $("#updateCtId").attr("disabled", false);
                        $this.greenField();
                    } else {
                        // can not proceed
                        $("#updateCtId").attr("disabled", true);
                        $this.errorField();
                        $this.errorCode = $this.codeNotDigits;
                    }
                } else {
                    // can not proceed
                    $("#updateCtId").attr("disabled", true);
                    $this.errorField();
                    $this.errorCode = $this.codeInvalid;
                }
            } else {
                // no need to check
                $("#updateCtId").attr("disabled", true);
                $this.clearField();
            }
        });
    }

    listenForValueFocusOut() {
        var $this = this;
        $(document).on("focusout", "#ectid_ctid", async function () {
            if (typeof $this.status !== 'undefined') {
                switch ($this.status) {
                    case $this.statusSuccess:
                        // no errors;
                        break;
                    case $this.statusError:
                        // check error code
                        switch ($this.errorCode) {
                            case $this.codeNotDigits:
                                $('#messageModalBody').html("<p>Provided CT ID does not contain digits only</p>");
                                $('#messageModal').modal('show');
                                break;
                            case $this.codeInvalid:
                                // can not proceed
                                $('#messageModalBody').html("<p>Provided CT ID is invalid</p>");
                                $('#messageModal').modal('show');
                                break;
                            default:
                                break;
                        }
                        break;
                    default:
                        break;
                }
            }
        });
    }

    disableListenForValueChange() {
        $(document).off("change", "#ectid_ctid");
    }

    disableListenForValueFocusOut() {
        $(document).off("focusout", "#ectid_ctid");
    }

    listenForEditCtId(isFmp, isCdip) {
        var isFm = isFmp;
        var isCdi = isCdip;
        var $this = this;
        $(document).on("click", ".btnEditCtid", function (e) {
            var title = $this.modalTitle;
            var cnum = $(this).data("cnum");
            var workerId = $(this).data("workerid");
            var ctId = $(this).data("ctid");
            $this.setupModal(title, cnum, workerId, ctId);
            $this.showModal();
        });
    }

    listenForSubmitForm() {
        var $this = this;
        $(document).on('submit', '#editCtIdForm', function (event) {
            $("#updateCtId").addClass("spinning").attr("disabled", true);
            event.preventDefault();
            var formData = $("#editCtIdForm").serialize();
            $.ajax({
                type: "post",
                url: "ajax/saveCtid.php",
                data: formData,
                success: function (response) {
                    var responseObj = JSON.parse(response);
                    $this.hideModal();
                    if (responseObj.success) {
                        $('#messageModalBody').html("<p>CT ID Saved</p>");
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
                    $this.tableObj.table.ajax.reload();
                }
            });
        });
    }

    listenForEditModallShown() {
        var $this = this;
        $(document).on('shown.bs.modal', '#editCtIdModal', function (e) {

        });
    }

    listenForEditModallHidden() {
        var $this = this;
        $(document).on('hidden.bs.modal', '#editCtIdModal', function (e) {
            // $this.disableListenForValueChange();
            // $this.disableListenForValueFocusOut();
            $this.clearField();
        });
    }
}

export { editCtidBox as default };