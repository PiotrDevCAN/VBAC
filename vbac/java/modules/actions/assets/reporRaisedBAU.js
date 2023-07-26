/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporRaisedBAU extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportBauRaised();
    }

    listenForReportBauRaised() {
        var $this = this;
        $(document).on("click", "#reportShowBauRaised", function (e) {
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseAssetRequestDataTable("bauRaised");
            $("#portalTitle").text("Asset Request Portal - Raised BAU");
        });
    }
}

export { reporRaisedBAU as default };