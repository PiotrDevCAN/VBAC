/*
 *
 *
 *
 */

let dlpTable = await cacheBustImport('./modules/tables/dlp.js');

let actions = await cacheBustImport('./modules/actions/dlp/dlpActions.js');

let dlpLicenseApproveBox = await cacheBustImport('./modules/boxes/dlp/dlpLicenseApproveBox.js');
let dlpLicenseRejectBox = await cacheBustImport('./modules/boxes/dlp/dlpLicenseRejectBox.js');
let dlpLicenseDeleteBox = await cacheBustImport('./modules/boxes/dlp/dlpLicenseDeleteBox.js');

class dlp {

  table;

  constructor() {
    console.log("+++ Function +++ dlp.init");
    if ($(".toggle").length > 0) {
      $(".toggle").bootstrapToggle();
    }

    $("#licencee").select2({
      width: "100%",
      placeholder: "Select Licencee",
      allowClear: true,
    });

    $("#approvingManager").select2({
      width: "100%",
      placeholder: "Select Approving Mgr",
      allowClear: true,
    });

    this.listenForSaveDlp();
    this.listenForSelectLicencee();

    console.log("--- Function --- dlp.init");
  }

  initialiseLicenseeDropDown() {
    $("#saveDlpLicence").attr("disabled", true); // Lock the Save Button - when the listener fires we unlock it. Saves duplicates.
    $("#licencee").select2({
      ajax: {
        url: "ajax/populateDlpLicenseeDropdown.php",
        dataType: "json",
        beforeSend: function (jqXHR, settings) {
          $.each(xhrPool, function (idx, jqXHR) {
            console.log('abort jqXHR');
            jqXHR.abort();  // basically, cancel any existing request, so this one is the only one running
            xhrPool.splice(idx, 1);
          });
          xhrPool.push(jqXHR);
        }
        // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
      },
      width: "100%",
      placeholder: "Select licence holder",
      allowClear: true,
    });
  }

  listenForSelectLicencee() {
    $(document).off("select2:select", "#licencee");
    $(document).on("select2:select", "#licencee", function (e) {
      $("#saveDlpLicence").attr("disabled", false);
      console.log(e.params.data);
      var cnum = e.params.data.id;
      var fmcnum = cnumfm[cnum];
      var hostname = licences[cnum];

      console.log(hostname);
      console.log(cnum);
      console.log(fmcnum);

      $("#currentHostname").val(hostname);

      console.log($("#currentHostname").val());

      $("#approvingManager").val(fmcnum).trigger("change");

      console.log($("#approvingManager").val());
    });
  }

  listenForSaveDlp() {
    var $this = this;
    $(document).on("click", "#saveDlpLicence", function () {
      console.log("they want to save");

      $("#saveDlpLicence").addClass("spinning");
      $("#saveDlpLicence").attr("disabled", true);

      var form = document.getElementById("dlpRecordingForm");
      var formValid = form.checkValidity();

      var currentHostname = $("#currentHostname").val();
      var hostname = $("#hostname").val().toUpperCase();

      console.log(formValid);
      console.log(currentHostname);
      console.log(hostname);

      formValid = formValid ? currentHostname != hostname : formValid;
      console.log(formValid);

      if (formValid) {
        $(document).on('hidden.bs.modal', '#confirmInstalled', function (event) {
          //
          if ($("#dlpInstalConfirmed").is(":checked")) {
            $this.saveRecord();
          } else {
            $("#saveDlpLicence").removeClass("spinning");
            $("#saveDlpLicence").attr("disabled", false);
          }
          $("#confirmInstalled").remove("hidden.bs.modal");
        });
        $("#confirmInstalled").prop("checked", false);
        $("#confirmInstalled").modal("show");
      } else if (currentHostname == hostname) {
        $('#messageModalBody').html("<p>New Hostname(" + hostname + ") matches current Hostname (" + currentHostname + ") for the licencee</p>");
        $('#messageModal').modal('show');
        $("#saveDlpLicence").removeClass("spinning");
        $("#saveDlpLicence").attr("disabled", false);
      } else {
        $('#messageModalBody').html("<p>Form is not valid, please correct</p>");
        $('#messageModal').modal('show');
        $("#saveDlpLicence").removeClass("spinning");
        $("#saveDlpLicence").attr("disabled", false);
      }
    });
  }

  saveRecord() {
    var $this = this;
    console.log("would save DLP now");
    var allDisabledFields = $("#dlpRecordingForm input:disabled");
    $(allDisabledFields).attr("disabled", false);
    var formData = $("#dlpRecordingForm").serialize();
    $(allDisabledFields).attr("disabled", true);
    console.log(formData);
    $.ajax({
      url: "ajax/saveDlp.php",
      data: formData,
      type: "POST",
      success: function (result) {
        var resultObj = JSON.parse(result);
        console.log(resultObj);
        console.log(resultObj.licencee);
        console.log(resultObj.hostname);
        console.log(licences);
        licences[resultObj.licencee] = resultObj.hostname;
        console.log(licences);
        $("#licencee").val(null).trigger("change");
        //  $this.initialiseLicenseeDropDown();
        //  $this.listenForSelectLicencee();
        $("#dlpSaveResponseModal .modal-body").html(
          resultObj.actionsTaken +
          "<hr/><p class='bg-warning'>" +
          resultObj.messages +
          "</p>"
        );
        $("#dlpSaveResponseModal").modal("show");
        $("#saveDlpLicence").removeClass("spinning");
        $("#saveDlpLicence").attr("disabled", false);
        $("#approvingManager").val("").trigger("change");
        $("#hostname").val("");
        $("#currentHostname").val("");
        $this.table.ajax.reload();
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(textStatus);
        console.log(errorThrown);
        $("#dlpSaveResponseModal .modal-body").html(
          textStatus + "<hr/>" + errorThrown
        );
        $("#dlpSaveResponseModal").modal("show");
        $("#saveDlpLicence").removeClass("spinning");
        $("#saveDlpLicence").attr("disabled", false);
        $("#licensee").val(null).trigger("change");
        $("#hostname").val("");
        $("#currentHostname").val("");
      },
    });
  }
}

const Dlp = new dlp();

const DlpTable = new dlpTable();
Dlp.table = DlpTable.table;
Dlp.tableObj = DlpTable;

// pass table to actions
const Actions = new actions(Dlp);

const DlpLicenseApproveBox = new dlpLicenseApproveBox(Dlp);
const DlpLicenseRejectBox = new dlpLicenseRejectBox(Dlp);
const DlpLicenseDeleteBox = new dlpLicenseDeleteBox(Dlp);

export { Dlp as default };