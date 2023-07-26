/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class editTribeBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ editTribeBox.constructor');

        super(parent);
        this.listenForEditTribe();
        this.listenForSubmitTribeForm();
        this.listenForLeader();

        console.log('--- Function --- editTribeBox.constructor');
    }

    listenForEditTribe() {
        $(document).on("click", ".btnEditTribe", function () {
            $("#TRIBE_NUMBER")
                .val($(this).data("tribenumber"))
                .trigger("change")
                .attr("disabled", true);
            $("#TRIBE_NAME").val($(this).data("tribename"));
            $("#TRIBE_LEADER").val($(this).data("tribeleader"));
            $("#ITERATION_MGR").val($(this).data("iterationmgr"));

            console.log($(this));
            console.log($(this).data("organisation"));

            if ($(this).data("organisation") == "Managed Services") {
                $("#radioTribeOrganisationManaged").prop("checked", true);
            } else {
                $("#radioTribeOrganisationProject").prop("checked", true);
            }
            $("#mode").val("edit");
        });
    }

    listenForSubmitTribeForm() {
        var $this = this;
        $(document).on('submit', '#tribeForm', function (event) {
            console.log("submit clicked");
            event.preventDefault();
            $(":submit").addClass("spinning").attr("disabled", true);
            var disabledFields = $(":disabled");
            $(disabledFields).attr("disabled", false);
            var formData = $("#tribeForm").serialize();
            var verData = $("#version").prop("checked")
                ? "&version=Original"
                : "&version=New";

            console.log(formData);
            console.log(verData);

            $(disabledFields).attr("disabled", true);
            $.ajax({
                type: "post",
                url: "ajax/saveAgileTribeRecord.php",
                data: formData + verData,
                success: function (response) {
                    var responseObj = JSON.parse(response);
                    console.log(responseObj);
                    if (responseObj.success) {
                        $('#messageModalBody').html("<p>Tribe Record Saved</p>");
                        $('#messageModal').modal('show');
                    } else {
                        $('#messageModalBody').html(
                            "<p>Save has encountered a problem</p><p>" +
                            responseObj.messages +
                            "</p>"
                        );
                        $('#messageModal').modal('show');
                    }
                    $(".spinning").removeClass("spinning").attr("disabled", false);
                    $("#TRIBE_NUMBER")
                        .val("")
                        .trigger("change")
                        .attr("disabled", false);
                    $("#TRIBE_NAME").val("");
                    $("#TRIBE_LEADER").val("");
                    $("#ITERATION_MGR").val("");
                    $("#radioTribeOrganisationManaged").prop("checked", true);
                    $this.tableObj.table.ajax.reload();
                }
            });
        });
    }

    listenForLeader() {
        $(".typeahead").bind("typeahead:select", function (ev, suggestion) {
            $(".tt-menu").hide();
            $("#TRIBE_LEADER").val(suggestion.notesEmail);
        });
    }
}

export { editTribeBox as default };