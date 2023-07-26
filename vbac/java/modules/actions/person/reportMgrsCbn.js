/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportMgrsCbn extends action {

    mgrsColumns;

    constructor(parent) {
        super(parent);
        this.mgrsColumns = parent.mgrsColumns;
        this.listenForReportMgrsCbn();
    }

    listenForReportMgrsCbn() {
        var $this = this;
        $(document).on("click", "#reportMgrsCbn", function (e) {
            $this.enableRemoveOffboarding();
            $("#portalTitle").text($this.title + " - Managers CBN Report");
            $.fn.dataTableExt.afnFiltering.pop();
            $this.table.columns().visible(false, false);
            $this.table.columns($this.mgrsColumns).visible(true);
            $this.table.search("").order([5, "asc"]).draw();
        });
    }
}

export { reportMgrsCbn as default };