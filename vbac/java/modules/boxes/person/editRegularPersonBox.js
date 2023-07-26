/*
 *
 *
 *
 */

let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');

let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate_Regular.js');
let initialiseOtherDates = await cacheBustImport('./modules/functions/initialiseOtherDates_Regular.js');
let initialiseFormSelect2 = await cacheBustImport('./modules/functions/initialiseFormSelect2_Regular.js');

let pesDescriptionHover = await cacheBustImport('./modules/functions/pesDescriptionHover.js');
let saveRegularBoarding = await cacheBustImport('./modules/functions/saveRegularBoarding.js');
let spinner = await cacheBustImport('./modules/functions/spinner.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class editRegularPersonBox extends box {

    static formId = 'boardingFormIbmer';
    static saveButtonId = 'saveRegularBoarding';
    static updateButtonId = 'updateRegularBoarding';
    static resetButtonId = 'resetRegularBoarding';
    static initiatePesButtonId = 'initiateRegularPes';

    updateButton;
    responseObj;

    constructor(parent) {
        console.log('+++ Function +++ editRegularPersonBox.constructor');

        super(parent);

        this.updateButton = $("#" + editRegularPersonBox.updateButtonId);

        this.listenForEditPerson(document.isFm, document.isCdi);

        this.listenForChangeFm();
        this.listenForConfirmChangeFm();
        this.listenForResetChangeFm();

        this.listenforUpdateBoarding();

        console.log('--- Function --- editRegularPersonBox.constructor');
    }

    listenForEditPerson(isFmp, isCdip) {
        var isFm = isFmp;
        var isCdi = isCdip;
        var $this = this;
        $(document).on("click", ".btnEditRegularPerson", function (e) {
            var cnum = $(this).data("cnum");
            $("#editPersonModal .modal-body").html(spinner);
            $("#editPersonModal").modal("show");
            $.ajax({
                url: "ajax/getEditPersonModalBody.php",
                data: { cnum: cnum },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    if (!resultObj.messages) {
                        $("#editPersonModal .modal-body").html(resultObj.body);

                        FormMessageArea.showMessageArea();

                        initialiseStartEndDate();
                        initialiseOtherDates();
                        initialiseFormSelect2();

                        // var ctbRtb = resultObj.ctbRtb;
                        // switch (ctbRtb) {
                        //     case "CTB":
                        //         $(".ctbRtb")[0].click();
                        //         break;
                        //     case "RTB":
                        //         $(".ctbRtb")[1].click();
                        //         break;
                        //     default:
                        //         $(".ctbRtb")[2].click();
                        //         break;
                        // }

                        // $("#LBG_LOCATION").select2({
                        //     width: "100%",
                        //     placeholder: "Approved Location",
                        //     allowClear: true,
                        // });

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
        $(document).on("change", "#person_FM_CNUM", function (e) {
            $("#" + editRegularPersonBox.updateButtonId).attr("disabled", true);
            $("#personFmPanelBodyCheckMsg").show();
        });
    }

    listenForConfirmChangeFm() {
        $(document).on("click", "#confirmFmChangePerson", function (e) {
            $("#" + editRegularPersonBox.updateButtonId).attr("disabled", false);
            $("#personFmPanelBodyCheckMsg").hide();
        });
    }

    listenForResetChangeFm() {
        var $this = this;
        $(document).on("click", "#resetFmChangePerson", function (e) {
            var originalFm = $("#person_original_fm").val();
            console.log(originalFm);
            $(document).off("change", "#person_FM_CNUM");
            $("#person_FM_CNUM").val(originalFm).trigger("change");
            $this.listenForChangeFm();
            $("#" + editRegularPersonBox.updateButtonId).attr("disabled", false);
            $("#personFmPanelBodyCheckMsg").hide();
        });
    }

    listenforUpdateBoarding() {
        var $this = this;
        $(document).on("click", "#" + editRegularPersonBox.updateButtonId, function () {
            var form = $("#" + editRegularPersonBox.formId);
            saveRegularBoarding("Update", form, $this.updateButton, null, $this.table);
        });
    }
}

export { editRegularPersonBox as default };