/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporRequiresApproval extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowDlpPending();
    }

    listenForReportShowDlpPending() {
        var $this = this;
        $(document).on("click", "#reportShowDlpPending", function (e) {
            console.log("show pending");
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseLicensesReport("pending", "true");
            $this.tableObj.table.columns().visible(false, false);
            $this.tableObj.table.columns([0, 1, 2, 3, 4, 5]).visible(true);
            $("#portalTitle").text("Licenses - Pending");
        });
    }
}

export { reporRequiresApproval as default };