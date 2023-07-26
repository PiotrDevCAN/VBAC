/*
 *
 *
 *
 */

let saveBoardingWithCFirst = await cacheBustImport('./modules/functions/saveBoardingWithCFirst.js');
let spinner = await cacheBustImport('./modules/functions/spinner.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class editEntryWithCFirstBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editEntryWithCFirstBox.constructor');

        super(parent);
        // this.listenForEditEntry();
        this.listenForCtbRtb();
        // this.listenForAccountOrganisation();

        this.listenForSaveBoarding();

        console.log('--- Function --- editEntryWithCFirstBox.constructor');
    }

    /*
    listenForEditEntry() {
        var $this = this;
        $(document).on("click", ".btnEditEntry", function (e) {
            $("#additionalBoardingDetailsModal .modal-body").html(spinner);
            $("#additionalBoardingDetailsModal").modal("show");
            $.ajax({
                url: "ajax/getEditEntryModalBody.php",
                data: { cnum: 111 },
                type: "POST",
                success: function (result) {
                    try {
                        var resultObj = JSON.parse(result);
                        $(".employeeTypeRadioBtn input[type=radio]").removeAttr(
                            "required"
                        );
                        if (!resultObj.messages) {
                            $("#additionalBoardingDetailsModal .modal-body").html(
                                resultObj.body
                            );
                        } else {
                            $("#additionalBoardingDetailsModal .modal-body").html(
                                resultObj.messages
                            );
                        }
                    } catch (e) {
                        $("#additionalBoardingDetailsModal .modal-body").html(
                            "<h2>Json call to delete resource request Failed.Tell Piotr</h2><p>" +
                            e +
                            "</p>"
                        );
                    }
                },
            });
        });
    }
    */

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

    /*
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
    */

    listenForSaveBoarding() {
        var $this = this;
        $(document).on("click", "#saveBoarding", function () {
            $(this).attr("disabled", true);
            saveBoardingWithCFirst("Save");
        });
    }
}

export { editEntryWithCFirstBox as default };