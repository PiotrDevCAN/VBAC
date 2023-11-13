/*
 *
 *
 *
 */

let buttonCommon = await cacheBustImport('./modules/functions/buttonCommon.js');

class squadALog {

  table;

  constructor(preBoardersAction) {
    this.initialiseSquadALog(preBoardersAction);
  }

  initialiseSquadALog(preBoardersAction) {
    preBoardersAction =
      typeof preBoardersAction == "undefined" ? null : preBoardersAction;
    // Setup - add a text input to each footer cell
    $("#squadalog tfoot th").each(function () {
      var title = $(this).text();
      var titleCondensed = title.replace(" ", "");
      $(this).html(
        '<input type="text" id="footer' +
        titleCondensed +
        '" placeholder="Search ' +
        title +
        '" />'
      );
    });
    // Show DataTable
    $('#squadalog').show();
    // DataTable
    this.table = $("#squadalog").DataTable({
      ajax: {
        url: "ajax/populateSquadALog.php",
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

      // <th>CNUM</th><th>Notes Id</th><th>JRSS</th><th>Squad Type</th>
      // <th>Tribe</th><th>Shift</th><th>Squad Leader</th><th>FLL</th><th>SLL</th><th>Squad Number</th>

      columns: [
        {
          data: "CNUM",
          defaultContent: "",
          render: { _: "display", sort: "sort" },
        },
        {
          data: "NOTES_ID",
          defaultContent: "",
          render: { _: "display", sort: "sort" },
        },
        { data: "JRSS", defaultContent: "<i>unknown</i>" },
        { data: "SQUAD_TYPE", defaultContent: "<i>unknown</i>" },
        {
          data: "TRIBE",
          defaultContent: "<i>unknown</i>",
          render: { _: "display", sort: "sort" },
        },
        { data: "TRIBE_NAME", defaultContent: "<i>unknown</i>" },
        { data: "SHIFT", defaultContent: "<i>unknown</i>" },
        { data: "ITERATION_MGR", defaultContent: "<i>unknown</i>" },
        { data: "SQUAD_LEADER", defaultContent: "<i>unknown</i>" },
        { data: "FLL_CNUM", defaultContent: "" },
        {
          data: "FLL",
          defaultContent: "",
          render: { _: "display", sort: "sort" },
        },
        { data: "SLL_CNUM", defaultContent: "" },
        {
          data: "SLL",
          defaultContent: "",
          render: { _: "display", sort: "sort" },
        },
        {
          data: "SQUAD",
          defaultContent: "",
          render: { _: "display", sort: "sort" },
        },
        { data: "SQUAD_NAME", defaultContent: "" },
      ],
      columnDefs: [
        { visible: false, targets: [0, 5, 6, 9, 11, 14] },
        { visible: true, targets: [1, 2, 3, 4, 7, 8, 10, 12, 13] },
      ],
      order: [[1, "asc"]],
      autoWidth: true,
      deferRender: true,
      processing: true,
      responsive: true,
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

export { squadALog as default };