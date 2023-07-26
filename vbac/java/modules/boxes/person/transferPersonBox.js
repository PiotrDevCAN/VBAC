/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class transferPersonBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ transferPersonBox.constructor');

        super(parent);
        this.listenForBtnTransfer();
        this.listenForBtnTransferConfirmed();

        console.log('--- Function --- transferPersonBox.constructor');
    }

    listenForBtnTransfer() {
        $(document).on("click", ".btnTransfer", function (e) {
            var cnum = $(this).data("cnum");
            var notesid = $(this).data("notesid");
            var fromCnum = $(this).data("fromcnum");
            var fromNotesid = $(this).data("fromnotesid");

            $("#transferNotes_id").val(notesid);
            $("#transferCnum").val(cnum);
            $("#transferFromCnum").val(fromCnum);
            $("#transferFromNotesId").val(fromNotesid);
            $("#confirmTransferModal").modal("show");
        });
    }

    listenForBtnTransferConfirmed() {
        var $this = this;
        $(document).on("click", ".btnConfirmTransfer", function (e) {
            $(".btnConfirmTransfer").addClass("spinning");
            var formData = $("#confirmTransferForm").serialize();
            $.ajax({
                url: "ajax/transferIndividual.php",
                type: "POST",
                data: formData,
                success: function (result) {
                    $this.tableObj.table.ajax.reload();
                    $("#transferNotes_id").val("");
                    $("#transferCnum").val("");
                    $("#transferFromCnum").val("");
                    $("#transferFromNotesId").val("");
                    $(".btnConfirmTransfer").removeClass("spinning");
                    $("#confirmTransferModal").modal("hide");
                },
            });
        });
    }
}

export { transferPersonBox as default };