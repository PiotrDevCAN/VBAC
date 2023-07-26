/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class exportForOrderIt extends action {

    constructor(parent) {
        super(parent);
        this.listenForExportButton();
    }

    listenForExportButton() {
        var $this = this;
        $(document).on("click", "#exportForOrderIt", function (e) {
            $("#exportForOrderIt").addClass("spinning");
            $("#exportForOrderIt").attr("disabled", true);
            $.ajax({
                url: "ajax/exportForOrderIt.php",
                type: "GET",
                success: function (result) {
                    console.log(result);
                    var resultObj = JSON.parse(result);
                    $this.table.ajax.reload();
                    $("#exportResultsModal .modal-body").html(resultObj.messages);
                    $("#exportResultsModal").modal("show");
                    $("#exportForOrderIt").removeClass("spinning");
                    $("#exportForOrderIt").attr("disabled", false);
                    $this.parent.parent.countRequestsForPortal();
                },
            });
        });
    }
}

export { exportForOrderIt as default };