/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporExported extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportExported();
    }

    listenForReportExported() {
        var $this = this;
        $(document).on("click", "#reportShowExported", function (e) {
            console.log("clicked on exported");
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseAssetRequestDataTable("exported");
            $("#portalTitle").text("Asset Request Portal - Exported");
        });
    }
}

export { reporExported as default };