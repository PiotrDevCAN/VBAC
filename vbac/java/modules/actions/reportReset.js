/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportReset extends action {

  resetColumns;

  constructor(parent) {
    super(parent);
    this.resetColumns = parent.resetColumns;
    this.listenForReportReset();
  }

  listenForReportReset() {
    var $this = this;
    $(document).on("click", "#reportReset", function (e) {
      $("#portalTitle").text($this.title);
      $this.enableRemoveOffboarding();
      $.fn.dataTableExt.afnFiltering.pop();
      $this.table.columns().visible(false, false);
      $this.table.columns($this.resetColumns).visible(true);
      $this.table.search("").order([5, "asc"]).draw();
      // $this.table.search("").order([0, "asc"]).draw();
    });
  }
}

export { reportReset as default };