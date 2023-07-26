/*
 *
 *
 *
 */

let buttonCommon = await cacheBustImport('./modules/functions/buttonCommon.js');

class workLocation {

  table;

  constructor() {
    this.initialiseWorkLocationTable();
  }

  initialiseWorkLocationTable() {
    // Setup - add a text input to each footer cell
    $("#workLocationTable tfoot th").each(function () {
      var title = $(this).text();
      $(this).html(
        '<input type="text" id="footer' +
        title +
        '" placeholder="Search ' +
        title +
        '" />'
      );
    });
    // DataTable
    this.table = $("#workLocationTable").DataTable({
      ajax: {
        url: "ajax/populateWorkLocationTable.php",
        data: function (d) { },
        type: "POST",
      },
      columns: [
        { data: "COUNTRY", render: { _: "display", sort: "sort" } },
        { data: "CITY" },
        { data: "ADDRESS" },
        { data: "ONSHORE" },
        { data: "CBC_IN_PLACE" },
      ],
      order: [[1, "asc"]],
      responsive: true,
      processing: true,
      dom: "Blfrtip",
      buttons: [
        "colvis",
        $.extend(true, {}, buttonCommon, {
          extend: "excelHtml5",
          exportOptions: {
            orthogonal: "sort",
            stripHtml: true,
            stripNewLines: false,
          },
          customize: function (xlsx) {
            var sheet = xlsx.xl.worksheets["sheet1.xml"];
            var now = new Date();
            $("c[r=A1] t", sheet).text("Ventus Tribes : " + now);
          },
        }),
        $.extend(true, {}, buttonCommon, {
          extend: "csvHtml5",
          exportOptions: {
            orthogonal: "sort",
            stripHtml: true,
            stripNewLines: false,
          },
        }),
        $.extend(true, {}, buttonCommon, {
          extend: "print",
          exportOptions: {
            orthogonal: "sort",
            stripHtml: true,
            stripNewLines: false,
          },
        }),
      ],
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

export { workLocation as default };