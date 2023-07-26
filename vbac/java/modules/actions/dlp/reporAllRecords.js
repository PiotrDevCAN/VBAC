/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporAllRecords extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowDlpAll();
    }

    listenForReportShowDlpAll() {
        var $this = this;
        $(document).on("click", "#reportShowDlpAll", function (e) {
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseLicensesReport("all", "false");
            $this.tableObj.table.columns().visible(false, false);
            $this.tableObj.table.columns([0, 1, 2, 3, 8, 9, 10]).visible(true);
            $("#portalTitle").text("Licenses - All");
        });
    }
}

export { reporAllRecords as default };