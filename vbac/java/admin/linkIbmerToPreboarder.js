/*
 *
 *
 *
 */

class linkIbmerToPreboarder {

    constructor() {
        this.initialiseLinkingFormSelect2();
        this.listenForSaveLinking();
    }

    initialiseLinkingFormSelect2() {
        $('#ibmer_preboarded').select2();
        $('#person_preboarded').select2();
    }

    listenForSaveLinking() {
        var $this = this;
        $(document).on("click", "#saveLinking", function () {
            $("#saveLinking").addClass("spinning");
            var form = $("#linkingForm");
            var formValid = form[0].checkValidity();
            if (formValid) {
                var boardingFormEnabledInputs = $("input:enabled");
                var allDisabledFields = $("input:disabled");
                $(allDisabledFields).attr("disabled", false);
                var formData = form.serialize();
                $(allDisabledFields).attr("disabled", true);
                $.ajax({
                    url: "ajax/saveLinking.php",
                    type: "POST",
                    data: formData,
                    success: function (result) {
                        $("#saveLinking").removeClass("spinning");
                        var resultObj = JSON.parse(result);
                        $("#ibmer_preboarded").val("");
                        $("#person_preboarded").val("");
                    },
                });
            } else {
                $("#saveLinking").removeClass("spinning");
                console.log("invalid fields follow");
                console.log($(form).find(":invalid"));
            }
        });
    }
}

const LinkIbmerToPreboarder = new linkIbmerToPreboarder();

export { LinkIbmerToPreboarder as default };