/*
 *
 *
 *
 */

let workLocationTable = await cacheBustImport('./modules/tables/workLocation.js');
let editWorkLocation = await cacheBustImport('./modules/boxes/editWorkLocation.js');

class workLocation {

  table;
  tableObj;

  constructor() {
    $('.select2').select2();

    $('#COUNTRY.select2, #CITY.select2').select2({
      tags: true,
      selectOnClose: true,
      //Allow manually entered text in drop down.
      createTag: function (params) {
        var name = params.term.charAt(0).toUpperCase() + params.term.slice(1);
        return {
          id: name,
          text: name
        };
      }
    });

    this.listenForSubmitLocationForm();
  }

  listenForSubmitLocationForm() {
    var $this = this;
    $(document).on('submit', '#workLocationForm', function (event) {
      console.log("submit clicked");
      event.preventDefault();
      $(":submit").addClass("spinning").attr("disabled", true);
      var disabledFields = $(":disabled");
      $(disabledFields).attr("disabled", false);
      var formData = $("#workLocationForm").serialize();

      console.log(formData);

      $(disabledFields).attr("disabled", true);
      $.ajax({
        type: "post",
        url: "ajax/saveWorkLocationRecord.php",
        data: formData,
        success: function (response) {
          var responseObj = JSON.parse(response);
          console.log(responseObj);
          if (responseObj.success) {
            $('#messageModalBody').html("<p>Work Location Record Saved</p>");
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
          $("#COUNTRY").val("").trigger("change");
          $("#CITY").val("").trigger("change");
          $("#ADDRESS").val("").trigger("change");
          $("#ONSHORE").val("").trigger("change");
          $("#CBC_IN_PLACE").val("").trigger("change");
          $this.table.ajax.reload();
        }
      });
    });
  }
}

const WorkLocation = new workLocation();

const WorkLocationTable = new workLocationTable();
WorkLocation.table = WorkLocationTable.table;
WorkLocation.tableObj = WorkLocationTable;

const EditWorkLocation = new editWorkLocation(WorkLocation);

export { WorkLocation as default };