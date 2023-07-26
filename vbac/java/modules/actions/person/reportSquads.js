/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportSquads extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportSquads();
    }

    listenForReportSquads() {
        var $this = this;
        $(document).on("click", "#reportSquads", function (e) {
            $("#portalTitle").text($this.title + " - Squad Details");
            $.fn.dataTableExt.afnFiltering.pop();
            $this.table.columns().visible(false, false);
            $this.table
                .columns([5, 8, 40, 41, 43, 44, 45, 46])
                .visible(true);
            $this.table.search("").order([5, "asc"]).draw();
        });
    }
}

export { reportSquads as default };