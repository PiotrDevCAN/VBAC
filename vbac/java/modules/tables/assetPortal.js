/*
 *
 *
 *
 */

class assetPortal {

  table;

  constructor() {
    this.initialiseAssetRequestDataTable();
  }

  initialiseAssetRequestDataTable(showType, pmoRaised) {

    showType = typeof showType == "undefined" ? "all" : showType;
    pmoRaised = typeof pmoRaised == "undefined" ? true : pmoRaised;

    // Setup - add a text input to each footer cell
    $("#assetPortalTable thead th").each(function () {
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
    // Show DataTable
    $('#assetPortalTable').show();
    // DataTable
    this.table = $("#assetPortalTable").DataTable({
      ajax: {
        url: "ajax/populateAssetRequestPortal.php",
        type: "POST",
        data: {
          show: showType,
          pmoRaised: pmoRaised
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
        {
          data: "REFERENCE",
          defaultContent: "",
          render: {
            _: "display",
            sort: "reference",
          },
        },
        { data: "CT_ID", defaultContent: "<i>no ctid</i>" },
        { data: "PERSON", defaultContent: "" },
        { data: "ASSET", defaultContent: "<i>unknown</i>" },
        { data: "STATUS", defaultContent: "" },
        { data: "JUSTIFICATION", defaultContent: "" },
        {
          data: "REQUESTOR",
          defaultContent: "",
          render: {
            _: "display",
            sort: "timestamp",
          },
        },
        { data: "APPROVER", defaultContent: "" },
        { data: "FM", defaultContent: "" },
        { data: "LOCATION", defaultContent: "<i>unknown</i>" },
        { data: "PRIMARY_UID", defaultContent: "<i>unknown</i>" },
        { data: "SECONDARY_UID", defaultContent: "<i>unknown</i>" },
        { data: "DATE_ISSUED_TO_IBM", defaultContent: "<i>unknown</i>" },
        { data: "DATE_ISSUED_TO_USER", defaultContent: "" },
        { data: "DATE_RETURNED", defaultContent: "" },
        { data: "ORDERIT_VARB_REF", defaultContent: "" },
        { data: "ORDERIT_NUMBER", defaultContent: "" },
        { data: "ORDERIT_STATUS", defaultContent: "" },
        { data: "ORDERIT_TYPE", defaultContent: "" },
        { data: "COMMENT", defaultContent: "" },
        { data: "USER_CREATED", defaultContent: "" },
        { data: "REQUESTEE_EMAIL", defaultContent: "" },
        { data: "REQUESTEE_NOTES", defaultContent: "" },
        { data: "APPROVER_EMAIL", defaultContent: "" },
        { data: "FM_EMAIL", defaultContent: "" },
        { data: "FM_NOTES", defaultContent: "" },
        { data: "CTB_RTB", defaultContent: "" },
        { data: "TT_BAU", defaultContent: "" },
        { data: "LOB", defaultContent: "" },
        { data: "WORK_STREAM", defaultContent: "" },
        { data: "PRE_REQ_REQUEST", defaultContent: "" },
        { data: "REQUEST_RETURN", defaultContent: "" },
      ],
      columnDefs: [
        {
          visible: false,
          targets: [
            8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
          ],
        },
      ],
      order: [[0, "desc"]],
      autoWidth: true,
      deferRender: true,
      processing: true,
      responsive: true,
      language: {
        emptyTable: "No Asset Requests to show",
      },
      dom: "Blfrtip",
      //	      colReorder: true,
      buttons: ["colvis", "excelHtml5", "csvHtml5", "print"],
    });
    // Apply the search
    this.table.columns().every(function () {
      var that = this;
      $("input", this.header()).on("keyup change", function () {
        if (that.search() !== this.value) {
          that.search(this.value).draw();
        }
      });
    });
  }
}

export { assetPortal as default };