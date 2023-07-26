/**
 *
 */

let convertOceanToKyndryl = await cacheBustImport('./modules/functions/convertOceanToKyndryl.js');
let inArrayCaseInsensitive = await cacheBustImport('./modules/functions/inArrayCaseInsensitive.js');

let fetchBluepagesDetailsForCnum = await cacheBustImport('./modules/functions/fetchBluepagesDetailsForCnum.js');

let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate_Regular.js');
let initialiseOtherDates = await cacheBustImport('./modules/functions/initialiseOtherDates_Regular.js');
let initialiseFormSelect2 = await cacheBustImport('./modules/functions/initialiseFormSelect2_Regular.js');

let saveRegularBoarding = await cacheBustImport('./modules/functions/saveRegularBoarding.js');

let knownCNUMs = await cacheBustImport('./modules/dataSources/knownCNUMs.js');

class regularOnboardEntry {

  static formId = 'boardingFormIbmer';
  static saveButtonId = 'saveRegularBoarding';
  static resetButtonId = 'resetRegularBoarding';
  static initiatePesButtonId = 'initiateRegularPes';

  saveButton;
  initiatePesButton;
  table;
  responseObj;

  constructor() {

    this.saveButton = $("#" + regularOnboardEntry.saveButtonId);
    this.initiatePesButton = $("#" + regularOnboardEntry.initiatePesButtonId);

    this.listenForName();
    this.listenForSerial();
    this.listenForLinkToPreBoarded();

    this.listenForSaveBoarding();
    this.listenForResetForm();
  }

  initialiseForm() {
    initialiseStartEndDate();
    initialiseOtherDates();
    initialiseFormSelect2();
  }

  listenForName() {
    var $this = this;
    $(".typeahead").bind("typeahead:select", async function (ev, suggestion) {
      $(".tt-menu").hide();
      $("#person_notesid").val(suggestion.notesEmail);
      $("#person_serial").val(suggestion.cnum).attr("disabled", "disabled");
      $("#person_bio").val(suggestion.role);
      $("#person_intranet").val(suggestion.mail);
      var kynValue = convertOceanToKyndryl(suggestion.mail);
      $("#person_kyn_intranet").val(kynValue);

      var newCnum = suggestion.cnum;
      var trimmedCnum = newCnum.trim();
      if (trimmedCnum !== "") {
        var knownCnum = await knownCNUMs.getCNUMs();
        // var allreadyExists = $.inArray(newCnum, knownCnum) >= 0;
        var allreadyExists = inArrayCaseInsensitive(newCnum, knownCnum) >= 0;
        if (allreadyExists) {
          // comes back with Position in array(true) or false is it's NOT in the array.
          $("#" + regularOnboardEntry.saveButtonId).attr("disabled", true);
          $("#person_name").css("background-color", "LightPink");
          $('#messageModalBody').html("<p>Person already defined to VBAC</p>");
          $('#messageModal').modal('show');
        } else {
          $("#" + regularOnboardEntry.saveButtonId).attr("disabled", false);
          $("#person_name").css("background-color", "LightGreen");
          fetchBluepagesDetailsForCnum(suggestion.cnum);
        }
      } else {
        // no need to check
        $("#" + regularOnboardEntry.saveButtonId).attr("disabled", true);
        $("#person_name").css("background-color", "LightPink");
        $('#messageModalBody').html("<p>Invalid person data read from BluePages</p>");
        $('#messageModal').modal('show');
      }
    });
  }

  listenForSerial() {
    var $this = this;
    $(document).on("keyup change", "#person_serial", function (e) {
      var cnum = $(this).val();
      if (cnum.length == 9) {
        fetchBluepagesDetailsForCnum(cnum);
      }
    });
  }

  listenForLinkToPreBoarded() {
    $(document).on("select2:select", "#person_preboarded", function (e) {
      var data = e.params.data;
      if (data.id != "") {
        // They have selected an entry
        var allEnabled = $("form :enabled");
        $(allEnabled).attr("disabled", true);
        $("#" + regularOnboardEntry.saveButtonId).addClass("spinning");
        $("#initiateRegularPes").hide();
        $.ajax({
          url: "ajax/prePopulateFromLink.php",
          type: "POST",
          data: { cnum: data.id },
          success: function (result) {
            $("#" + regularOnboardEntry.saveButtonId).removeClass("spinning");
            var resultObj = JSON.parse(result);
            if (resultObj.success == true) {
              $(allEnabled).attr("disabled", false);
              var $radios = $("input:radio[name=CTB_RTB]");
              $($radios).attr("disabled", false);

              if (resultObj.data.CTB_RTB != null) {
                var button = $radios.filter(
                  "[value=" + resultObj.data.CTB_RTB.trim() + "]"
                );
                $(button).prop("checked", true);
                $(button).trigger("click");
              }

              var $radios = $(".accountOrganisation");
              $($radios).attr("disabled", false);

              var contractorIdReq;
              if (resultObj.data.CT_ID_REQUIRED != null) {
                if (
                  resultObj.data.CT_ID_REQUIRED.trim()
                    .toUpperCase()
                    .substring(0, 1) == "Y"
                ) {
                  contractorIdReq = "yes";
                } else {
                  contractorIdReq = "no";
                }
              } else {
                contractorIdReq = "no";
              }
              $("#person_ct_id_required")
                .attr("disabled", false)
                .val(contractorIdReq)
                .trigger("change");

              var FMCnum = resultObj.data.FM_CNUM;
              $("#person_FM_CNUM").attr("disabled", false)
              if (FMCnum) {
                $("#person_FM_CNUM")
                  .val(FMCnum.trim())
                  .trigger("change");
              } else {

              }

              var openSeatNumber = resultObj.data.OPEN_SEAT_NUMBER;
              $("#person_open_seat").attr("disabled", false);
              if (openSeatNumber) {
                $("#person_open_seat").val(openSeatNumber.trim());
              } else {
                $("#person_open_seat").val("");
              }

              var skillsetId = resultObj.data.SKILLSET_ID;
              if (skillsetId) {
                $("#person_skill_set_id")
                  .val(skillsetId)
                  .trigger("change");
              } else {
                $("#person_skill_set_id")
                  .val("")
                  .trigger("change");
              }

              // date picker
              var sDate = resultObj.data.START_DATE;
              $("#person_start_date").attr("disabled", false);
              if (sDate) {
                var sDateDate = new Date(sDate);
                $("#person_start_date").datepicker("setDate", sDateDate);
              } else {
                $("#person_start_date").datepicker("setDate", "");
              }

              // date picker
              var eDate = resultObj.data.PROJECTED_END_DATE;
              $("#person_end_date").attr("disabled", false);
              if (eDate) {
                var eDateDate = new Date(eDate);
                $("#person_end_date").datepicker("setDate", eDateDate);
              } else {
                $("#person_end_date").datepicker("setDate", "");
              }

              // hidden
              var pesDateReq = resultObj.data.PES_DATE_REQUESTED;
              if (pesDateReq) {
                $("#person_pes_date_requested").val(pesDateReq.trim());
              } else {
                $("#person_pes_date_requested").val("");
              }

              // hidden
              var pesDateResp = resultObj.data.PES_DATE_RESPONDED;
              if (pesDateResp) {
                $("#person_pes_date_responded").val(pesDateResp.trim());
              } else {
                $("#person_pes_date_responded").val("");
              }

              var pesRequestor = resultObj.data.PES_REQUESTOR;
              if (pesRequestor) {
                $("#person_pes_requestor").val(pesRequestor.trim());
              } else {
                $("#person_pes_requestor").val("");
              }

              var pesStatus = resultObj.data.PES_STATUS;
              if (pesStatus) {
                $("#person_pes_status").val(pesStatus.trim());
              } else {
                $("#person_pes_status").val("");
              }

              var pesStatusDet = resultObj.data.PES_STATUS_DETAILS;
              if (pesStatusDet) {
                $("#person_pes_status_details").val(pesStatusDet.trim());
              } else {
                $("#person_pes_status_details").val("");
              }

              var pesLevel = resultObj.data.PES_LEVEL;
              if (pesLevel) {
                $("#person_pesLevel")
                  .val(pesLevel.trim())
                  .trigger("change");
              } else {
                $("#person_pes_status_details")
                  .val("")
                  .trigger("change");
              }

              // disabled date picker
              var pesClearedDate = resultObj.data.PES_CLEARED_DATE;
              if (pesClearedDate) {
                $("#person_pes_cleared_date").val(pesClearedDate.trim());
              } else {
                $("#person_pes_cleared_date").val("");
              }

              // disabled date picker
              var pesRecheckDate = resultObj.data.PES_RECHECK_DATE;
              if (pesRecheckDate) {
                $("#person_pes_recheck_date").val(pesRecheckDate.trim());
              } else {
                $("#person_pes_recheck_date").val("");
              }

              // disabled date picker
              var proposedLeavingDate = resultObj.data.PROPOSED_LEAVING_DATE;
              if (proposedLeavingDate) {
                $("#person_proposed_leaving_date").val(proposedLeavingDate.trim());
              } else {
                $("#person_proposed_leaving_date").val("");
              }

              $(allEnabled).attr("disabled", false); // open up the fields we'd disabled.
            }
          },
        });
      } else {

      }
    });
  }

  listenForSaveBoarding() {
    var $this = this;
    $(document).on("click", "#" + regularOnboardEntry.saveButtonId, function () {
      $(this).attr("disabled", true);
      var form = $("#" + regularOnboardEntry.formId);
      saveRegularBoarding("Save", form, $this.saveButton, $this.initiatePesButton);
    });
  }

  listenForResetForm() {
    var $this = this;
    $(document).on('reset', '#' + regularOnboardEntry.formId, function (event) {
      console.log("reset clicked " + regularOnboardEntry.formId);
      // event.preventDefault();

      // fields in IBMer
      // $("#person_name").val("").trigger("change");
      $("#person_name").css("background-color", "");
      // $("#person_serial").val("").trigger("change");

      // $("#person_notesid").val("").trigger("change");
      // $("#person_intranet").val("").trigger("change");
      // $("#person_bio").val("").trigger("change");

    });
  }
}

const RegularOnboardEntry = new regularOnboardEntry();

export { RegularOnboardEntry as default };