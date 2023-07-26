/*
 *
 *
 *
 */

let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');

let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate_Vendor.js');
let initialiseOtherDates = await cacheBustImport('./modules/functions/initialiseOtherDates_Vendor.js');
let initialiseOnboardNonIBMPersonFormSelect2 = await cacheBustImport('./modules/functions/initialiseFormSelect2_Vendor.js');

let pesDescriptionHover = await cacheBustImport('./modules/functions/pesDescriptionHover.js');
let saveVendorBoarding = await cacheBustImport('./modules/functions/saveVendorBoarding.js');
let spinner = await cacheBustImport('./modules/functions/spinner.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class editVendorPersonBox extends box {

    static formId = 'boardingFormNotIbmer';
    static saveButtonId = 'saveVendorBoarding';
    static updateButtonId = 'updateVendorBoarding';
    static resetButtonId = 'resetVendorBoarding';
    static initiatePesButtonId = 'initiateVendorPes';

    updateButton;
    responseObj;

    constructor(parent) {
        console.log('+++ Function +++ editVendorPersonBox.constructor');

        super(parent);

        this.updateButton = $("#" + editVendorPersonBox.updateButtonId);

        this.listenForEditPerson(document.isFm, document.isCdi);

        this.listenForChangeFm();
        this.listenForConfirmChangeFm();
        this.listenForResetChangeFm();

        this.listenforUpdateBoarding();

        console.log('--- Function --- editVendorPersonBox.constructor');
    }

    listenForEditPerson(isFmp, isCdip) {
        var isFm = isFmp;
        var isCdi = isCdip;
        var $this = this;
        $(document).on("click", ".btnEditVendorPerson", function (e) {
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
                        initialiseOnboardNonIBMPersonFormSelect2();

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
        $(document).on("change", "#resource_FM_CNUM", function (e) {
            $("#" + editVendorPersonBox.updateButtonId).attr("disabled", true);
            $("#resourceFmPanelBodyCheckMsg").show();
        });
    }

    listenForConfirmChangeFm() {
        $(document).on("click", "#confirmFmChangeResource", function (e) {
            $("#" + editVendorPersonBox.updateButtonId).attr("disabled", false);
            $("#resourceFmPanelBodyCheckMsg").hide();
        });
    }

    listenForResetChangeFm() {
        var $this = this;
        $(document).on("click", "#resetFmChangeResource", function (e) {
            var originalFm = $("#person_original_fm").val();
            console.log(originalFm);
            $(document).off("change", "#resource_FM_CNUM");
            $("#resource_FM_CNUM").val(originalFm).trigger("change");
            $this.listenForChangeFm();
            $("#" + editVendorPersonBox.updateButtonId).attr("disabled", false);
            $("#resourceFmPanelBodyCheckMsg").hide();
        });
    }

    listenforUpdateBoarding() {
        var $this = this;
        $(document).on("click", "#" + editVendorPersonBox.updateButtonId, function () {
            var form = $("#" + editVendorPersonBox.formId);
            saveVendorBoarding("Update", form, $this.updateButton, null, $this.table);
        });
    }
}

export { editVendorPersonBox as default };