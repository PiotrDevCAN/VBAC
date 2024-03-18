/*
 *
 *
 *
 */

class squadAssignment {

  table;

  constructor() {
    this.initialiseDataTable();
  }

  initialiseDataTable() {
    // Setup - add a text input to each footer cell
    $("#personTable tfoot th").each(function () {
      var title = $(this).text();
      $(this).html(
        '<input type="text" id="footer' +
        title +
        '" placeholder="Search ' +
        title +
        '" />'
      );
    });
    // Show DataTable
    $('#personTable').show();
    // DataTable
    this.table = $("#personTable").DataTable({
      ajax: {
        url: "ajax/populateSquadsAssignment.php",
        type: "GET",
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
        { data: "ID", defaultContent: "" },
        { data: "CNUM", defaultContent: "" },
        { data: "WORKER_ID", defaultContent: "" },
        { data: "EMAIL_ADDRESS", defaultContent: "" },
        { data: "KYN_EMAIL_ADDRESS", defaultContent: "" },
        { data: "SQUAD_NAME", render: { _: "display", sort: "sort" } },
        { data: "TRIBE_NAME", defaultContent: "" },
        { data: "TYPE", defaultContent: "" }
      ],
      columnDefs: [
        {
          visible: false,
          targets: [
            0, 2, 4
          ],
        },
      ],
      order: [[3, "desc"]],
      processing: true,
      responsive: true,
      paging: true,
      pagingType: 'full_numbers',
      pageLength: 100,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
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

export { squadAssignment as default };