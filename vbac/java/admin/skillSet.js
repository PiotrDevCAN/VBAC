/*
 *
 *
 *
 */

let workLocationTable = await cacheBustImport('./modules/tables/skillSet.js');
let editSkillSet = await cacheBustImport('./modules/boxes/editSkillSet.js');

class skillSet {

  table;
  tableObj;

  constructor() {
    $('.select2').select2();

    this.listenForSubmitSkillsetForm();
  }

  listenForSubmitSkillsetForm() {
    var $this = this;
    $(document).on('submit', '#skillSetForm', function (event) {
      console.log("submit clicked");
      event.preventDefault();
      $(":submit").addClass("spinning").attr("disabled", true);
      var disabledFields = $(":disabled");
      $(disabledFields).attr("disabled", false);
      var formData = $("#skillSetForm").serialize();

      console.log(formData);

      $(disabledFields).attr("disabled", true);
      $.ajax({
        type: "post",
        url: "ajax/saveSkillSetRecord.php",
        data: formData,
        success: function (response) {
          var responseObj = JSON.parse(response);
          console.log(responseObj);
          if (responseObj.success) {
            $('#messageModalBody').html("<p>Skillset Record Saved</p>");
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
          $("#SKILLSET").val("");
          $this.table.ajax.reload();
        }
      });
    });
  }
}

const SkillSet = new skillSet();

const SkillSetTable = new workLocationTable();
SkillSet.table = SkillSetTable.table;
SkillSet.tableObj = SkillSetTable;

const EditWorkLocation = new editSkillSet(SkillSet);

export { SkillSet as default };