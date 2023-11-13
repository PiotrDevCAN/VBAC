/*
 *
 *
 *
 */

class ringFencing {

  table;

  constructor() {
    this.initialiseRfStartEndDate();
    this.initialiseRfFlagReport();
    this.listenForSaveRfFlag();
    this.listenForDeleteRfFlag();
  }

  initialiseRfStartEndDate() {
    $("#rfStart_Date").datepicker({
      dateFormat: "dd M yy",
      altField: "#rfStart_Date_Db2",
      altFormat: "yy-mm-dd",
      maxDate: +100,
      onSelect: function (selectedDate) {
        $("#rfEnd_Date").datepicker("option", "minDate", selectedDate);
      },
    });

    var rfStartDate = $("#rfStart_Date").datepicker("getDate");

    $("#rfEnd_Date").datepicker({
      dateFormat: "dd M yy",
      altField: "#rfEnd_Date_Db2",
      altFormat: "yy-mm-dd",
      minDate: rfStartDate,
    });
  }

  initialiseRfFlagReport() {
    // Setup - add a text input to each footer cell
    $("#rfFlagTable tfoot th").each(function () {
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
    this.table = $("#rfFlagTable").DataTable({
      ajax: {
        url: "ajax/populateRfFlagReport.php",
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
        { data: "CNUM", defaultContent: "" },
        { data: "NOTES_ID", defaultContent: "<i>unknown</i>" },
        { data: "LOB", defaultContent: "<i>unknown</i>" },
        { data: "CTB_RTB", defaultContent: "<i>unknown</i>" },
        { data: "FM", defaultContent: "<i>unknown</i>" },
        { data: "REVAL", defaultContent: "" },
        { data: "EXP", defaultContent: "" },
        { data: "FROM", defaultContent: "" },
        { data: "TO", defaultContent: "" },
      ],
      processing: true,
      responsive: true,
      dom: "Blfrtip",
      buttons: ["colvis", "print"],
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

  listenForSaveRfFlag() {
    var $this = this;
    $(document).on("click", "#saveRfFlag", function () {
      $("#saveRfFlag").addClass("spinning");
      var form = $("#rfFlagForm");
      var formValid = form[0].checkValidity();
      if (formValid) {
        var cnum = $("#personForRfFlag").val();
        var rfStart = $("#rfStart_Date_Db2").val();
        var rfEnd = $("#rfEnd_Date_Db2").val();
        $.ajax({
          url: "ajax/setRfFlag.php",
          type: "POST",
          data: { cnum: cnum, rfFlag: 1, rfStart: rfStart, rfEnd: rfEnd },
          success: function (result) {
            $("#saveRfFlag").removeClass("spinning");
            var resultObj = JSON.parse(result);
            $("#personForRfFlag").val("");
            $("#rfStart_Date").val("");
            $("#rfStart_Date_Db2").val("");
            $("#rfEnd_Date").val("");
            $("#rfEnd_Date_Db2").val("");
            $this.table.ajax.reload();
          },
        });
      } else {
        $("#saveRfFlag").removeClass("spinning");
        console.log("invalid fields follow");
        console.log($(form).find(":invalid"));
      }
    });
  }

  listenForDeleteRfFlag() {
    var $this = this;
    $(document).on("click", ".btnDeleteRfFlag", function (e) {
      $(this).addClass("spinning");
      var cnum = $(this).data("cnum");
      $.ajax({
        url: "ajax/setRfFlag.php",
        type: "POST",
        data: { cnum: cnum, rfFlag: 0 },
        success: function (result) {
          console.log(result);
          var resultObj = JSON.parse(result);
          $this.table.ajax.reload();
        },
      });
    });
  }
}

const RingFencing = new ringFencing();

export { RingFencing as default };