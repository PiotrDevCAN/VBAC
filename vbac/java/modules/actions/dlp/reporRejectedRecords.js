/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporRejectedRecords extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowDlpRejected();
    }

    listenForReportShowDlpRejected() {
        var $this = this;
        $(document).on("click", "#reportShowDlpRejected", function (e) {
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseLicensesReport("rejected", "true");
            $this.tableObj.table.columns().visible(false, false);
            $this.tableObj.table.columns([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]).visible(true);
            $("#portalTitle").text("Licenses - Rejected");
        });
    }
}

export { reporRejectedRecords as default };