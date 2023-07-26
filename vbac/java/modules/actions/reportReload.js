/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportReload extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportReload();
    }

    listenForReportReload() {
        var $this = this;
        $(document).on("click", "#reportReload", function (e) {
            $("#portalTitle").text($this.title);
            $.fn.dataTableExt.afnFiltering.pop();
            $this.table.ajax.reload();
        });
    }
}

export { reportReload as default };