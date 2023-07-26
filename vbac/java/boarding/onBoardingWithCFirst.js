/*
 *
 *
 *
 */

let validateEmail = await cacheBustImport('./modules/functions/validateEmail.js');
let convertOceanToKyndryl = await cacheBustImport('./modules/functions/convertOceanToKyndryl.js');
let checkIbmEmailAddress = await cacheBustImport('./modules/functions/checkIbmEmailAddress.js');
let checkOceanEmailAddress = await cacheBustImport('./modules/functions/checkOceanEmailAddress.js');
let checkKyndrylEmailAddress = await cacheBustImport('./modules/functions/checkKyndrylEmailAddress.js');
let inArrayCaseInsensitive = await cacheBustImport('./modules/functions/inArrayCaseInsensitive.js');

let fetchBluepagesDetailsForCnum = await cacheBustImport('./modules/functions/fetchBluepagesDetailsForCnum.js');
let initialiseOnboardPersonFormSelect2 = await cacheBustImport('./modules/functions/initialiseOnboardPersonFormSelect2.js');
let initialiseOnboardNonIBMPersonFormSelect2 = await cacheBustImport('./modules/functions/initialiseOnboardNonIBMPersonFormSelect2.js');
let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate.js');

let pesInitiateFromBoardingBox = await cacheBustImport('./modules/boxes/person/pesInitiateFromBoardingBox.js');
let editEntryBox = await cacheBustImport('./modules/boxes/person/editEntryWithCFirstBox.js');
let pesDescriptionBox = await cacheBustImport('./modules/boxes/person/pesDescriptionBox.js');

// let knownCNUMs = await cacheBustImport('./modules/dataSources/knownCNUMs.js');
// let knownExternalEmails = await cacheBustImport('./modules/dataSources/knownExternalEmails.js');
// let knownIBMEmails = await cacheBustImport('./modules/dataSources/knownIBMEmails.js');
// let knownKyndrylEmails = await cacheBustImport('./modules/dataSources/knownKyndrylEmails.js');

class onBoardingWithCFirst {

    constructor() {
        // $('.toggle').bootstrapToggle();

        initialiseStartEndDate();
        initialiseOnboardPersonFormSelect2();
        // initialiseOnboardNonIBMPersonFormSelect2();

        // this.initialiseBoardingForm();

        this.listenForHasBpEntry();
        this.listenForName();
        this.listenForEmailChange();
        this.listenForEmailFocusOut();
        this.listenForSerial();
        this.listenForLinkToPreBoarded();
        this.listenForEmployeeTypeRadioBtn();
        this.listenForResetBoardingIbmerForm();
        this.listenForResetBoardingNonIbmerForm();
    }

    initialiseBoardingForm() {

        let cnum = $("#person_serial").val();
        let resultXXX = cnum.endsWith("XXX");
        let resultxxx = cnum.endsWith("xxx");
        let result999 = cnum.endsWith("999");

        if (resultXXX || resultxxx || result999) {
            $("#notAnIbmer").show();
            $("#existingIbmer").hide();
        } else {
            $("#notAnIbmer").hide();
            $("#existingIbmer").show();
        }
    }

    listenForHasBpEntry() {
        $(document).on("change", "#hasBpEntry", function () {
            $("#notAnIbmer").toggle();
            $("#existingIbmer").toggle();
            // $("#linkToPreBoarded").toggle();
            if ($("#notAnIbmer").is(":visible")) {

                initialiseOnboardNonIBMPersonFormSelect2();

                $("#notAnIbmer :input").attr("required", true);
                $("#existingIbmer :input").attr("required", false);

                var form = $("#boardingFormIbmer");
                form[0].reset();

                // employee type selection
                $(".employeeTypeRadioBtn input[type=radio]").prop("required", true);

                $("#linkToPreBoarded").hide();
                $("#LBG_LOCATION").val("").trigger("change");
                $("#person_preboarded").val("").trigger("change"); // incase they already selected a pre-boarder - we need to clear this field.

                $("#open_seat").attr("placeholder", "Open Seat Number");
                $("#editCtidDiv").show();

                $("#saveBoarding").attr("disabled", false);
            } else {
                $("#notAnIbmer :input").attr("required", false);
                $("#existingIbmer :input").attr("required", true);

                var form = $("#boardingFormNotIbmer");
                form[0].reset();

                // employee type selection
                $(".employeeTypeRadioBtn input[type=radio]").removeAttr("required");

                $("#linkToPreBoarded").show();
                $("#LBG_LOCATION").val("").trigger("change");
                $("#person_preboarded").val("").trigger("change"); // incase they already selected a pre-boarder - we need to clear this field.

                $("#open_seat").attr("placeholder", "Open Seat Number");
                $("#editCtidDiv").hide();

                $("#saveBoarding").attr("disabled", true);
            }
            var currentHeading = $("#employeeResourceHeading").text();
            var newHeading =
                currentHeading == "Resource Details - Use external email addresses"
                    ? "Resource Details - Kyndryl employees use Ocean IDs"
                    : "Resource Details - Use external email addresses";
            $("#employeeResourceHeading").text(newHeading);
        });
    }

    listenForName() {
        var $this = this;
        $(".typeahead").bind("typeahead:select", function (ev, suggestion) {
            $(".tt-menu").hide();
            $("#person_notesid").val(suggestion.notesEmail);
            $("#person_serial").val(suggestion.cnum).attr("disabled", "disabled");
            $("#person_bio").val(suggestion.role);
            $("#person_intranet").val(suggestion.mail);
            var kynValue = convertOceanToKyndryl(suggestion.mail);
            $("#person_kyn_intranet").val(kynValue);

            var newCnum = suggestion.cnum;

            // let knownCnum = await knownCNUMs.getCNUMs();
            var allreadyExists = $.inArray(newCnum, knownCnum) >= 0;
            if (allreadyExists) {
                // comes back with Position in array(true) or false is it's NOT in the array.
                $("#saveBoarding").attr("disabled", true);
                $("#person_name").css("background-color", "LightPink");
                $('#messageModalBody').html("<p>Person already defined to VBAC</p>");
                $('#messageModal').modal('show');
            } else {
                $("#saveBoarding").attr("disabled", false);
                $("#person_name").css("background-color", "LightGreen");
                fetchBluepagesDetailsForCnum(suggestion.cnum);
            }

            $("#personDetails").show();
        });
    }

    listenForEmailChange() {
        $(document).on("change", "#resource_email", function () {
            console.log('resource_email change');
            // var newEmail = $("#resource_email").val();
            var newEmail = $(this).val();
            var trimmedEmail = newEmail.trim();
            if (trimmedEmail !== "") {

                // validate email address
                if (validateEmail(trimmedEmail)) {

                    // let knownExternalEmail = await knownExternalEmails.getEmails();
                    // let knownIBMEmail = await knownIBMEmails.getEmails();
                    // let knownKyndrylEmail = await knownKyndrylEmails.getEmails();

                    var allreadyExternalExists = inArrayCaseInsensitive(trimmedEmail, knownExternalEmail) >= 0;
                    var allreadyIBMExists = inArrayCaseInsensitive(trimmedEmail, knownIBMEmail) >= 0;
                    var allreadyKyndrylExists = inArrayCaseInsensitive(trimmedEmail, knownKyndrylEmail) >= 0;

                    var ibmEmailAddress = checkIbmEmailAddress(trimmedEmail);
                    var oceanEmailAddress = checkOceanEmailAddress(trimmedEmail);
                    var kyndrylEmailAddress = checkKyndrylEmailAddress(trimmedEmail);

                    if (allreadyExternalExists || allreadyIBMExists) {
                        // comes back with Position in array(true) or false is it's NOT in the array.
                        $("#saveBoarding").attr("disabled", true);
                        $("#resource_email").css("background-color", "LightPink");
                    // } else if (ibmEmailAddress) {
                    //     $("#saveBoarding").attr("disabled", true);
                    //     $("#resource_email").css("background-color", "Red");
                    } else if (oceanEmailAddress) {
                        $("#saveBoarding").attr("disabled", true);
                        $("#resource_email").css("background-color", "Red");
                    } else if (kyndrylEmailAddress) {
                        $("#saveBoarding").attr("disabled", true);
                        $("#resource_email").css("background-color", "Red");
                    } else {
                        $("#saveBoarding").attr("disabled", false);
                        $("#resource_email").css("background-color", "LightGreen");
                    }
                } else {
                    // can not proceed
                    $("#saveBoarding").attr("disabled", true);
                    $("#resource_email").css("background-color", "Red");
                }
            } else {
                // no need to check
                $("#saveBoarding").attr("disabled", true);
                $("#resource_email").val("");
                $("#resource_email").css("background-color", "white");
            }
        });
    }

    listenForEmailFocusOut() {
        $(document).on("focusout", "#resource_email", function () {
            $("#open_seat").attr("placeholder", "Open Seat Number");
            // var newEmail = $("#resource_email").val();
            var newEmail = $(this).val();
            var trimmedEmail = newEmail.trim();
            if (trimmedEmail !== "") {

                // validate email address
                if (validateEmail(trimmedEmail)) {

                    // let knownExternalEmail = await knownExternalEmails.getEmails();
                    // let knownIBMEmail = await knownIBMEmails.getEmails();
                    // let knownKyndrylEmail = await knownKyndrylEmails.getEmails();

                    var allreadyExternalExists = inArrayCaseInsensitive(trimmedEmail, knownExternalEmail) >= 0;
                    var allreadyIBMExists = inArrayCaseInsensitive(trimmedEmail, knownIBMEmail) >= 0;
                    var allreadyKyndrylExists = inArrayCaseInsensitive(trimmedEmail, knownKyndrylEmail) >= 0;

                    var ibmEmailAddress = checkIbmEmailAddress(trimmedEmail);
                    var oceanEmailAddress = checkOceanEmailAddress(trimmedEmail);
                    var kyndrylEmailAddress = checkKyndrylEmailAddress(trimmedEmail);

                    if (allreadyExternalExists || allreadyIBMExists) {
                        // comes back with Position in array(true) or false is it's NOT in the array.
                        $('#messageModalBody').html("<p>Email address already defined to VBAC</p>");
                        $('#messageModal').modal('show');
                        return false;
                        // } else if (ibmEmailAddress) {
                        //     $('#messageModalBody').html("<p>IBMers should NOT BE Pre-Boarded. Please board as an IBMer</p>");
                        //     $('#messageModal').modal('show');
                    } else if (oceanEmailAddress) {
                        $('#messageModalBody').html("<p>Ocean IDs should NOT BE Pre-Boarded. Please board as a regular employee</p>");
                        $('#messageModal').modal('show');
                    } else if (kyndrylEmailAddress) {
                        $('#messageModalBody').html("<p>Kyndryls should NOT BE Pre-Boarded. Please board as a regular employee</p>");
                        $('#messageModal').modal('show');
                    } else {

                    }
                } else {
                    // can not proceed
                    $('#messageModalBody').html("<p>Provided email address in invalid</p>");
                    $('#messageModal').modal('show');
                }
            } else {
                // no need to check
            }
        });
    }

    listenForSerial() {
        var $this = this;
        $(document).on("keyup change", "#person_serial", function (e) {
            var cnum = $(this).val();
            if (cnum.length == 9) {
                fetchBluepagesDetailsForCnum(cnum);
            }
        });
    }

    listenForLinkToPreBoarded() {
        $(document).on("select2:select", "#person_preboarded", function (e) {
            var data = e.params.data;
            if (data.id != "") {
                // They have selected an entry
                var allEnabled = $("form :enabled");
                $(allEnabled).attr("disabled", true);
                $("#saveBoarding").addClass("spinning");
                $("#initiatePes").hide();
                $.ajax({
                    url: "ajax/prePopulateFromLink.php",
                    type: "POST",
                    data: { cnum: data.id },
                    success: function (result) {
                        $("#saveBoarding").removeClass("spinning");
                        var resultObj = JSON.parse(result);
                        if (resultObj.success == true) {
                            $(allEnabled).attr("disabled", false);
                            var $radios = $("input:radio[name=CTB_RTB]");
                            $($radios).attr("disabled", false);

                            if (resultObj.data.CTB_RTB != null) {
                                var button = $radios.filter(
                                    "[value=" + resultObj.data.CTB_RTB.trim() + "]"
                                );
                                $(button).prop("checked", true);
                                $(button).trigger("click");
                            }

                            var $radios = $(".accountOrganisation");
                            $($radios).attr("disabled", false);

                            // if (resultObj.data.TT_BAU != null) {
                            //     var button = $radios.filter(
                            //         "[value='" + resultObj.data.TT_BAU.trim() + "']"
                            //     );
                            //     $(button).prop("checked", true);
                            //     $(button).trigger("click");
                            // }
                            var contractorIdReq;
                            if (resultObj.data.CT_ID_REQUIRED != null) {
                                if (
                                    resultObj.data.CT_ID_REQUIRED.trim()
                                        .toUpperCase()
                                        .substring(0, 1) == "Y"
                                ) {
                                    contractorIdReq = "yes";
                                } else {
                                    contractorIdReq = "no";
                                }
                            } else {
                                contractorIdReq = "no";
                            }
                            $("#person_contractor_id_required")
                                .attr("disabled", false)
                                .val(contractorIdReq)
                                .trigger("change");

                            if (resultObj.data.FM_CNUM != null) {
                                $("#FM_CNUM")
                                    .attr("disabled", false)
                                    .val(resultObj.data.FM_CNUM.trim())
                                    .trigger("change");
                            }

                            if (resultObj.data.OPEN_SEAT_NUMBER != null) {
                                var openSeatNumber = resultObj.data.OPEN_SEAT_NUMBER;
                                $("#open_seat").attr("disabled", false);
                                if (openSeatNumber) {
                                    $("#open_seat").val(openSeatNumber.trim());
                                }
                            }

                            if (resultObj.data.CIO_ALIGNMENT != null) {
                                var cioAlignment = resultObj.data.CIO_ALIGNMENT.trim();
                                $("#cioAlignment").attr("disabled", false);
                                if (cioAlignment) {
                                    $("#cioAlignment").val(cioAlignment).trigger("change");
                                }
                            }

                            if (resultObj.data.LOB != null) {
                                $("#lob").attr("disabled", false);
                                $("#lob").val(resultObj.data.LOB.trim()).trigger("change");
                            }

                            // var workStream = resultObj.data.WORK_STREAM;
                            // if (workStream) {
                            //     $("#work_stream").val(workStream.trim()).trigger("change");
                            // }

                            // var roleOnAccount = resultObj.data.ROLE_ON_THE_ACCOUNT;
                            // if (roleOnAccount) {
                            //     $("#role_on_account").val(roleOnAccount.trim());
                            // }
                            // $("#role_on_account").attr("disabled", false);

                            var sDate = resultObj.data.START_DATE;
                            $("#start_date").attr("disabled", false);
                            if (sDate) {
                                // startPicker.setDate(sDate);
                                $("#start_date").datepicker("setDate", sDate);
                            }

                            var eDate = resultObj.data.PROJECTED_END_DATE;
                            $("#end_date").attr("disabled", false);
                            if (eDate) {
                                //  endPicker.setDate(eDate);
                                $("#end_date").datepicker("setDate", eDate);
                            }

                            var pesDateReq = resultObj.data.PES_DATE_REQUESTED;
                            if (pesDateReq) {
                                $("#pes_date_requested").val(pesDateReq.trim());
                            }

                            var pesDateResp = resultObj.data.PES_DATE_RESPONDED;
                            if (pesDateResp) {
                                $("#pes_date_responded").val(pesDateResp.trim());
                            }

                            var pesRequestor = resultObj.data.PES_REQUESTOR;
                            if (pesRequestor) {
                                $("#pes_requestor").val(pesRequestor.trim());
                            }

                            var pesStatus = resultObj.data.PES_STATUS;
                            if (pesStatus) {
                                $("#pes_status").val(pesStatus.trim());
                            }

                            var pesStatusDet = resultObj.data.PES_STATUS_DETAILS;
                            if (pesStatusDet) {
                                $("#pes_status_details").val(pesStatusDet.trim());
                            }

                            var pesLevel = resultObj.data.PES_LEVEL;
                            if (pesLevel) {
                                $("#pesLevel").val(pesLevel.trim()).trigger("change");
                            }

                            var pesClearedDate = resultObj.data.PES_CLEARED_DATE;
                            if (pesClearedDate) {
                                $("#pes_cleared_date").val(pesClearedDate.trim());
                            }

                            var pesRecheckDate = resultObj.data.PES_RECHECK_DATE;
                            if (pesRecheckDate) {
                                $("#pes_recheck_date").val(pesRecheckDate.trim());
                            }

                            $(allEnabled).attr("disabled", false); // open up the fields we'd disabled.
                        }
                    },
                });
            } else {

            }
        });
    }

    listenForEmployeeTypeRadioBtn() {
        $(document).on("click", "input[name=employeeType]", function (e) {
            var selectedEmployeeType = $("input[name=employeeType]:checked");
            var employeeType = selectedEmployeeType.val();
            var type = selectedEmployeeType.data("type");

            $("#resource_employee_type").val(employeeType);

            switch (employeeType) {
                case 'preboarder':
                    switch (type) {
                        case 'ibmer':
                            $("#resource_email")
                                .attr("disabled", false)
                                .attr("required", true)
                                .attr("placeholder", "Email Address")
                                .css("background-color", "white");
                            break;
                        default:
                            $("#resource_email")
                                .attr("disabled", false)
                                .attr("required", true)
                                .attr("placeholder", "Email Address")
                                .css("background-color", "white");
                            break;
                    }
                    $("#saveBoarding").attr("disabled", true);
                    $("#open_seat").val("");
                    $("#role_on_account")
                        .val("")
                        .attr("disabled", false);
                    break;
                case 'vendor':
                    switch (type) {
                        case 'other':
                            $("#resource_email")
                                .val("")
                                .attr("disabled", false)
                                .attr("required", false)
                                .attr("placeholder", "Enter Email Address if PES required, else blank")
                                .css("background-color", "white");
                            break;
                        default:
                            $("#resource_email")
                                .val("")
                                .attr("disabled", true)
                                .attr("required", false)
                                .attr("placeholder", "Not required - GDPR")
                                .css("background-color", "#eeeeee");
                            break;
                    }
                    $("#saveBoarding").attr("disabled", false);
                    var Type = type[0].toUpperCase() + type.slice(1).toLowerCase();
                    $("#open_seat").val(Type);
                    $("#role_on_account")
                        .val(Type)
                        .attr("disabled", true);
                    break;
                default:
                    break;
            }
        });
    }

    listenForResetBoardingIbmerForm() {
        var $this = this;
        $(document).on('reset', '#boardingFormIbmer', function (event) {
            console.log("reset clicked boardingFormIbmer");
            // event.preventDefault();

            // fields in IBMer
            // $("#person_name").val("").trigger("change");
            $("#person_name").css("background-color", "");
            // $("#person_serial").val("").trigger("change");

            // $("#person_notesid").val("").trigger("change");
            // $("#person_intranet").val("").trigger("change");
            // $("#person_bio").val("").trigger("change");

        });
    }

    listenForResetBoardingNonIbmerForm() {
        var $this = this;
        $(document).on('reset', '#boardingFormNotIbmer', function (event) {
            console.log("reset clicked boardingFormNotIbmer");
            // event.preventDefault();

            // fields in none IBMer
            // $("#resource_first_name").val("").trigger("change");
            // $("#resource_last_name").val("").trigger("change");
            // $("#resource_email").val("").trigger("change");

            $("#resource_email").val("").trigger("change");

            // if ($("#resource_country").data("select2")) {
            //     $("#resource_country").select2("destroy");
            // }
            // $("#resource_country").select2();

        });
    }
}

const OnBoardingWithCFirst = new onBoardingWithCFirst();

const PesInitiateFromBoardingBox = new pesInitiateFromBoardingBox(OnBoardingWithCFirst);
const EditEntryBox = new editEntryBox(OnBoardingWithCFirst);
const PesDescriptionBox = new pesDescriptionBox(OnBoardingWithCFirst);

export { OnBoardingWithCFirst as default };