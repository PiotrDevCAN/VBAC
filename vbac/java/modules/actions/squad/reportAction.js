/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportAction extends action {

  visibleColumns;

  constructor(parent) {
    super(parent);
    this.visibleColumns = parent.visibleColumns;
    this.listenForReportAction();
  }

  listenForReportAction() {
    var $this = this;
    $(document).on("click", "#reportAction", function (e) {
      $("#portalTitle").text($this.title + " - Action Mode");
      $.fn.dataTableExt.afnFiltering.pop();
      $this.tableObj.table.columns().visible(false, false);
      $this.tableObj.table.columns($this.visibleColumns).visible(true);
      $this.tableObj.table.order([5, "asc"]).draw();
    });
  }
}

export { reportAction as default };