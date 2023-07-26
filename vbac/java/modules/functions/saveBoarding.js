function saveBoarding(mode, table) {
    $("#saveBoarding").addClass("spinning");
    $("#updateBoarding").addClass("spinning");
    var ibmer = $("#hasBpEntry").is(":checked");
    
    // var form = $("#boardingForm");
    // var formValid = form[0].checkValidity();

    var formIbmer = $("#boardingFormIbmer");
    var formNotIbmer = $("#boardingFormNotIbmer");
    var formLink = $("#boardingFormLink");
    var formCommon = $("#boardingFormCommon");

    var formValidIbmerValid = formIbmer[0].checkValidity();
    var formNotIbmerValid = formNotIbmer[0].checkValidity();
    var formLinkValid = formLink[0].checkValidity();
    var formCommonValid = formCommon[0].checkValidity();
    
    var $this = this;
    // if (formValid) {
    if (formValidIbmerValid && 
        formNotIbmerValid && 
        formLinkValid && 
        formCommonValid
    ) {
        var boardingFormEnabledInputs = $("input:enabled");
        var allDisabledFields = $("input:disabled").not("#saveBoarding");
        $(allDisabledFields).attr("disabled", false);
        // var formData = form.serialize();

        var formData = '';
        formData += formIbmer.serialize();
        formData +='&';
        formData += formNotIbmer.serialize();
        formData +='&';
        formData += formLink.serialize();
        formData +='&';
        formData += formCommon.serialize();

        formData += "&mode=" + mode + "&boarding=" + ibmer;
        
        $(allDisabledFields).attr("disabled", true);
        $.ajax({
            url: "ajax/saveBoardingForm.php",
            type: "POST",
            data: formData,
            success: function (result) {
                $("#saveBoarding").removeClass("spinning");
                $("#updateBoarding").removeClass("spinning");
                var resultObj = JSON.parse(result);
                var message = "";
                var panelclass = "";
                if (resultObj.success == true) {
                    $("#person_uid").val(resultObj.cnum);
                    message += "<div class=panel-heading><h3 class=panel-title>Success</h3>" + resultObj.messages;
                    message += resultObj.offboarding
                        ? "<br/><h4>Offboarding has been initiated</h4></br>"
                        : "";
                    if (resultObj.offboarding) {
                        panelclass = "panel-warning";
                    } else {
                        panelclass = "panel-success";
                    }
                    $("#boardingForm :input").attr("disabled", true);
                    $("#saveBoarding").attr("disabled", true);
                    $("#initiatePes").attr("disabled", false);
                    $("#hasBpEntry").bootstrapToggle("disable");
                } else {
                    message += "<div class=panel-heading><h3 class=panel-title>Error : Please inform vBAC Support</h3>" + resultObj.messages;
                    panelclass = "panel-danger";
                    $("#saveBoarding").attr("disabled", false);
                    $("#initiatePes").attr("disabled", true);
                }
                $("#savingBoardingDetailsModal .panel").html(message);
                $("#savingBoardingDetailsModal .panel")
                    .removeClass("panel-danger")
                    .removeClass("panel-warning")
                    .removeClass("panel-success");
                $("#savingBoardingDetailsModal .panel").addClass(panelclass);
                $("#editPersonModal").modal("hide");
                $("#savingBoardingDetailsModal").modal("show");
                if (typeof table != "undefined") {
                    table.ajax.reload();
                }
                if (resultObj.pesstatus == "TBD") {
                    $("#initiatePes").attr("disabled", false);
                }
                if (resultObj.employeetype == "vendor") {
                    $("#initiatePes").attr("disabled", true);
                }
            },
        });
    } else {
        $("#saveBoarding").removeClass("spinning").attr("disabled", false);
        $("#updateBoarding").removeClass("spinning").attr("disabled", false);

        console.log("invalid fields follow");
        console.log(formIbmer.find(":invalid"));
        console.log(formNotIbmer.find(":invalid"));
        console.log(formLink.find(":invalid"));
        console.log(formCommon.find(":invalid"));
    }
}

export { saveBoarding as default };