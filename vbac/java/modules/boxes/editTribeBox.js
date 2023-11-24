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

        console.log('--- Function --- editTribeBox.constructor');
    }

    listenForEditTribe() {
        var $this = this;
        $(document).on("click", ".btnEditTribe", function () {
            $("#TRIBE_NUMBER")
                .val($(this).data("tribenumber"))
                .trigger("change")
                .attr("disabled", true);
            if ($(this).data("organisation") == "Managed Services") {
                $("#radioTribeOrganisationManaged").prop("checked", true);
            } else {
                $("#radioTribeOrganisationProject").prop("checked", true);
            }
            $("#TRIBE_NAME").val($(this).data("tribename"));
            $("#TRIBE_LEADER").typeahead('val', $(this).data("tribeleader"));
            $("#ITERATION_MGR").typeahead('val', $(this).data("iterationmgr"));
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
                    $("#TRIBE_LEADER").typeahead('val', '');
                    $("#ITERATION_MGR").typeahead('val', '');
                    $("#radioTribeOrganisationManaged").prop("checked", true);
                    $this.tableObj.table.ajax.reload();
                }
            });
        });
    }
}

export { editTribeBox as default };