/*
 *
 *
 *
 */

class personcFirst {

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
        url: "ajax/populatePersoncFirstDatatable.php",
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
        { data: "API_REFERENCE_CODE", defaultContent: "" },
        { data: "UNIQUE_REFERENCE_NO", defaultContent: "" },
        { data: "PROFILE_ID", defaultContent: "" },
        { data: "CANDIDATE_ID", defaultContent: "" },
        { data: "STATUS", defaultContent: "" },
        { data: "EMAIL_ADDRESS", defaultContent: "" },
        { data: "FIRST_NAME", defaultContent: "" },
        { data: "MIDDLE_NAME", defaultContent: "" },
        { data: "LAST_NAME", defaultContent: "" },
        { data: "PHONE", defaultContent: "" },
        { data: "ADDED_ON_DATE", defaultContent: "" },
        { data: "INFO_RECEIVED_ON_DATE", defaultContent: "" },
        { data: "INFO_REQUESTED_ON_DATE", defaultContent: "" },
        { data: "INVITED_ON_DATE", defaultContent: "" },
        { data: "SUBMITTED_ON_DATE", defaultContent: "" },
        { data: "COMPLETED_ON_DATE", defaultContent: "" },
      ],
      order: [[0, "asc"]],
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

export { personcFirst as default };