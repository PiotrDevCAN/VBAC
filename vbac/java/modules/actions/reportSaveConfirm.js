/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportSaveConfirm extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportSaveConfirm();
    }

    listenForReportSaveConfirm() {
        $(document).on("click", "#reportSaveConfirm", function (e) {
            $("#saveReportModal").modal("hide");
            var form = $("#reportSaveForm").serialize();
        });
    }
}

export { reportSaveConfirm as default };