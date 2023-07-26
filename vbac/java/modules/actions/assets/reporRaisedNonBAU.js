/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reporRaisedNonBAU extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportNonBauRaised();
    }

    listenForReportNonBauRaised() {
        var $this = this;
        $(document).on("click", "#reportShowNonBauRaised", function (e) {
            $this.tableObj.table.destroy();
            $this.tableObj.initialiseAssetRequestDataTable("nonBauRaised");
            $("#portalTitle").text("Asset Request Portal - Raised Non-BAU");
        });
    }
}

export { reporRaisedNonBAU as default };