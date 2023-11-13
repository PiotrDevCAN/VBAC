/*
 *
 *
 *
 */

class dlp {

  table;

  constructor() {
    this.initialiseLicensesReport();
  }

  initialiseLicensesReport(showType, withButtons) {
    showType = typeof showType == "undefined" ? "active" : showType;
    withButtons = typeof withButtons == "undefined" ? "true" : withButtons;
    // Setup - add a text input to each footer cell
    $("#dlpLicensesTable tfoot th").each(function () {
      var title = $(this).text();
      $(this).html(
        title +
        '<br/><input type="text" id="footer' +
        title +
        '" placeholder="Search ' +
        title +
        '" />'
      );
    });
    // DataTable
    this.table = $("#dlpLicensesTable").DataTable({
      ajax: {
        url: "ajax/populateDlpLicenseReport.php",
        type: "POST",
        data: {
          showType: showType,
          withButtons: withButtons
        },
        beforeSend: function (jqXHR, settings) {
          $.each(xhrPool, function (idx, jqXHR) {
            console.log('abort jqXHR');
            jqXHR.abort();  // basically, cancel any existing request, so this one is the only one running
            xhrPool.splice(idx, 1);
          });
          xhrPool.push(jqXHR);
        }
      },
      columns: [
        { data: "CNUM", defaultContent: "" },
        { data: "LICENSEE", defaultContent: "" },
        { data: "HOSTNAME", defaultContent: "" },
        { data: "APPROVER", defaultContent: "" },
        { data: "APPROVED", defaultContent: "" },
        { data: "FM", defaultContent: "" },
        { data: "CREATED", defaultContent: "" },
        { data: "CODE", defaultContent: "" },
        { data: "OLD_HOSTNAME", defaultContent: "" },
        { data: "TRANSFERRED", defaultContent: "" },
        { data: "TRANSFERRER", defaultContent: "" },
        { data: "STATUS", defaultContent: "" },
      ],
      autoWidth: true,
      deferRender: true,
      responsive: true,
      processing: true,
      language: {
        emptyTable: "No License Details to show",
      },
      dom: "Blfrtip",
      buttons: ["colvis", "excelHtml5", "csvHtml5", "print"],
    });
    // Apply the search
    this.table.columns().every(function () {
      var that = this;
      $("input", this.footer()).on("keyup change", function () {
        if (that.search() !== this.value) {
          that.search(this.value).draw();
        }
      });
    });
  }
}

export { dlp as default };