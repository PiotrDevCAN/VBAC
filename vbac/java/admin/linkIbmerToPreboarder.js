/*
 *
 *
 *
 */

let formatKyndrylPerson = await cacheBustImport('./modules/functions/formatKyndrylPerson.js');

class linkIbmerToPreboarder {

    constructor() {
        this.initialiseLinkingFormSelect2();
        this.listenForRegularPerson();
        this.listenForPreboardedPerson();
        this.listenForSaveLinking();
    }

    initialiseLinkingFormSelect2() {
        $('#ibmer_preboarded').select2({
            templateResult: formatKyndrylPerson
        });
        $('#person_preboarded').select2();
    }

    listenForRegularPerson() {
        $(document).on("select2:select", "#ibmer_preboarded", function (e) {
            var data = e.params.data;
            var $el = $(data.element);
            var $data = $el.data();
            $('#cnum').val($data.cnum);
            $('#workerid').val($data.workerid);
        });
    }

    listenForPreboardedPerson() {
        $(document).on("select2:select", "#person_preboarded", function (e) {
            var data = e.params.data;
            var $el = $(data.element);
            var $data = $el.data();
            $('#preboarderCnum').val($data.cnum);
            $('#preboarderWorkerId').val($data.workerid);
        });
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
                        $("#preboarderCnum").val("");
                        $("#preboarderWorkerId").val("");
                        $("#cnum").val("");
                        $("#workerid").val("");
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