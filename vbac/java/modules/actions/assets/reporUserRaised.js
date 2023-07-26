/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporUserRaised extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportShowUserRaised();
    }

    listenForReportShowUserRaised() {
        var $this = this;
        $(document).on("click", "#reportShowUserRaised", function (e) {
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseAssetRequestDataTable("exportable", false);
            $("#portalTitle").text(
                "Asset Request Portal - Show User Raised Requests"
            );
        });
    }
}

export { reporUserRaised as default };