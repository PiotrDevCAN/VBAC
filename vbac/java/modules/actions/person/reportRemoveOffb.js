/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportRemoveOffb extends action {

    statusColumnIdx;

    constructor(parent) {
        super(parent);
        this.statusColumnIdx = parent.statusColumnIdx;
        this.listenForReportRemoveOffb();
    }

    listenForReportRemoveOffb() {
        var $this = this;
        $(document).on("click", "#reportRemoveOffb", function (e) {
            $this.disableRemoveOffboarding();
            $("#portalTitle").html(
                $("#portalTitle").text() +
                "<span style='color:red;font-size:14px'><br/>Offboarding & Offboarded hidden</span>"
            );
            $.fn.dataTable.ext.search.push(function (settings, data, dataIndex) {
                return data[$this.statusColumnIdx].trim().substring(0, 3) != "off";
            });
            $this.table.draw();            
        });
    }
}

export { reportRemoveOffb as default };