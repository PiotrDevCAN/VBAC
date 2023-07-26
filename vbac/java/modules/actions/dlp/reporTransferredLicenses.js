/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporTransferredLicenses extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowDlpTransferred();
    }

    listenForReportShowDlpTransferred() {
        var $this = this;
        $(document).on("click", "#reportShowDlpTransferred", function (e) {
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseLicensesReport("transferred", "false");
            $this.tableObj.table.columns().visible(false, false);
            $this.tableObj.table.columns([0, 1, 2, 3, 8, 9, 10]).visible(true);
            $("#portalTitle").text("Licenses - Transferred");
        });
    }
}

export { reporTransferredLicenses as default };