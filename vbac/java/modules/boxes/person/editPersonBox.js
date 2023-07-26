/*
 *
 *
 *
 */

let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');

let initialisePersonFormSelect2 = await cacheBustImport('./modules/functions/initialisePersonFormSelect2.js');
let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate.js');
let initialiseProposedLeavingDate = await cacheBustImport('./modules/functions/initialiseProposedLeavingDate.js');
let pesDescriptionHover = await cacheBustImport('./modules/functions/pesDescriptionHover.js');
let saveBoarding = await cacheBustImport('./modules/functions/saveBoarding.js');
let spinner = await cacheBustImport('./modules/functions/spinner.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class editPersonBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editPersonBox.constructor');

        super(parent);
        this.listenForEditPerson(document.isFm, document.isCdi);
        this.listenForCtbRtb();

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
        $(document).on("click", ".btnEditPerson", function (e) {
            var cnum = $(this).data("cnum");
            $("#editPersonModal .modal-body").html(spinner);
            $("#editPersonModal").modal("show");
            $.ajax({
                url: "ajax/getEditPersonModalBody.php",
                data: { cnum: cnum },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    $(".employeeTypeRadioBtn input[type=radio]").removeAttr("required");

                    if (!resultObj.messages) {
                        $("#editPersonModal .modal-body").html(resultObj.body);

                        FormMessageArea.showMessageArea();

                        let employeeType_person = $("#person_employee_type").val();
                        let employeeType_resource = $("#resource_employee_type").val();
                        let employeeType = '';
                        if (typeof employeeType_person != "undefined") {
                            employeeType = employeeType_person;
                        } else {                        
                            employeeType = employeeType_resource;
                        }

                        switch(employeeType) {
                            case 'Regular':
                            case 'Contractor':
                                alert('it is Regular/Contractor');

                                break;
                            case 'Pre-Hire':
                            case 'preboarder':
                            case 'vendor':
                                alert('it is Pre-Hire/Vendor');

                                break;
                            default:
                                break;
                        }

                        // initialisePersonFormSelect2();
                        // $.fn.modal.Constructor.prototype.enforceFocus = function () { };

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
                        // initialiseStartEndDate();
                        // initialiseProposedLeavingDate();
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

                        // pesDescriptionHover();
                    } else {
                        $("#editPersonModal .modal-body").html(resultObj.messages);
                    }

                    FormMessageArea.clearMessageArea();
                },
            });
        });
    }

    listenForCtbRtb() {
        $(document).on("click", ".ctbRtb", function () {
            var ctbRtb = $(this).val();
            if ($("#cioAlignment").data("select2")) {
                $("#cioAlignment").select2("destroy");
            }
            if (ctbRtb == "CTB") {
                $("#cioAlignment")
                    .select2({
                        placeholder: "Select CIO Alignment",
                    })
                    .attr("disabled", false)
                    .attr("required", true);
            } else {
                $("#cioAlignment").val("").trigger("change");
                $("#cioAlignment")
                    .select2({
                        placeholder: "Not required",
                    })
                    .attr("disabled", true)
                    .attr("required", false);
            }
        });
    }

    listenForChangeFm() {
        $(document).on("change", "#FM_CNUM", function (e) {
            $("#updateBoarding").attr("disabled", true);
            $("#fmPanelBodyCheckMsg").show();
        });
    }

    listenForConfirmChangeFm() {
        $(document).on("click", "#confirmFmChange", function (e) {
            $("#updateBoarding").attr("disabled", false);
            $("#fmPanelBodyCheckMsg").hide();
        });
    }

    listenForResetChangeFm() {
        var $this = this;
        $(document).on("click", "#resetFmChange", function (e) {
            var originalFm = $("#originalFm").val();
            console.log(originalFm);
            $(document).off("change", "#FM_CNUM");
            $("#FM_CNUM").val(originalFm).trigger("change");
            $this.listenForChangeFm();
            $("#updateBoarding").attr("disabled", false);
            $("#fmPanelBodyCheckMsg").hide();
        });
    }

    listenforUpdateBoarding() {
        var $this = this;
        $(document).on("click", "#updateBoarding", function () {
            saveBoarding("Update", $this.tableObj.table);
        });
    }
}

export { editPersonBox as default };