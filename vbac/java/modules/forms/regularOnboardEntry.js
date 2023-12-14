/**
 *
 */

let convertOceanToKyndryl = await cacheBustImport('./modules/functions/convertOceanToKyndryl.js');
let inArrayCaseInsensitive = await cacheBustImport('./modules/functions/inArrayCaseInsensitive.js');

let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate_Regular.js');
let initialiseOtherDates = await cacheBustImport('./modules/functions/initialiseOtherDates_Regular.js');
let initialiseFormSelect2 = await cacheBustImport('./modules/functions/initialiseFormSelect2_Regular.js');

let saveRegularBoarding = await cacheBustImport('./modules/functions/saveRegularBoarding.js');

let toTitleCase = await cacheBustImport('./modules/functions/toTitleCase.js');

let knownCNUMs = await cacheBustImport('./modules/dataSources/knownCNUMs.js');
let knownWorkerIDs = await cacheBustImport('./modules/dataSources/knownWorkerIDs.js');
let knownKyndrylEmails = await cacheBustImport('./modules/dataSources/knownKyndrylEmails.js');

let entry = await cacheBustImport('./modules/forms/onboardEntry.js');

class regularOnboardEntry extends entry {

  static formId = 'boardingFormIbmer';
  static saveButtonId = 'saveRegularBoarding';
  static resetButtonId = 'resetRegularBoarding';
  static initiatePesButtonId = 'initiateRegularPes';

  static saveBoarding = saveRegularBoarding;

  static noLongerAvailable = 'No longer available';

  table;
  responseObj;

  constructor() {
    console.log('+++ Function +++ regularOnboardEntry.constructor');

    super(regularOnboardEntry);

    this.listenForName();
    this.listenForLinkToPreBoarded();

    this.listenForResetForm();

    console.log('--- Function --- regularOnboardEntry.constructor');
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

      var newCnum = suggestion.cnum;
      var newCnumRAW = suggestion.cnum;
      if (typeof (newCnum) == 'undefined') {
        newCnum = regularOnboardEntry.noLongerAvailable;
        newCnumRAW = '';
      }

      var workerId = suggestion.workerID;
      var workerIdRAW = suggestion.workerID;
      if (typeof (newCnum) == 'undefined') {
        workerId = '';
        workerIdRAW = 0;
      }

      $("#person_notesid").val(suggestion.notesEmail);
      $("#person_serial").val(newCnum)
        .attr("disabled", "disabled");
      $("#person_workerid").val(workerId);
      $("#person_bio").val(suggestion.role);
      $("#person_intranet").val(suggestion.mail);
      var kynValue = convertOceanToKyndryl(suggestion.mail);
      $("#person_kyn_intranet").val(kynValue);

      let knownCnum = await knownCNUMs.getCNUMs();
      let knownWorkerIds = await knownWorkerIDs.getWorkerIDs();
      let knownKyndrylEmail = await knownKyndrylEmails.getEmails();

      var trimmedCnum = newCnumRAW.trim();
      var trimmedWorkerId = workerIdRAW;
      var trimmedKynValue = kynValue.trim();
      if (trimmedCnum !== "" || trimmedWorkerId != 0 || trimmedKynValue != "") {
        // either value available

        var allreadyExistsCNUM = false;
        if (trimmedCnum !== "") {
          var allreadyExistsCNUM = inArrayCaseInsensitive(trimmedCnum, knownCnum) >= 0;
        }

        var allreadyExistsWorkerID = false;
        if (trimmedWorkerId !== 0) {
          var allreadyExistsWorkerID = $.inArray(trimmedWorkerId, knownWorkerIds) >= 0;
        }

        var allreadyKyndrylExists = false;
        if (trimmedKynValue !== "") {
          var allreadyKyndrylExists = inArrayCaseInsensitive(trimmedKynValue, knownKyndrylEmail) >= 0;
        }

        if (allreadyExistsCNUM || allreadyExistsWorkerID || allreadyKyndrylExists) {
          // comes back with Position in array(true) or false is it's NOT in the array.
          $("#" + regularOnboardEntry.saveButtonId).attr("disabled", true);
          $("#person_name").css("background-color", "LightPink");
          $('#messageModalBody').html("<p>Person already defined to VBAC</p>");
          $('#messageModal').modal('show');
        } else {
          $("#" + regularOnboardEntry.saveButtonId).attr("disabled", false);
          $("#person_name").css("background-color", "LightGreen");

          var regex = /[.]/;
          var bio = document.getElementById("person_bio");
          if (typeof bio !== "undefined") {
            bio.value = suggestion.businessTitle;
          }

          var uid = document.getElementById("person_uid");
          if (typeof uid !== "undefined") {
            uid.value = newCnum;
          }

          var fname = document.getElementById("person_first_name");
          var firstName = suggestion.firstName;
          while (regex.test(firstName) && i < suggestion.firstName.length) {
            var firstNameNext = suggestion.firstName[++i];
            if (typeof firstNameNext !== "undefined") {
              firstName = firstNameNext;
            }
          }

          // get rid off dot from end of name
          if (firstName[firstName.length - 1] === ".") {
            firstName = firstName.slice(0, -1);
          }

          var capitalizedName = toTitleCase(firstName);
          if (typeof fname !== "undefined") {
            fname.value = capitalizedName;
          }

          var lname = document.getElementById("person_last_name");
          var lastName = suggestion.lastName;
          if (typeof lname !== "undefined") {
            lname.value = lastName;
          }

          var isMgr = document.getElementById("person_is_mgr");
          if (typeof isMgr !== "undefined") {
            var isManager = suggestion.isManager;
            if (isManager == "Y" || isManager == "Yes" || isManager == true) {
              isMgr.value = "Yes";
            } else {
              isMgr.value = "No";
            }
          }

          var employeeeType = document.getElementById("person_employee_type");
          if (typeof employeeeType !== "undefined") {
            employeeeType.value = suggestion.employeeType;
          }

          var country = document.getElementById("person_country");
          if (typeof country !== "undefined") {
            country.value = suggestion.countryName;
          }

          var location = document.getElementById("person_ibm_location");
          if (typeof location !== "undefined") {
            location.value = suggestion.workLoc;
          }

          var workeridObj = document.getElementById("person_workerid");
          if (typeof workeridObj !== "undefined") {
            workeridObj.value = workerId;
          }
          var worker_idObj = document.getElementById("person_worker_id");
          if (typeof worker_idObj !== "undefined") {
            worker_idObj.value = workerId;
          }
        }
      } else {
        // no need to check
        $("#" + regularOnboardEntry.saveButtonId).attr("disabled", true);
        $("#person_name").css("background-color", "LightPink");
        $('#messageModalBody').html("<p>Invalid person data read from Worker API</p>");
        $('#messageModal').modal('show');
      }
    });
  }

  listenForLinkToPreBoarded() {
    $(document).on("select2:select", "#person_preboarded", function (e) {
      var data = e.params.data;
      var cnum = data.id;
      if (cnum != "") {
        // They have selected an entry
        var allEnabled = $("form :enabled");
        $(allEnabled).attr("disabled", true);
        $("#" + regularOnboardEntry.saveButtonId).addClass("spinning");
        $("#initiateRegularPes").hide();
        $.ajax({
          url: "ajax/prePopulateFromLink.php",
          type: "POST",
          data: {
            cnum: cnum
          },
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