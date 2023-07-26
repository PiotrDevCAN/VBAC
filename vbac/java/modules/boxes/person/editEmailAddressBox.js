/*
 *
 *
 *
 */

let validateEmail = await cacheBustImport('./modules/functions/validateEmail.js');
let checkIbmEmailAddress = await cacheBustImport('./modules/functions/checkIbmEmailAddress.js');
let checkOceanEmailAddress = await cacheBustImport('./modules/functions/checkOceanEmailAddress.js');
let checkKyndrylEmailAddress = await cacheBustImport('./modules/functions/checkKyndrylEmailAddress.js');
let inArrayCaseInsensitive = await cacheBustImport('./modules/functions/inArrayCaseInsensitive.js');

let knownCNUMs = await cacheBustImport('./modules/dataSources/knownCNUMs.js');
let knownExternalEmails = await cacheBustImport('./modules/dataSources/knownExternalEmails.js');
let knownIBMEmails = await cacheBustImport('./modules/dataSources/knownIBMEmails.js');
let knownKyndrylEmails = await cacheBustImport('./modules/dataSources/knownKyndrylEmails.js');

let box = await cacheBustImport('./modules/boxes/box.js');

class editEmailAddressBox extends box {

    field;
    status;
    errorCode;

    fieldGeneric = 'EMAIL_ADDRESS';
    fieldKyndryl = 'KYN_EMAIL_ADDRESS';
    fieldIBM = 'IBM_EMAIL_ADDRESS';

    modalTitle = 'Edit Email Address';
    modalTitleKyndryl = 'Edit Kyndryl Email Address';
    modalTitleIBM = 'Edit IBM Email Address';

    statusDefault = undefined;
    statusSuccess = 'success';
    statusError = 'error';

    codeDefault = undefined;
    codeInvalid = 'invalid';
    codeExists = 'exists';
    codeIBM = 'IBM';
    codeOcean = 'Ocean';
    codeKyndryl = 'Kyndryl';

    constructor(parent) {
        console.log('+++ Function +++ editEmailAddressBox.constructor');

        super(parent);
        this.listenForSubmitForm();
        this.listenForEditModallShown();
        this.listenForEditModallHidden();

        this.listenForEditEmailAddress(document.isFm, document.isCdi);
        this.listenForEditKyndrylEmailAddress(document.isFm, document.isCdi);
        this.listenForEditIBMEmailAddress(document.isFm, document.isCdi);

        this.listenForEmailChange();
        this.listenForEmailFocusOut();

        console.log('--- Function --- editEmailAddressBox.constructor');
    }

    setupModal(title, field, cnum, email) {
        $("#editEmailAddressModal .modal-title").text(title);
        $("#eam_field").val(field);
        $("#eam_cnum").val(cnum);
        $("#eam_email").val(email);
    }

    showModal() {
        $("#editEmailAddressModal").modal("show");
    }

    hideModal() {
        $("#editEmailAddressModal").modal("hide");
    }

    greenEmailField() {
        this.status = this.statusSuccess;
        this.errorCode = this.codeDefault;
        $("#eam_email").css("background-color", "LightGreen");
    }

    pinkEmailField() {
        this.status = this.statusError;
        this.errorCode = this.codeExists;
        $("#eam_email").css("background-color", "LightPink");
    }

    errorEmailField() {
        this.status = this.statusError;
        // this.errorCode = this.codeDefault;
        $("#eam_email").css("background-color", "Red");
    }

    clearEmailField() {
        this.status = this.statusDefault;
        this.errorCode = this.codeDefault;
        $("#eam_email").val("");
        $("#eam_email").css("background-color", "");
    }

    // Email Address change
    listenForEmailChange() {
        var $this = this;
        $(document).on("change", "#eam_email", async function () {
            var newEmail = $(this).val();
            var trimmedEmail = newEmail.trim();
            if (trimmedEmail !== "") {

                // validate email address
                if (validateEmail(trimmedEmail)) {

                    let knownExternalEmail = await knownExternalEmails.getEmails();
                    let knownIBMEmail = await knownIBMEmails.getEmails();
                    let knownKyndrylEmail = await knownKyndrylEmails.getEmails();

                    var allreadyExternalExists = inArrayCaseInsensitive(trimmedEmail, knownExternalEmail) >= 0;
                    var allreadyIBMExists = inArrayCaseInsensitive(trimmedEmail, knownIBMEmail) >= 0;
                    var allreadyKyndrylExists = inArrayCaseInsensitive(trimmedEmail, knownKyndrylEmail) >= 0;

                    var ibmEmailAddress = checkIbmEmailAddress(trimmedEmail);
                    var oceanEmailAddress = checkOceanEmailAddress(trimmedEmail);
                    var kyndrylEmailAddress = checkKyndrylEmailAddress(trimmedEmail);

                    switch ($this.field) {
                        case $this.fieldGeneric:
                            // allow IBM and Ocean Email Address only
                            if (allreadyExternalExists || allreadyIBMExists) {
                                // comes back with Position in array(true) or false is it's NOT in the array.
                                $("#updateEmailAddress").attr("disabled", true);
                                $this.pinkEmailField();
                            } else if (kyndrylEmailAddress) {
                                $("#updateEmailAddress").attr("disabled", true);
                                $this.errorEmailField();
                                $this.errorCode = $this.codeKyndryl;
                            } else {
                                $("#updateEmailAddress").attr("disabled", false);
                                $this.greenEmailField();
                            }
                            break;
                        case $this.fieldIBM:
                            // allow IBM Email Address only
                            if (allreadyIBMExists) {
                                // comes back with Position in array(true) or false is it's NOT in the array.
                                $("#updateEmailAddress").attr("disabled", true);
                                $this.pinkEmailField();
                            } else if (oceanEmailAddress) {
                                $("#updateEmailAddress").attr("disabled", true);
                                $this.errorEmailField();
                                $this.errorCode = $this.codeOcean;
                            } else if (kyndrylEmailAddress) {
                                $("#updateEmailAddress").attr("disabled", true);
                                $this.errorEmailField();
                                $this.errorCode = $this.codeKyndryl;
                            } else {
                                $("#updateEmailAddress").attr("disabled", false);
                                $this.greenEmailField();
                            }
                            break;
                        case $this.fieldKyndryl:
                            // allow Kyndryl Email Address only
                            if (allreadyKyndrylExists) {
                                // comes back with Position in array(true) or false is it's NOT in the array.
                                $("#updateEmailAddress").attr("disabled", true);
                                $this.pinkEmailField();
                            } else if (ibmEmailAddress) {
                                $("#updateEmailAddress").attr("disabled", true);
                                $this.errorEmailField();
                                $this.errorCode = $this.codeIBM;
                            } else if (oceanEmailAddress) {
                                $("#updateEmailAddress").attr("disabled", true);
                                $this.errorEmailField();
                                $this.errorCode = $this.codeKyndryl;
                            } else {
                                $("#updateEmailAddress").attr("disabled", false);
                                $this.greenEmailField();
                            }
                            break;
                        default:
                            break;
                    }
                } else {
                    // can not proceed
                    $("#updateEmailAddress").attr("disabled", true);
                    $this.errorEmailField();
                    $this.errorCode = $this.codeInvalid;
                }
            } else {
                // no need to check
                $("#updateEmailAddress").attr("disabled", true);
                $this.clearEmailField();
            }
        });
    }

    listenForEmailFocusOut() {
        var $this = this;
        $(document).on("focusout", "#eam_email", async function () {
            if (typeof $this.status !== 'undefined') {
                switch ($this.status) {
                    case $this.statusSuccess:
                        // no errors;
                        break;
                    case $this.statusError:
                        // check error code
                        switch ($this.errorCode) {
                            case $this.codeExists:
                                // comes back with Position in array(true) or false is it's NOT in the array.
                                $('#messageModalBody').html("<p>Email address already defined to VBAC</p>");
                                $('#messageModal').modal('show');
                                break;
                            case $this.codeIBM:
                                $('#messageModalBody').html("<p>IBM IDs should NOT BE Pre-Boarded. Please board as a regular employee</p>");
                                $('#messageModal').modal('show');
                                break;
                            case $this.codeOcean:
                                $('#messageModalBody').html("<p>Ocean IDs should NOT BE Pre-Boarded. Please board as a regular employee</p>");
                                $('#messageModal').modal('show');
                                break;
                            case $this.codeKyndryl:
                                $('#messageModalBody').html("<p>Kyndryls should NOT BE Pre-Boarded. Please board as a regular employee</p>");
                                $('#messageModal').modal('show');
                                break;
                            case $this.codeInvalid:
                                // can not proceed
                                $('#messageModalBody').html("<p>Provided email address in invalid</p>");
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

    disableListenForEmailChange() {
        $(document).off("change", "#eam_email");
    }

    disableListenForEmailFocusOut() {
        $(document).off("focusout", "#eam_email");
    }

    listenForEditEmailAddress(isFmp, isCdip) {
        var isFm = isFmp;
        var isCdi = isCdip;
        var $this = this;
        $(document).on("click", ".btnEditEmail", function (e) {
            var title = $this.modalTitle;
            var field = $this.fieldGeneric;
            $this.field = field;
            var cnum = $(this).data("cnum");
            var email = $(this).data("email");
            $this.setupModal(title, field, cnum, email);
            $this.showModal();
        });
    }

    listenForEditKyndrylEmailAddress(isFmp, isCdip) {
        var isFm = isFmp;
        var isCdi = isCdip;
        var $this = this;
        $(document).on("click", ".btnEditKyndrylEmail", function (e) {
            var title = $this.modalTitleKyndryl;
            var field = $this.fieldKyndryl;
            $this.field = field;
            var cnum = $(this).data("cnum");
            var email = $(this).data("email");
            $this.setupModal(title, field, cnum, email);
            $this.showModal();
        });
    }

    listenForEditIBMEmailAddress(isFmp, isCdip) {
        var isFm = isFmp;
        var isCdi = isCdip;
        var $this = this;
        $(document).on("click", ".btnEditIBMlEmail", function (e) {
            var title = $this.modalTitleIBM;
            var field = $this.fieldIBM;
            $this.field = field;
            var cnum = $(this).data("cnum");
            var email = $(this).data("email");
            $this.setupModal(title, field, cnum, email);
            $this.showModal();
        });
    }

    listenForSubmitForm() {
        var $this = this;
        $(document).on('submit', '#editEmailAddressForm', function (event) {
            $("#updateEmailAddress").addClass("spinning").attr("disabled", true);
            event.preventDefault();
            var formData = $("#editEmailAddressForm").serialize();
            $.ajax({
                type: "post",
                url: "ajax/saveEmailAddress.php",
                data: formData,
                success: function (response) {
                    var responseObj = JSON.parse(response);
                    $this.hideModal();
                    if (responseObj.success) {
                        $('#messageModalBody').html("<p>Email Address Saved</p>");
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
        $(document).on('shown.bs.modal', '#editEmailAddressModal', function (e) {

        });
    }

    listenForEditModallHidden() {
        var $this = this;
        $(document).on('hidden.bs.modal', '#editEmailAddressModal', function (e) {
            // $this.disableListenForEmailChange();
            // $this.disableListenForEmailFocusOut();
            $this.clearEmailField();
        });
    }
}

export { editEmailAddressBox as default };