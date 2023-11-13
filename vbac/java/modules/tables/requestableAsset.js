/*
 *
 *
 *
 */

class requestableAsset {

  table;

  constructor() {
    this.initialiseDataTable();
  }

  initialiseDataTable() {
    // Setup - add a text input to each footer cell
    $("#requestableAssetTable tfoot th").each(function () {
      var title = $(this).text();
      $(this).html(
        '<input type="text" placeholder="Search ' + title + '" />'
      );
    });
    // Show DataTable
    $('#requestableAssetTable').show();
    // DataTable
    console.log($("#requestableAssetTable"));
    this.table = $("#requestableAssetTable").DataTable({
      ajax: {
        url: "ajax/populateRequestableAssetTable.php",
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
      columnDefs: [
        { visible: false, targets: [6, 7, 8, 9, 10, 12, 13, 14, 15] },
      ],
      columns: [
        { data: "ASSET_TITLE", defaultContent: "" },
        { data: "ASSET_PREREQUISITE", defaultContent: "" },

        { data: "ASSET_PRIMARY_UID_TITLE", defaultContent: "" },
        { data: "ASSET_SECONDARY_UID_TITLE", defaultContent: "" },

        { data: "APPLICABLE_ONSHORE", defaultContent: "" },
        { data: "APPLICABLE_OFFSHORE", defaultContent: "" },

        { data: "REQUEST_BY_DEFAULT", defaultContent: "" },
        { data: "BUSINESS_JUSTIFICATION_REQUIRED", defaultContent: "" },

        { data: "RECORD_DATE_ISSUED_TO_IBM", defaultContent: "" },
        { data: "RECORD_DATE_ISSUED_TO_USER", defaultContent: "" },
        { data: "RECORD_DATE_RETURNED", defaultContent: "" },

        { data: "LISTING_ENTRY_CREATED", defaultContent: "" },
        { data: "LISTING_ENTRY_CREATED_BY", defaultContent: "" },

        { data: "LISTING_ENTRY_REMOVED", defaultContent: "" },
        { data: "LISTING_ENTRY_REMOVED_BY", defaultContent: "" },

        { data: "PROMPT", defaultContent: "" },
        { data: "ORDER_IT_TYPE", defaultContent: "" },
        { data: "ORDER_IT_REQUIRED", defaultContent: "" },
      ],
      order: [[0, "asc"]],
      autoWidth: false,
      deferRender: true,
      processing: true,
      responsive: true,
      dom: "Blfrtip",
      //	    	colReorder: true,
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

export { requestableAsset as default };