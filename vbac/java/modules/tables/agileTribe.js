/*
 *
 *
 *
 */

let buttonCommon = await cacheBustImport('./modules/functions/buttonCommon.js');

class agileTribe {

  table;

  constructor(version) {
    this.initialiseAgileTribeTable(version);
  }

  initialiseAgileTribeTable(version) {
    // Setup - add a text input to each footer cell
    $("#tribeTable tfoot th").each(function () {
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
    $('#tribeTable').show();
    // DataTable
    this.table = $("#tribeTable").DataTable({
      ajax: {
        url: "ajax/populateAgileTribeTable.php",
        data: function (d) {
          var version = $("#version").prop("checked") ? "Original" : "New";
          d.version = version;
        },
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
        { data: "TRIBE_NUMBER", render: { _: "display", sort: "sort" } },
        { data: "TRIBE_NAME" },
        { data: "TRIBE_LEADER" },
        { data: "ITERATION_MGR", defaultContent: "<i>To be assigned</i>" },
        { data: "ORGANISATION" },
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

export { agileTribe as default };