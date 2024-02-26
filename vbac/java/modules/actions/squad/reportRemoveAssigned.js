/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportRemoveAssigned extends action {

    statusColumnIdx;

    constructor(parent) {
        super(parent);
        this.statusColumnIdx = parent.statusColumnIdx;
        this.listenForReportRemoveAssigned();
    }

    listenForReportRemoveAssigned() {
        var $this = this;
        $(document).on("click", "#reportRemoveAssigned", function (e) {
            $("#portalTitle").text($this.title + " - Assigned hidden");
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                return data[$this.statusColumnIdx].trim().substring(0, 3) == "Not";
            });
            $this.table.draw();
        });
    }
}

export { reportRemoveAssigned as default };