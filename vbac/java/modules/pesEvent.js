/*
 *
 *
 *
 */

let spinner = await cacheBustImport('./modules/functions/spinner.js');

$.expr[":"].contains = $.expr.createPseudo(function (arg) {
  return function (elem) {
    return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
  };
});

function searchTable() {
  var filter = $("#pesTrackerTableSearch").val().toUpperCase();

  if (filter.length > 3) {
    $("#pesTrackerTable tr").hide();
    $("#pesTrackerTable th").parent("tr").show();

    $("#pesTrackerTable tbody tr")
      .children("td")
      .not(".nonSearchable")
      .each(function () {
        var text = $(this)
          .text()
          .trim()
          .replace(/[\xA0]/gi, " ")
          .replace(/  /g, "")
          .toUpperCase();
        if (text.indexOf(filter) > -1) {
          var tr = $(this).parent("tr").show();
        }
      });
  } else {
    $("#pesTrackerTable tr").show();
  }
}

class pesEvent {

  table;

  constructor() {
    var $this = this;
    $(".pesDateLastChased")
      .datepicker({
        dateFormat: "dd M yy",
        maxDate: 0,
        onSelect: function (dateText) {
          var cnum = $(this).data("cnum");
          var workerId = $(this).data("workerid");
          $this.saveDateLastChased(dateText, cnum, workerId, this);
        },
      })
      .on("change", function () {
        alert("Got change event from field");
      });
  }

  listenForBtnRecordSelection() {
    var $this = this;
    $(document).on("click", ".btnRecordSelection", function () {
      $(".btnRecordSelection").removeClass("active");
      $(this).addClass("active");
      $this.populatePesTracker($(this).data("pesrecords"));
    });
  }

  listenForBtnChaser() {
    var $this = this;
    $(document).on("click", ".btnChaser", function () {
      var chaser = $(this).data("chaser");
      var details = $(this)
        .parent("span")
        .parents("td")
        .children(".personDetails")
        .first();
      var cnum = $(details).data("cnum");
      var workerId = $(details).data("workerid");
      var firstName = $(details).data("firstname");
      var lastName = $(details).data("lastname");
      var emailaddress = $(details).data("emailaddress");
      var flm = $(details).data("flm");

      var buttonObj = $(this);
      buttonObj.addClass("spinning");

      var dateField = buttonObj
        .parents("td")
        .find(".pesDateLastChased")
        .first();
      $.ajax({
        url: "ajax/sendPesEmailChaser.php",
        type: "POST",
        data: {
          cnum: cnum,
          workerid: workerId,
          firstname: firstName,
          lastname: lastName,
          emailaddress: emailaddress,
          chaser: chaser,
          flm: flm,
        },
        success: function (result) {
          var resultObj = JSON.parse(result);
          console.log(resultObj);
          $(dateField).val(resultObj.lastChased);
          $this.getAlertClassForPesChasedDate(dateField);
          if (resultObj.success == true) {
            buttonObj.removeClass("spinning");
            buttonObj
              .parents("td")
              .parent("tr")
              .children("td.pesCommentsTd")
              .children("div.pesComments")
              .html(resultObj.comment);
          } else {
            alert("error has occured");
            alert(resultObj);
          }
        },
      });
    });
  }

  listenForBtnSetPesLevel() {
    $(document).on("click", ".btnSetPesLevel", function () {
      var cnum = $(this).data("cnum");
      var workerId = $(this).data("workerid");
      var level = $(this).data("level");

      var buttonObj = $(this);
      var cellObj = $(this).parent("span").parents("td");

      console.log(cellObj);

      buttonObj.addClass("spinning");

      $.ajax({
        url: "ajax/setPesLevel.php",
        type: "POST",
        data: {
          cnum: cnum,
          workerid: workerId,
          level: level
        },
        success: function (result) {
          var resultObj = JSON.parse(result);
          console.log(resultObj);
          if (resultObj.success == true) {
            buttonObj.removeClass("spinning");
            cellObj.html(resultObj.cell);
            cellObj
              .parent("tr")
              .children("td.pesCommentsTd")
              .children("div.pesComments")
              .html(resultObj.comment);
          } else {
            alert("error has occured");
            alert(resultObj);
          }
        },
      });
    });
  }

  populatePesTracker(records) {
    var buttons = $(".btnRecordSelection");

    $("#pesTrackerTableDiv").html(spinner);

    this.table = $.ajax({
      url: "ajax/populatePesTrackerTable.php",
      type: "POST",
      data: {
        records: records
      },

      // dataType: 'json',
      // dataSrc: function (json) {
      //   console.log('dataSrc');
      //   console.log(json);
      //   console.log($('#pesTrackerTable_processing').is(":visible"));

      //   //Make your callback here.
      //   if (json.error.length != 0) {
      //     $('#messageModalBody').html(json.error);
      //     $('#messageModal').modal('show');
      //   }
      //   console.log(json.data);
      //   return json.data;
      // },

      beforeSend: function (jqXHR, settings) {
        console.log('before send');
        console.log($('.dataTables_processing'));
        console.log($('#pesTrackerTable_processing').is(":visible"));

        $.each(xhrPool, function (idx, jqXHR) {
          jqXHR.abort();  // basically, cancel any existing request, so this one is the only one running
          xhrPool.splice(idx, 1);
        });
        xhrPool.push(jqXHR);
      },
      success: function (result) {
        var resultObj = JSON.parse(result);

        console.log(resultObj.success);
        console.log(resultObj.messages);
        if (resultObj.success) {
          $("#pesTrackerTableDiv").html(resultObj.table);

          $("#pesTrackerTable thead th").each(function () {
            var title = $(this).text();
            $(this).html(
              title + '<input class="secondInput" type="hidden"  />'
            );
          });

          $("#pesTrackerTable thead td").each(function () {
            var title = $(this).text();
            $(this).html(
              '<input class="firstInput" type="text" size="10" placeholder="Search ' +
              title +
              '" />'
            );
          });
        } else {
          $("#pesTrackerTableDiv").html(resultObj.messages);
        }
      },
    });

    // Apply the search

    $(document).on("keyup change", ".firstInput", function (e) {
      var searchFor = this.value;
      var col = $(this).parent().index();
      var searchCol = col + 1;
      if (searchFor.length >= 3) {
        $("#pesTrackerTable tbody tr").hide();
        $(
          "#pesTrackerTable tbody td:nth-child(" +
          searchCol +
          "):contains(" +
          searchFor +
          ")	"
        )
          .parent()
          .show();
      } else {
        $("#pesTrackerTable tbody tr").show();
      }
    });
  }

  saveDateLastChased(date, cnum, workerId, field) {
    console.log(field);
    console.log($(field));
    var $this = this;
    var parentDiv = $(field).parent("div");
    $.ajax({
      url: "ajax/savePesDateLastChased.php",
      type: "POST",
      data: {
        cnum: cnum,
        workerid: workerId,
        date: date
      },
      success: function (result) {
        var resultObj = JSON.parse(result);
        $this.getAlertClassForPesChasedDate(field);
        buttonObj
          .parents("td")
          .parent("tr")
          .children("td.pesCommentsTd")
          .children("div.pesComments")
          .html(resultObj.comment);
      },
    });
  }

  listenForComment() {
    $("textarea").on("input", function () {

    });
  }

  listenForSavePesComment() {
    $(document).on("click", ".btnPesSaveComment", function () {
      var cnum = $(this).siblings("textarea").data("cnum");
      var workerid = $(this).siblings("textarea").data("workerid");
      var comment = $(this).siblings("textarea").val();
      var button = $(this);

      console.log(button.siblings("div"));
      console.log(button.siblings("div.pesComments"));

      button.addClass("spinning");
      $.ajax({
        url: "ajax/savePesComment.php",
        type: "POST",
        data: {
          cnum: cnum,
          workerid: workerid,
          comment: comment
        },
        success: function (result) {
          var resultObj = JSON.parse(result);
          button.removeClass("spinning");
          button.siblings("div.pesComments").html(resultObj.comment);
          button.siblings("textarea").val("");
        },
      });
    });
  }

  listenForPesStageValueChange() {
    var $this = this;
    $(document).on("click", ".btnPesStageValueChange", function () {
      var setPesTo = $(this).data("setpesto");
      var column = $(this).parents("div").data("pescolumn");
      var cnum = $(this).parents("div").data("cnum");
      var workerId = $(this).parents("div").data("workerid");

      var alertClass = $this.getAlertClassForPesStage(setPesTo);

      $(this).parents("div").prev("div.pesStageDisplay").html(setPesTo);
      $(this)
        .parents("div")
        .prev("div.pesStageDisplay")
        .removeClass("alert-info")
        .removeClass("alert-warning")
        .removeClass("alert-success")
        .addClass(alertClass);
      $(this).addClass("spinning");

      var buttonObj = $(this);

      $.ajax({
        url: "ajax/savePesStageValue.php",
        type: "POST",
        data: {
          cnum: cnum,
          workerid: workerId,
          stageValue: setPesTo,
          stage: column
        },
        success: function (result) {
          var resultObj = JSON.parse(result);
          console.log(result.messages);
          if (resultObj.success == true) {
            buttonObj
              .parents("td")
              .parent("tr")
              .children("td.pesCommentsTd")
              .children("div.pesComments")
              .html(resultObj.comment);
          } else {
            $(this)
              .parents("div")
              .prev("div.pesStageDisplay")
              .html(resultObj.message);
          }
          buttonObj.removeClass("spinning");
        },
      });
    });
  }

  getAlertClassForPesStage(pesStageValue) {
    var alertClass = "";
    switch (pesStageValue) {
      case "Yes":
        alertClass = " alert-success ";
        break;
      case "Prov":
        alertClass = " alert-warning ";
        break;
      case "N/A":
        alertClass = " alert-secondary ";
        break;
      default:
        alertClass = " alert-info ";
        break;
    }
    return alertClass;
  }

  listenForPesProcessStatusChange() {
    $(document).on("click", ".btnProcessStatusChange", function () {
      var buttonObj = $(this);
      var processStatus = $(this).data("processstatus");
      var dataDiv = $(this).parents("td").children(".personDetails").first();
      var cnum = $(dataDiv).data("cnum");
      var workerId = $(dataDiv).data("workerid");
      var firstname = $(dataDiv).data("firstname");
      var lastname = $(dataDiv).data("lastname");
      var emailaddress = $(dataDiv).data("emailaddress");
      var flm = $(dataDiv).data("flm");
      $(this).addClass("spinning");
      $.ajax({
        url: "ajax/savePesProcessStatus.php",
        type: "POST",
        data: {
          cnum: cnum,
          workerid: workerId,
          processStatus: processStatus,
          firstname: firstname,
          lastname: lastname,
          emailaddress: emailaddress,
          flm: flm,
        },
        success: function (result) {
          var resultObj = JSON.parse(result);
          if (resultObj.success == true) {
            buttonObj
              .parents("div:first")
              .siblings("div.pesProcessStatusDisplay")
              .html(resultObj.formattedStatusField);
            buttonObj
              .parents("td")
              .parent("tr")
              .children("td.pesCommentsTd")
              .children("div.pesComments")
              .html(resultObj.comment);
          }
          $(buttonObj).removeClass("spinning");
        },
      });
    });
  }

  listenForPesPriorityChange() {
    var $this = this;
    $(document).on("click", ".btnPesPriority", function () {
      var buttonObj = $(this);
      var pespriority = $(this).data("pespriority");
      var cnum = $(this).data("cnum");
      var workerId = $(this).data("workerid");
      $(this).addClass("spinning");
      $.ajax({
        url: "ajax/savePesPriority.php",
        type: "POST",
        data: {
          cnum: cnum,
          workerid: workerId,
          pespriority: pespriority
        },
        success: function (result) {
          var resultObj = JSON.parse(result);
          if (resultObj.success == true) {
            buttonObj
              .parent("span")
              .siblings("div.priorityDiv:first")
              .html("Priority:" + pespriority);
            $this.setAlertClassForPesPriority(
              buttonObj.parent("span").siblings("div.priorityDiv:first"),
              pespriority
            );

            buttonObj
              .parents("td")
              .parent("tr")
              .children("td.pesCommentsTd")
              .children("div.pesComments")
              .html(resultObj.comment);
          }
          $(buttonObj).removeClass("spinning");
        },
      });
    });
  }

  setAlertClassForPesPriority(priorityField, priority) {
    $(priorityField).removeClass("alert-success");
    $(priorityField).removeClass("alert-warning");
    $(priorityField).removeClass("alert-danger");
    $(priorityField).removeClass("alert-info");

    switch (priority) {
      case 1:
        console.log("danger");
        $(priorityField).addClass("alert-danger");
        break;
      case 2:
        console.log("warning");
        $(priorityField).addClass("alert-warning");
        break;
      case 3:
        $(priorityField).addClass("alert-success");
        break;
      default:
        $(priorityField).addClass("alert-info");
        break;
    }
  }

  getAlertClassForPesChasedDate(dateField) {
    $(dateField).parent("div").removeClass("alert-success");
    $(dateField).parent("div").removeClass("alert-warning");
    $(dateField).parent("div").removeClass("alert-danger");
    $(dateField).parent("div").removeClass("alert-info");

    var today = new Date();
    var dateValue = $(dateField).val();
    var lastChased = new Date(dateValue);

    if (typeof lastChased == "object") {
      var timeDiff = Math.abs(today.getTime() - lastChased.getTime());
      var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24));

      switch (true) {
        case diffDays < 7:
          $(dateField).parent("div").addClass("alert-success");
          break;
        case diffDays < 14:
          $(dateField).parent("div").addClass("alert-warning");
          break;
        default:
          $(dateField).parent("div").addClass("alert-danger");
          break;
      }
    } else {
      $(dateField).parent("div").removeClass("alert-info");
      return;
    }
  }

  listenForFilterPriority() {
    $(document).on("click", ".btnSelectPriority", function () {
      var priority = $(this).data("pespriority");
      if (priority != 0) {
        $("tr").hide();
        $(".priorityDiv:contains('" + priority + "')")
          .parents("tr")
          .show();
        $("th").parent("tr").show();
      } else {
        $("tr").show();
      }
    });
  }

  listenForFilterProcess() {
    $(document).on("click", ".btnSelectProcess", function () {
      var pesprocess = $(this).data("pesprocess");
      $("tr").hide();
      $(".pesProcessStatusDisplay:contains('" + pesprocess + "')")
        .parents("tr")
        .show();
      $("th").parent("tr").show();
    });
  }
}

const PesEvent = new pesEvent();

export { PesEvent as default };