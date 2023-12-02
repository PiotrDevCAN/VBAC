/*
 *
 *
 *
 */

let bandMappingTable = await cacheBustImport('./modules/tables/bandMapping.js');
let editBandMapping = await cacheBustImport('./modules/boxes/editBandMapping.js');

class bandMapping {

  table;
  tableObj;

  constructor() {
    $('.select2').select2();

    this.listenForSubmitBandMappingForm();
  }

  listenForSubmitBandMappingForm() {
    var $this = this;
    $(document).on('submit', '#bandMappingForm', function (event) {
      console.log("submit clicked");
      event.preventDefault();
      $(":submit").addClass("spinning").attr("disabled", true);
      var disabledFields = $(":disabled");
      $(disabledFields).attr("disabled", false);
      var formData = $("#bandMappingForm").serialize();

      console.log(formData);

      $(disabledFields).attr("disabled", true);
      $.ajax({
        type: "post",
        url: "ajax/saveBandMappingRecord.php",
        data: formData,
        success: function (response) {
          var responseObj = JSON.parse(response);
          console.log(responseObj);
          if (responseObj.success) {
            $('#messageModalBody').html("<p>Band Mapping Record Saved</p>");
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
          $("#BUSINESS_TITLE").val("");
          $("#BAND").val("");
          $this.table.ajax.reload();
        }
      });
    });
  }
}

const BandMapping = new bandMapping();

const BandMappingTable = new bandMappingTable();
BandMapping.table = BandMappingTable.table;
BandMapping.tableObj = BandMappingTable;

const EditBandMapping = new editBandMapping(BandMapping);

export { BandMapping as default };