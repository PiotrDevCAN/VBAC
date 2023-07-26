/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class addToJustificationBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ addToJustificationBox.constructor');

        super(parent);
        this.listenForAddToJustification();
        this.listenForSaveAmendedJustification();

        console.log('--- Function --- addToJustificationBox.constructor');
    }

    listenForAddToJustification() {
        $(document).on("click", ".btnAddToJustification", function (e) {
            console.log($(this));

            var reference = $(this).data("reference");
            var requestee = $(this).data("requestee");
            var asset = $(this).data("asset");
            var status = $(this).data("status");
            $.ajax({
                url: "ajax/getDetailsForEditJustification.php",
                type: "GET",
                data: { reference: reference },
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    console.log(resultObj);
                    console.log($(this));
                    console.log(reference);

                    $("#editJustificationRequestReference").html(reference);
                    $("#editJustificationRequestee").html(requestee);
                    $("#editJustificationAssetTitle").html(asset);
                    $("#editJustificationStatus").val(status);

                    $("#editJustificationJustification").val(resultObj.justification);
                    $("#editJustificationComment").html(resultObj.comment);
                    $("#justificationEditModal").modal("show");
                },
            });
        });
    }

    listenForSaveAmendedJustification() {
        var $this = this;
        $(document).on("click", "#editJustificationConfirm", function (e) {
            var form = document.getElementById("editJustificationForm");
            var formValid = form.checkValidity();
            if (formValid) {
                $("#editJustificationConfirm").addClass("spinning");
                var reference = $("#editJustificationRequestReference").text();
                var justification = $("#editJustificationJustification").val();
                var status = $("#editJustificationStatus").val();

                console.log(reference);
                console.log(justification);
                console.log(status);

                $.ajax({
                    url: "ajax/saveAmendedJustification.php",
                    type: "POST",
                    data: {
                        reference: reference,
                        justification: justification,
                        status: status,
                    },
                    success: function (result) {
                        var resultObj = JSON.parse(result);
                        $("#editJustificationConfirm").removeClass("spinning");
                        $("#justificationEditModal").modal("hide");
                        $this.tableObj.table.ajax.reload();
                    },
                });
            }
        });
    }
}

export { addToJustificationBox as default };