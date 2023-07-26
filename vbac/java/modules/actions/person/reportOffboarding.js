/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportOffboarding extends action {

    offColumns;
    offOrderingColumnIdx;

    constructor(parent) {
        super(parent);
        this.offColumns = parent.offColumns;
        this.offOrderingColumnIdx = parent.offOrderingColumnIdx;
        this.listenForReportOffboarding();
    }
    
    listenForReportOffboarding() {
        var $this = this;
        $(document).on("click", "#reportOffboarding", function (e) {
            $this.enableRemoveOffboarding();
            $("#portalTitle").text($this.title + " - Offboarding Report");
            $.fn.dataTableExt.afnFiltering.pop();
            $.fn.dataTableExt.afnFiltering.push(function (
                oSettings,
                aData,
                iDataIndex
            ) {
                var dat = new Date();
                dat.setDate(dat.getDate() + 31);

                var month = "00".concat(dat.getMonth() + 1).substr(-2);
                var day = "00".concat(dat.getDate()).substr(-2);
                var thirtyDaysHence = dat.getFullYear() + "-" + month + "-" + day;
                var dateEnd = thirtyDaysHence;
                // aData represents the table structure as an array of columns, so the script access the date value
                // in the first column of the table via aData[0]
                var projectedEndDate = aData[16];
                var revalidationStatus = aData[27];

                if (
                    projectedEndDate != "" &&
                    projectedEndDate != "2000-01-01" &&
                    projectedEndDate <= dateEnd &&
                    revalidationStatus.trim().substr(0, 10) != "preboarder" &&
                    revalidationStatus.trim().substr(0, 10) != "offboarded"
                ) {
                    return true;
                } else if (
                    revalidationStatus.trim().substr(0, 6) == "leaver" ||
                    revalidationStatus.trim().substr(0, 11) == "offboarding"
                ) {
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

export { reportOffboarding as default };