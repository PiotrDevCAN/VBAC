function saveVendorBoarding(mode, form, saveButton, initiatePesButton, table) {

    saveButton.addClass("spinning");

    var formValid = form[0].checkValidity();

    var $this = this;
    if (formValid) {
        var boardingFormEnabledInputs = $("input:enabled");
        var allDisabledFields = $("input:disabled").not("#saveVendorBoarding");
        $(allDisabledFields).attr("disabled", false);
        var formData = form.serialize();

        formData += "&mode=" + mode;

        $(allDisabledFields).attr("disabled", true);
        $.ajax({
            url: "ajax/saveBoardingVendorForm.php",
            type: "POST",
            data: formData,
            success: function (result) {
                saveButton.removeClass("spinning");
                var resultObj = JSON.parse(result);
                var message = "";
                var panelclass = "panel-success";
                if (resultObj.success == true) {
                    $("#resource_uid").val(resultObj.cnum);
                    message += "<div class=panel-heading><h3 class=panel-title>Success</h3>" + resultObj.messages;
                    form.find(':input').attr("disabled", true);
                    saveButton.attr("disabled", true);
                } else {
                    message += "<div class=panel-heading><h3 class=panel-title>Error : Please inform vBAC Support</h3>" + resultObj.messages;
                    panelclass = "panel-danger";
                    saveButton.attr("disabled", false);
                }
                $("#savingBoardingDetailsModal .panel").html(message);
                $("#savingBoardingDetailsModal .panel")
                    .removeClass("panel-danger")
                    .removeClass("panel-warning")
                    .removeClass("panel-success");
                $("#savingBoardingDetailsModal .panel").addClass(panelclass);
                $("#editPersonModal").modal("hide");
                $("#savingBoardingDetailsModal").modal("show");

                if (initiatePesButton !== null) {
                    if (resultObj.allowPESInitalise == true) {
                        initiatePesButton.attr("disabled", false);
                    } else {
                        initiatePesButton.attr("disabled", true);
                    }
                }

                if (typeof table != "undefined") {
                    table.ajax.reload();
                }
            },
        });
    } else {
        saveButton.removeClass("spinning").attr("disabled", false);
    }
}

export { saveVendorBoarding as default };