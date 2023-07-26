/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporAwaitingIAM extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowAwaitingIam();
    }

    listenForReportShowAwaitingIam() {
        var $this = this;
        $(document).on("click", "#reportShowAwaitingIam", function (e) {
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseAssetRequestDataTable("awaitingIam", true);
            $("#portalTitle").text("Asset Request Portal - Awaiting IAM");
        });
    }
}

export { reporAwaitingIAM as default };