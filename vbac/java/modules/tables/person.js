/*
 *
 *
 *
 */

let buttonCommon = await cacheBustImport('./modules/functions/buttonCommon.js');

class person {

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
      processing: true,
      serverSide: true,
      scrollColapse: false,
      searchDelay: 1000,
      autoWidth: true,
      responsive: false,
      // responsive: true,
      dom: "Blfrtip",
      paging: true,
      pagingType: 'full_numbers',
      pageLength: 100,
      lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
      ajax: {
        url: "ajax/populatePersonDatatable.php",
        dataType: 'json',
        type: "POST",
        data: {
          preBoardersAction: preBoardersAction
        },
        dataSrc: function (json) {
          // console.log('dataSrc');
          // console.log(json);
          // console.log($('#personTable_processing').is(":visible"));

          //Make your callback here.
          if (json.error.length != 0) {
            $('#errorMessageBody').html(json.error);
            $('#errorMessageModal').modal('show');
          }
          // console.log(json.data);
          return json.data;
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
          title: "CNUM",
          data: "CNUM",
          defaultContent: ""
        }, //00
        {
          title: "WORKER_ID",
          data: "WORKER_ID",
          defaultContent: ""
        }, //01
        {
          title: "OPEN_SEAT_NUMBER",
          data: "OPEN_SEAT_NUMBER",
          defaultContent: "",
        }, //02
        {
          title: "FIRST_NAME",
          data: "FIRST_NAME",
          defaultContent: "<i>unknown</i>",
        }, //03
        {
          title: "LAST_NAME",
          data: "LAST_NAME",
          defaultContent: "<i>unknown</i>",
        }, //04
        {
          title: "EMAIL_ADDRESS",
          data: "EMAIL_ADDRESS",
          defaultContent: "<i>unknown</i>",
        }, //05
        {
          title: "KYN_EMAIL_ADDRESS",
          data: "KYN_EMAIL_ADDRESS",
          defaultContent: "<i>unknown</i>",
        }, //06
        {
          title: "NOTES_ID",
          data: "NOTES_ID",
          defaultContent: "<i>unknown</i>",
        }, //07
        {
          title: "LBG_EMAIL",
          data: "LBG_EMAIL",
          defaultContent: "<i>unknown</i>",
        }, //08
        {
          title: "EMPLOYEE_TYPE",
          data: "EMPLOYEE_TYPE",
          defaultContent: ""
        }, //09
        {
          title: "FM_CNUM",
          data: "FM_CNUM",
          defaultContent: ""
        }, //10
        {
          title: "FM_MANAGER_FLAG",
          data: "FM_MANAGER_FLAG",
          defaultContent: "",
        }, //11
        {
          title: "CTB_RTB",
          data: "CTB_RTB",
          defaultContent: ""
        }, //12
        {
          title: "LOB",
          data: "LOB",
          defaultContent: ""
        }, //13
        {
          title: "SKILLSET",
          data: "SKILLSET",
          defaultContent: ""
        }, //14
        {
          title: "ROLE_TECHNOLOGY",
          data: "ROLE_TECHNOLOGY",
          defaultContent: "",
        }, //15
        {
          title: "START_DATE",
          data: "START_DATE",
          defaultContent: ""
        }, //16
        {
          title: "PROJECTED_END_DATE",
          data: "PROJECTED_END_DATE",
          defaultContent: "",
        }, //17
        {
          title: "COUNTRY",
          data: "COUNTRY",
          defaultContent: ""
        }, //18
        {
          title: "BASE_LOCATION",
          data: "IBM_BASE_LOCATION",
          defaultContent: "",
        }, //19
        {
          title: "LBG_LOCATION",
          data: "LBG_LOCATION",
          defaultContent: ""
        }, //20
        {
          title: "OFFBOARDED_DATE",
          data: "OFFBOARDED_DATE",
          defaultContent: "",
        }, //21
        {
          title: "PES_DATE_REQUESTED",
          data: "PES_DATE_REQUESTED",
          defaultContent: "",
        }, //22
        {
          title: "PES_REQUESTOR",
          data: "PES_REQUESTOR",
          defaultContent: ""
        }, //23
        {
          title: "PES_DATE_RESPONDED",
          data: "PES_DATE_RESPONDED",
          defaultContent: "",
        }, //24
        {
          title: "PES_STATUS_DETAILS",
          data: "PES_STATUS_DETAILS",
          defaultContent: "",
        }, //25
        {
          title: "PES_STATUS",
          data: "PES_STATUS",
          defaultContent: "",
          render: { _: "display", sort: "sort" },
        }, //26
        {
          title: "REVALIDATION_DATE_FIELD",
          data: "REVALIDATION_DATE_FIELD",
          defaultContent: "",
        }, //27
        {
          title: "REVALIDATION_STATUS",
          data: "REVALIDATION_STATUS",
          defaultContent: "",
        }, //28
        {
          title: "PROPOSED_LEAVING_DATE",
          data: "PROPOSED_LEAVING_DATE",
          defaultContent: "",
        }, //29
        {
          title: "CBN_DATE_FIELD",
          data: "CBN_DATE_FIELD",
          defaultContent: "",
        }, //30
        {
          title: "CBN_STATUS",
          data: "CBN_STATUS",
          defaultContent: ""
        }, //31
        {
          title: "CT_ID_REQUIRED",
          data: "CT_ID_REQUIRED",
          defaultContent: "",
        }, //32
        {
          title: "CT_ID",
          data: "CT_ID",
          defaultContent: ""
        }, //33
        {
          title: "CIO_ALIGNMENT",
          data: "CIO_ALIGNMENT",
          defaultContent: ""
        }, //34
        {
          title: "PRE_BOARDED",
          data: "PRE_BOARDED",
          defaultContent: ""
        }, //35
        {
          title: "SECURITY_EDUCATION",
          data: "SECURITY_EDUCATION",
          defaultContent: "",
        }, //36
        {
          title: "PMO_STATUS",
          data: "PMO_STATUS",
          defaultContent: ""
        }, //37
        {
          title: "PES_DATE_EVIDENCE",
          data: "PES_DATE_EVIDENCE",
          defaultContent: "",
        }, //38
        {
          title: "RSA_TOKEN",
          data: "RSA_TOKEN",
          defaultContent: ""
        }, //39
        {
          title: "CALLSIGN_ID",
          data: "CALLSIGN_ID",
          defaultContent: ""
        }, //40
        {
          title: "PROCESSING_STATUS",
          data: "PROCESSING_STATUS",
          defaultContent: "",
        }, //41
        {
          title: "PROCESSING_STATUS_CHANGED",
          data: "PROCESSING_STATUS_CHANGED",
          defaultContent: "",
        }, //42
        {
          title: "PES_LEVEL",
          data: "PES_LEVEL",
          defaultContent: "",
          render: { _: "display", sort: "sort" },
        }, //43
        {
          title: "PES_RECHECK_DATE",
          data: "PES_RECHECK_DATE",
          defaultContent: "",
        }, //44
        {
          title: "PES_CLEARED_DATE",
          data: "PES_CLEARED_DATE",
          defaultContent: "",
        }, //45	
        {
          title: "SQUAD_NUMBER",
          data: "SQUAD_NUMBER",
          defaultContent: "",
        }, //46
        {
          title: "SQUAD_NAME",
          data: "SQUAD_NAME",
          render: { _: "display", sort: "sort" },
        }, //47
        {
          title: "SQUAD_LEADER",
          data: "SQUAD_LEADER",
          defaultContent: "",
        }, //48
        {
          title: "TRIBE_NUMBER",
          data: "TRIBE_NUMBER",
          defaultContent: "",
        }, //49
        {
          title: "TRIBE_NAME",
          data: "TRIBE_NAME",
          defaultContent: "",
        }, //50
        {
          title: "TRIBE_LEADER",
          data: "TRIBE_LEADER",
          defaultContent: "",
        }, //51
        {
          title: "ORGANISATION",
          data: "ORGANISATION",
          defaultContent: "",
        }, //52
        {
          title: "ITERATION_MGR",
          data: "ITERATION_MGR",
          defaultContent: "",
        }, //53
        {
          title: "HAS_DELEGATES",
          data: "HAS_DELEGATES",
          defaultContent: "",
        }, //54
      ],
      columnDefs: [
        {
          visible: false,
          targets: [
            2, 7, 8, 9, 10,
            12, 13, 14, 15, 16, 17, 18, 19, 20,
            21, 22, 23, 24, 26, 27, 28, 29, 30,
            31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 
            51, 52, 53, 54
          ],
        },
      ],
      ordering: true,
      colReorder: false,
      // colReorder: {
      //   order: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34]
      // },
      order: [[4, "asc"]],
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

export { person as default };