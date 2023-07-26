/*
 *
 *
 *
 */

class personFinder {

  table;

  constructor() {
    this.initialisePersonFinderDataTable();
  }

  initialisePersonFinderDataTable() {
    // Setup - add a text input to each footer cell
    $("#personFinderTable tfoot th").each(function () {
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
    $('#personFinderTable').show();
    // DataTable
    this.table = $("#personFinderTable").DataTable({
      ajax: {
        url: "ajax/populatePersonFinderDatatable.php",
        type: "GET",
      },
      columns: [
        { data: "CNUM", defaultContent: "" },
        { data: "FIRST_NAME", defaultContent: "<i>unknown</i>" },
        { data: "LAST_NAME", defaultContent: "<i>unknown</i>" },
        { data: "EMAIL_ADDRESS", defaultContent: "<i>unknown</i>" },
        { data: "KYN_EMAIL_ADDRESS", defaultContent: "<i>unknown</i>" },
        { data: "NOTES_ID", defaultContent: "<i>unknown</i>" },
        { data: "FM_CNUM", defaultContent: "" },
      ],
      order: [[4, "asc"]],
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

export { personFinder as default };