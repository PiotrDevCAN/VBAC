/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportPes extends action {

    PESColumns;

    constructor(parent) {
        super(parent);
        this.PESColumns = parent.PESColumns;
        this.listenForReportPes();
    }

    listenForReportPes() {
        var $this = this;
        $(document).on("click", "#reportPes", function (e) {
            $this.enableRemoveOffboarding();
            $("#portalTitle").text($this.title + " - PES Report");
            $.fn.dataTableExt.afnFiltering.pop();
            $this.table.columns().visible(false, false);
            $this.table.columns($this.PESColumns).visible(true);
            $this.table.order([18, "desc"], [5, "asc"]).draw();
        });
    }
}

export { reportPes as default };