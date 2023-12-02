/*
 *
 *
 *
 */

let buttonCommon = await cacheBustImport('./modules/functions/buttonCommon.js');

class bandMapping {

  table;

  constructor() {
    this.initialiseBandMappingTable();
  }

  initialiseBandMappingTable() {
    // Setup - add a text input to each footer cell
    $("#bandMappingTable tfoot th").each(function () {
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
    this.table = $("#bandMappingTable").DataTable({
      ajax: {
        url: "ajax/populateBandMappingTable.php",
        data: function (d) { },
        type: "POST",
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
        { data: "BUSINESS_TITLE", render: { _: "display", sort: "sort" } },
        { data: "BAND" },
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

export { bandMapping as default };