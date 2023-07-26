/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportAll extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportAll();
    }

    listenForReportAll() {
        var $this = this;
        $(document).on("click", "#reportAll", function (e) {
            $("#portalTitle").text($this.title + " - All Columns");
            $this.enableRemoveOffboarding();
            $.fn.dataTableExt.afnFiltering.pop();
            $this.table.columns().visible(true);
            $this.table.columns().search("");
            $this.table.order([5, "asc"]).draw();
        });
    }
}

export { reportAll as default };