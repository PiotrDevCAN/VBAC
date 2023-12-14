/*
 *
 *
 *
 */

let initialisePersonFormSelect2 = await cacheBustImport('./modules/functions/initialisePersonFormSelect2.js');
let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate.js');
let pesDescriptionHover = await cacheBustImport('./modules/functions/pesDescriptionHover.js');
let saveBoardingWithCFirst = await cacheBustImport('./modules/functions/saveBoardingWithCFirst.js');
let spinner = await cacheBustImport('./modules/functions/spinner.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class editPersonWithCFirstBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editPersonWithCFirstBox.constructor');

        super(parent);
        this.listenForEditPerson(document.isFm, document.isCdi);
        this.listenForCtbRtb();
        this.listenForAccountOrganisation();

        this.listenForChangeFm();
        this.listenForConfirmChangeFm();
        this.listenForResetChangeFm();

        this.listenforUpdateBoarding();

        console.log('--- Function --- editPersonWithCFirstBox.constructor');
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
                data: {
                    cnum: cnum
                },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    $(".employeeTypeRadioBtn input[type=radio]").removeAttr("required");

                    if (!resultObj.messages) {
                        $("#editPersonModal .modal-body").html(resultObj.body);

                        let cnum = $("#person_serial").val();
                        let resultXXX = cnum.endsWith("XXX");
                        let resultxxx = cnum.endsWith("xxx");
                        let result999 = cnum.endsWith("999");

                        if (resultXXX || resultxxx || result999) {
                            var newHeading = "Resource Details - Use external email addresses";
                            $("#notAnIbmer").show();
                            $("#existingIbmer").hide();
                        } else {
                            var newHeading = "Resource Details - Kyndryl employees use Kyndryl IDs";
                            $("#notAnIbmer").hide();
                            $("#existingIbmer").show();
                        }
                        $("#employeeResourceHeading").text(newHeading);

                        initialisePersonFormSelect2();
                        $.fn.modal.Constructor.prototype.enforceFocus = function () { };
                        var accountOrganisation = resultObj.accountOrganisation;
                        if (accountOrganisation == "T&T") {
                            $(".accountOrganisation")[0].click();
                        }
                        if (accountOrganisation == "BAU") {
                            $(".accountOrganisation")[1].click();
                        }
                        var ctbRtb = resultObj.ctbRtb;
                        switch (ctbRtb) {
                            case "CTB":
                                $(".ctbRtb")[0].click();
                                break;
                            case "RTB":
                                $(".ctbRtb")[1].click();
                                break;
                            default:
                                $(".ctbRtb")[2].click();
                                break;
                        }
                        initialiseStartEndDate();
                        $("#LBG_LOCATION").select2({
                            width: "100%",
                            placeholder: "Approved Location",
                            allowClear: true,
                        });

                        if (isFm == "yes") {
                            // Dont let FM Edit the Func Mgr Field, Ant Stark November 12th 2020
                            // Let the FM EDit the FUnc Mgr Field, Ant Stark January 26th 2021
                            // $('#FM_CNUM').attr('disabled',true);
                        }

                        if (isCdi == "yes") {
                            $("#person_intranet").attr("disabled", false);
                        }

                        pesDescriptionHover();
                    } else {
                        $("#editPersonModal .modal-body").html(resultObj.messages);
                    }
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

    listenForAccountOrganisation() {
        $(document).on("click", '.accountOrganisation', function () {
            var accountOrganisation = $(".accountOrganisation:checked").val();
            var nullFirstEntry = [""];

            if (typeof workStream != "undefined") {
                for (var i = 0; i < workStream.length; i++) {
                    if (workStream[0][i] == accountOrganisation) {
                        var workStreamValues = nullFirstEntry.concat(workStream[i + 1]);
                    }
                }
            }

            if ($("#work_stream").data("select2")) {
                $("#work_stream").select2("destroy");
            }
            // $('#work_stream').html('');

            if (typeof workStreamValues != "undefined") {
                $("#work_stream")
                    .select2({
                        data: workStreamValues,
                        placeholder: "Select workstream",
                    })
                    .attr("disabled", false)
                    .attr("required", true);
            } else {
                $("#work_stream")
                    .select2({
                        data: [""],
                        placeholder: "No workstream required",
                    })
                    .attr("disabled", true)
                    .attr("required", false);
            }

            var currentWorkstream = $("#currentWorkstream").val();

            if (currentWorkstream != "") {
                $("#work_stream").val(currentWorkstream); // Select the option with a value of currentWorkstream
                $("#work_stream").trigger("change");
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
            saveBoardingWithCFirst("Update", $this.tableObj.table);
        });
    }
}

export { editPersonWithCFirstBox as default };