/*
 *
 *
 *
 */

let buttonCommon = await cacheBustImport('./modules/functions/buttonCommon.js');

class personLite {

  table;

  constructor(preBoardersAction) {
    this.initialiseDataTable(preBoardersAction);
  }

  initialiseDataTable(preBoardersAction) {
    $("#personTable").on("draw.dt", function () {
      $('[data-toggle="popover"]').popover();
    });

    $("#personTable").on("column-visibility.dt", function () {
      $('[data-toggle="popover"]').popover();
    });

    preBoardersAction = typeof preBoardersAction == "undefined" ? null : preBoardersAction;

    // Setup - add a text input to each footer cell
    $("#personTable tfoot th").each(function () {
      var title = $(this).text();
      var titleCondensed = title.replace(" ", "");
      $(this).html('<input type="text" id="footer' + titleCondensed + '" placeholder="Search ' + title + '" />');
    });

    // Show DataTable
    $('#personTable').show();

    // DataTable
    this.table = $("#personTable").DataTable({
      autoWidth: true,
      deferRender: true,
      processing: true,
      responsive: true,
      dom: "Blfrtip",
      paging: true,
      pagingType: 'full_numbers',
      pageLength: 100,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      ajax: {
        url: "ajax/populatePersonPortalLite.php",
        dataType: 'json',
        type: "POST",
        data: {
          preBoardersAction: preBoardersAction
        },
      },
      columns: [
        {
          title: "CNUM",
          data: "CNUM",
          defaultContent: "",
          visible: true,
        }, //00
        {
          title: "OPEN_SEAT_NUMBER",
          data: "OPEN_SEAT_NUMBER",
          defaultContent: "",
          visible: false,
        }, //01
        {
          title: "FIRST_NAME",
          data: "FIRST_NAME",
          defaultContent: "<i>unknown</i>",
          visible: true,
        }, //02
        {
          title: "LAST_NAME",
          data: "LAST_NAME",
          defaultContent: "<i>unknown</i>",
          visible: true,
        }, //03
        {
          title: "EMAIL_ADDRESS",
          data: "EMAIL_ADDRESS",
          defaultContent: "<i>unknown</i>",
          visible: true,
        }, //04
        {
          title: "KYN_EMAIL_ADDRESS",
          data: "KYN_EMAIL_ADDRESS",
          defaultContent: "<i>unknown</i>",
          visible: true,
        }, //05
        {
          title: "NOTES_ID",
          data: "NOTES_ID",
          defaultContent: "<i>unknown</i>",
          visible: false,
        }, //06
        {
          title: "LBG_EMAIL",
          data: "LBG_EMAIL",
          defaultContent: "<i>unknown</i>",
          visible: false,
        }, //07
        {
          title: "EMPLOYEE_TYPE",
          data: "EMPLOYEE_TYPE",
          defaultContent: "",
          visible: false,
        }, //08
        {
          title: "FM_CNUM",
          data: "FM_CNUM",
          defaultContent: "",
          visible: false,
        }, //09
        {
          title: "FM_MANAGER_FLAG",
          data: "FM_MANAGER_FLAG",
          defaultContent: "",
          visible: false,
        }, //10
        {
          title: "LOB",
          data: "LOB",
          defaultContent: "",
          visible: false
        }, //11
        {
          title: "SKILLSET",
          data: "SKILLSET",
          defaultContent: "",
          visible: false
        }, //12
        {
          title: "START_DATE",
          data: "START_DATE",
          defaultContent: "",
          visible: false,
        }, //13
        {
          title: "PROJECTED_END_DATE",
          data: "PROJECTED_END_DATE",
          defaultContent: "",
          visible: false,
        }, //14
        {
          title: "COUNTRY",
          data: "COUNTRY",
          defaultContent: "",
          visible: false,
        }, //15
        {
          title: "BASE_LOCATION",
          data: "IBM_BASE_LOCATION",
          defaultContent: "",
          visible: false,
        }, //16
        {
          title: "LBG_LOCATION",
          data: "LBG_LOCATION",
          defaultContent: "",
          visible: false,
        }, //17
        {
          title: "PES_DATE_REQUESTED",
          data: "PES_DATE_REQUESTED",
          defaultContent: "",
          visible: false,
        }, //18
        {
          title: "PES_REQUESTOR",
          data: "PES_REQUESTOR",
          defaultContent: "",
          visible: false,
        }, //19
        {
          title: "PES_DATE_RESPONDED",
          data: "PES_DATE_RESPONDED",
          defaultContent: "",
          visible: false,
        }, //20
        {
          title: "PES_STATUS_DETAILS",
          data: "PES_STATUS_DETAILS",
          defaultContent: "",
          visible: false,
        }, //21
        {
          title: "PES_STATUS",
          data: "PES_STATUS",
          defaultContent: "",
          render: { _: "display", sort: "sort" },
          visible: true
        }, //22
        {
          title: "REVALIDATION_DATE_FIELD",
          data: "REVALIDATION_DATE_FIELD",
          defaultContent: "",
          visible: false,
        }, //23
        {
          title: "REVALIDATION_STATUS",
          data: "REVALIDATION_STATUS",
          defaultContent: "",
          visible: false,
        }, //24
        {
          title: "PROPOSED_LEAVING_DATE",
          data: "PROPOSED_LEAVING_DATE",
          defaultContent: "",
          visible: false,
        }, //25
        {
          title: "CBN_DATE_FIELD",
          data: "CBN_DATE_FIELD",
          defaultContent: "",
          visible: false,
        }, //26
        {
          title: "CBN_STATUS",
          data: "CBN_STATUS",
          defaultContent: "",
          visible: false,
        }, //27
        {
          title: "CT_ID",
          data: "CT_ID",
          defaultContent: "",
          visible: false
        }, //28
        {
          title: "PRE_BOARDED",
          data: "PRE_BOARDED",
          defaultContent: "",
          visible: false,
        }, //29
        {
          title: "PES_DATE_EVIDENCE",
          data: "PES_DATE_EVIDENCE",
          defaultContent: "",
          visible: false,
        }, //30
        {
          title: "RSA_TOKEN",
          data: "RSA_TOKEN",
          defaultContent: "",
          visible: false,
        }, //31
        {
          title: "CALLSIGN_ID",
          data: "CALLSIGN_ID",
          defaultContent: "",
          visible: false,
        }, //32
        {
          title: "PROCESSING_STATUS",
          data: "PROCESSING_STATUS",
          defaultContent: "",
          visible: false,
        }, //33
        {
          title: "PROCESSING_STATUS_CHANGED",
          data: "PROCESSING_STATUS_CHANGED",
          defaultContent: "",
          visible: false,
        }, //34
        {
          title: "PES_LEVEL",
          data: "PES_LEVEL",
          defaultContent: "",
          render: { _: "display", sort: "sort" },
          visible: false,
        }, //35
        {
          title: "PES_RECHECK_DATE",
          data: "PES_RECHECK_DATE",
          defaultContent: "",
          visible: false,
        }, //36
        {
          title: "PES_CLEARED_DATE",
          data: "PES_CLEARED_DATE",
          defaultContent: "",
          visible: false,
        }, //37
        {
          title: "SQUAD_NUMBER",
          data: "SQUAD_NUMBER",
          defaultContent: "",
          visible: false,
        }, //38
        {
          title: "SQUAD_NAME",
          data: "SQUAD_NAME",
          render: { _: "display", sort: "sort" },
          visible: false
        }, //39
        {
          title: "SQUAD_LEADER",
          data: "SQUAD_LEADER",
          defaultContent: "",
          visible: false,
        }, //40
        {
          title: "TRIBE_NUMBER",
          data: "TRIBE_NUMBER",
          defaultContent: "",
          visible: false,
        }, //41
        {
          title: "TRIBE_NAME",
          data: "TRIBE_NAME",
          defaultContent: "",
          visible: false,
        }, //42
        {
          title: "TRIBE_LEADER",
          data: "TRIBE_LEADER",
          defaultContent: "",
          visible: false,
        }, //43
        {
          title: "ORGANISATION",
          data: "ORGANISATION",
          defaultContent: "",
          visible: false,
        }, //44
        {
          title: "ITERATION_MGR",
          data: "ITERATION_MGR",
          defaultContent: "",
          visible: false,
        }, //45
        {
          title: "PMO_STATUS",
          data: "PMO_STATUS",
          defaultContent: "",
          visible: false,
        }, //46
        {
          title: "HAS_DELEGATES",
          data: "HAS_DELEGATES",
          defaultContent: "",
          visible: false,
        }, //45
      ],
      drawCallback: function (settings) {
        $('[data-toggle="popover"]').popover();
      },
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

export { personLite as default };