/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporShowUID extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowUid();
    }

    listenForReportShowUid() {
        var $this = this;
        $(document).on("click", "#reportShowUid", function (e) {
            $("#portalTitle").text("Asset Request Portal - Show UID");
            $.fn.dataTableExt.afnFiltering.pop();

            $this.tableObj.table.destroy();
            $this.tableObj.initialiseAssetRequestDataTable("all");

            $this.tableObj.table.columns().visible(false, false);
            $this.tableObj.table.columns([0, 1, 2, 3, 10, 11]).visible(true);
            $this.tableObj.table.search("").order([0, "desc"]).draw();
        });
    }
}

export { reporShowUID as default };