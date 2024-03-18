/*
 *
 *
 *
 */

let convertOceanToKyndryl = await cacheBustImport('./modules/functions/convertOceanToKyndryl.js');

let squadAssignmentTable = await cacheBustImport('./modules/tables/squadAssignment.js');
let editSquadAssignment = await cacheBustImport('./modules/boxes/editSquadAssignment.js');

let agileTribe = await cacheBustImport('./modules/functions/selects/agileTribe.js');
let agileSquad = await cacheBustImport('./modules/functions/selects/agileSquad.js');

let agileTribeToSquadSelect = await cacheBustImport('./modules/functions/agileTribeToSquadSelect.js');

class squadAssignment {

  static formId = 'squadForm';
  static saveButtonId = 'save';
  static resetButtonId = 'reset';

  static noLongerAvailable = 'No longer available';

  table;
  tableObj;

  constructor() {

    this.listenForName();
    this.listenForSaveForm();
    this.listenForResetForm();

    agileTribe('TRIBE_NUMBER', 'originalTRIBE_NUMBER');
    agileSquad('SQUAD_NUMBER', 'originalSQUAD_NUMBER');

    agileTribeToSquadSelect('TRIBE_NUMBER', 'SQUAD_NUMBER');

    $('#TYPE').select2();

    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
  }

  listenForName() {
    var $this = this;
    $(".typeahead").bind("typeahead:select", async function (ev, suggestion) {
      $(".tt-menu").hide();

      var newCnum = suggestion.cnum;
      var newCnumRAW = suggestion.cnum;
      if (typeof (newCnum) == 'undefined') {
        newCnum = squadAssignment.noLongerAvailable;
        newCnumRAW = '';
      }

      var workerId = suggestion.workerID;
      var workerIdRAW = suggestion.workerID;
      if (typeof (workerId) == 'undefined') {
        workerId = '';
        workerIdRAW = 0;
      }

      $("#CNUM").val(newCnum);
      $("#WORKER_ID").val(workerId);
      $("#EMAIL_ADDRESS").val(suggestion.mail);
      var kynValue = convertOceanToKyndryl(suggestion.mail);
      $("#KYN_EMAIL_ADDRESS").val(kynValue);

      var trimmedCnum = newCnumRAW.trim();
      var trimmedWorkerId = workerIdRAW;
      var trimmedKynValue = kynValue.trim();
      if (trimmedCnum !== "" || trimmedWorkerId != 0 || trimmedKynValue != "") {
        // either value available

        $("#" + squadAssignment.saveButtonId).attr("disabled", false);
        $("#person_name").css("background-color", "LightGreen");

      } else {
        // no need to check
        $("#" + squadAssignment.saveButtonId).attr("disabled", true);
        $("#person_name").css("background-color", "LightPink");
        $('#messageModalBody').html("<p>Invalid person data read from Worker API</p>");
        $('#messageModal').modal('show');
      }
    });
  }

  listenForSaveForm() {
    var $this = this;
    $(document).on('submit', '#squadForm', function (event) {
      console.log("submit clicked");
      event.preventDefault();
      $(":submit").addClass("spinning").attr("disabled", true);
      var disabledFields = $(":disabled");
      $(disabledFields).attr("disabled", false);
      var formData = $("#squadForm").serialize();

      console.log(formData);

      $(disabledFields).attr("disabled", true);
      $.ajax({
        type: "post",
        url: "ajax/saveSquadAssignmentRecord.php",
        data: formData,
        success: function (response) {
          var responseObj = JSON.parse(response);
          console.log(responseObj);
          if (responseObj.success) {
            $('#messageModalBody').html("<p>Squad Assignment Record Saved</p>");
            $('#messageModal').modal('show');
          } else {
            $('#messageModalBody').html(
              "<p>Save has encountered a problem</p><p>" +
              responseObj.messages +
              "</p>"
            );
            $('#messageModal').modal('show');
          }
          $(".spinning").removeClass("spinning").attr("disabled", false);
          $("#ID").val("");
          $this.table.ajax.reload();
        }
      });
    });
  }

  listenForResetForm() {
    var $this = this;
    $(document).on('reset', '#' + squadAssignment.formId, function (event) {
      console.log("reset clicked " + squadAssignment.formId);
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

const PersonPortal = new squadAssignment();

const PersonTable = new squadAssignmentTable();
PersonPortal.table = PersonTable.table;
PersonPortal.tableObj = PersonTable;

const EditSquadAssignment = new editSquadAssignment(PersonPortal);

export { squadAssignment as default };