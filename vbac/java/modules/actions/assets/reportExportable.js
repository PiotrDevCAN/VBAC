/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportExportable extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowExportable();
    }

    listenForReportShowExportable() {
        var $this = this;
        $(document).on("click", "#reportShowExportable", function (e) {
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseAssetRequestDataTable("exportable", true);
            $("#portalTitle").text(
                "Asset Request Portal - Show Pmo To Raise Requests"
            );
        });
    }
}

export { reportExportable as default };