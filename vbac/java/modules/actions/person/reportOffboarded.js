/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportOffboarded extends action {

    offColumns;
    offOrderingColumnIdx;

    constructor(parent) {
        super(parent);
        this.offColumns = parent.offColumns;
        this.offOrderingColumnIdx = parent.offOrderingColumnIdx;
        this.listenForReportOffboarded();
    }

    listenForReportOffboarded() {
        var $this = this;
        $(document).on("click", "#reportOffboarded", function (e) {
            $this.enableRemoveOffboarding();
            $("#portalTitle").text($this.title + " - Offboarded Report");
            $.fn.dataTableExt.afnFiltering.pop();
            $.fn.dataTableExt.afnFiltering.push(function (
                oSettings,
                aData,
                iDataIndex
            ) {
                // aData represents the table structure as an array of columns, so the script access the date value
                // in the first column of the table via aData[0]
                var revalidationStatus = aData[27];
                if (revalidationStatus.trim().substr(0, 10) == "offboarded") {
                    return true;
                } else {
                    return false;
                }
            });

            $this.table.columns().visible(false, false);
            $this.table.columns($this.offColumns).visible(true);
            $this.table.order([$this.offOrderingColumnIdx, "asc"], [5, "asc"]);

            $this.table.draw();
        });
    }
}

export { reportOffboarded as default };