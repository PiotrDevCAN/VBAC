/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class exportNonBauForOrderIt extends action {

    constructor(parent) {
        super(parent);
        this.listenForExportNonBauButton();
    }

    listenForExportNonBauButton() {
        var $this = this;
        $(document).on("click", "#exportNonBauForOrderIt", function (e) {
            $("#exportNonBauForOrderIt").addClass("spinning");
            $("#exportNonBauForOrderIt").attr("disabled", true);
            $.ajax({
                url: "ajax/exportForOrderIt.php",
                type: "GET",
                data: { bau: false },
                success: function (result) {
                    console.log(result);
                    var resultObj = JSON.parse(result);
                    $this.table.ajax.reload();
                    $("#exportResultsModal .modal-body").html(resultObj.messages);
                    $("#exportResultsModal").modal("show");
                    $("#exportNonBauForOrderIt").removeClass("spinning");
                    $("#exportNonBauForOrderIt").attr("disabled", false);
                    $this.parent.parent.countRequestsForPortal();
                },
            });
        });
    }
}

export { exportNonBauForOrderIt as default };