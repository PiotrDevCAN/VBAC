/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportAll extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowAll();
    }

    listenForReportShowAll() {
        var $this = this;
        $(document).on("click", "#reportShowAll", function (e) {
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseAssetRequestDataTable("all");
            $("#portalTitle").text("Asset Request Portal - Show All");
        });
    }
}

export { reportAll as default };