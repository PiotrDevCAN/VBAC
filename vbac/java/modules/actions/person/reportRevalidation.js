/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportRevalidation extends action {

    revColumns;

    constructor(parent) {
        super(parent);
        this.revColumns = parent.revColumns;
        this.listenForReportRevalidation();
    }

    listenForReportRevalidation() {
        var $this = this;
        $(document).on("click", "#reportRevalidation", function (e) {
            $this.enableRemoveOffboarding();
            $("#portalTitle").text($this.title + " - Revalidation Report");
            $.fn.dataTableExt.afnFiltering.pop();
            $this.table.columns().visible(false, false);
            $this.table.columns(this.revColumns).visible(true);
            $this.table.search("").order([5, "asc"]).draw();
        });
    }
}

export { reportRevalidation as default };