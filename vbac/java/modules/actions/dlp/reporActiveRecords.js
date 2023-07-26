/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporActiveRecords extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowDlpActive();
    }

    listenForReportShowDlpActive() {
        var $this = this;
        $(document).on("click", "#reportShowDlpActive", function (e) {
            console.log("show active");
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseLicensesReport("active", "false");
            $this.tableObj.table.columns().visible(false, false);
            $this.tableObj.table.columns([0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10]).visible(true);
            $("#portalTitle").text("Licenses - Active");
        });
    }
}

export { reporActiveRecords as default };