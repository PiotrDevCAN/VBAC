/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class exportBauForOrderIt extends action {

    constructor(parent) {
        super(parent);
        this.listenForExportBauButton();
    }

    listenForExportBauButton() {
        var $this = this;
        $(document).on("click", "#exportBauForOrderIt", function (e) {
            $("#exportBauForOrderIt").addClass("spinning");
            $("#exportBauForOrderIt").attr("disabled", true);
            $.ajax({
                url: "ajax/exportForOrderIt.php",
                type: "GET",
                data: { bau: true },
                success: function (result) {
                    console.log(result);
                    var resultObj = JSON.parse(result);
                    $this.table.ajax.reload();
                    $("#exportResultsModal .modal-body").html(resultObj.messages);
                    $("#exportResultsModal").modal("show");
                    $("#exportBauForOrderIt").removeClass("spinning");
                    $("#exportBauForOrderIt").attr("disabled", false);
                    $this.parent.parent.countRequestsForPortal();
                },
            });
        });
    }
}

export { exportBauForOrderIt as default };