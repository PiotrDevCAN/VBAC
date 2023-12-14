/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');
let spinner = await cacheBustImport('./modules/functions/spinner.js');
let pesDescriptionHover = await cacheBustImport('./modules/functions/pesDescriptionHover.js');

class editPersonBox extends box {

    child;

    formId;
    updateButton;
    responseObj;

    constructor(parent, child) {
        console.log('+++ Function +++ editPersonBox.constructor');

        super(parent);

        this.child = child;

        this.listenForEditPerson(document.isFm, document.isCdi);

        this.listenForChangeFm();
        this.listenForConfirmChangeFm();
        this.listenForResetChangeFm();

        this.listenforUpdateBoarding();

        console.log('--- Function --- editPersonBox.constructor');
    }

    listenForEditPerson(isFmp, isCdip) {
        var isFm = isFmp;
        var isCdi = isCdip;
        var $this = this;
        $(document).on("click", "." + $this.child.editButtonClass, function (e) {
            var cnum = $(this).data("cnum");
            var workerId = $(this).data("workerid");
            $("#editPersonModal .modal-body").html(spinner);
            $("#editPersonModal").modal("show");
            $.ajax({
                url: "ajax/getEditPersonModalBody.php",
                data: {
                    cnum: cnum,
                    workerid: workerId
                },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    if (!resultObj.messages) {
                        $("#editPersonModal .modal-body").html(resultObj.body);

                        FormMessageArea.showMessageArea();

                        $this.child.initialiseStartEndDate();
                        $this.child.initialiseOtherDates();
                        $this.child.initialiseFormSelect2();

                        if (isFm == "yes") {
                            // Dont let FM Edit the Func Mgr Field, Ant Stark November 12th 2020
                            // Let the FM Edit the FUnc Mgr Field, Ant Stark January 26th 2021
                            // $('#FM_CNUM').attr('disabled',true);
                        }

                        if (isCdi == "yes") {
                            $("#person_intranet").attr("disabled", false);
                        }

                        pesDescriptionHover();
                    } else {
                        $("#editPersonModal .modal-body").html(resultObj.messages);
                    }

                    FormMessageArea.clearMessageArea();
                },
            });
        });
    }

    listenForChangeFm() {
        var $this = this;
        $(document).on("change", "#" + $this.child.FMCnumFieldId, function (e) {
            $("#" + $this.child.updateButtonId).attr("disabled", true);
            $("#" + $this.child.FMCheckMsg).show();
        });
    }

    listenForConfirmChangeFm() {
        var $this = this;
        $(document).on("click", "#" + $this.child.FMConfirmButtonId, function (e) {
            $("#" + $this.child.updateButtonId).attr("disabled", false);
            $("#" + $this.child.FMCheckMsg).hide();
        });
    }

    listenForResetChangeFm() {
        var $this = this;
        $(document).on("click", "#" + $this.child.FMResetButtonId, function (e) {
            var originalFm = $("#" + $this.child.FMOriginalCnumFieldId).val();
            console.log(originalFm);
            $(document).off("change", "#" + $this.child.FMCnumFieldId);
            $("#" + $this.child.FMCnumFieldId).val(originalFm).trigger("change");
            $this.listenForChangeFm();
            $("#" + $this.child.updateButtonId).attr("disabled", false);
            $("#" + $this.child.FMCheckMsg).hide();
        });
    }

    listenforUpdateBoarding() {
        var $this = this;
        $(document).on("click", "#" + $this.child.updateButtonId, function () {
            var form = $("#" + $this.child.formId);
            var updateButton = $("#" + $this.child.updateButtonId);
            $this.child.saveBoardingForm("Update", form, updateButton, null, $this.table);
        });
    }
}

export { editPersonBox as default };