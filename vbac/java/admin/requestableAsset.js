/**
 *
 */

let requestableAssetTable = await cacheBustImport('./modules/tables/requestableAsset.js');
let editAssetBox = await cacheBustImport('./modules/boxes/editAssetBox.js');

class requestableAsset {

  table;
  tableObj;

  constructor() {
    $('[data-toggle="tooltip"]').tooltip();

    this.initialiseSelect2();
    this.listenForJustificationButton();
    this.listenForSaveRequestableAsset();
  }

  initialiseSelect2() {
    $("#asset_prerequisite").select2({
      placeholder: "Select a pre-requisite asset(if appro.)",
      //		  allowClear: true,
      //		  ajax: {
      //			    url: 'ajax/possiblePrerequisiteAssets.php',
      //			    dataType: 'json',
      //			    data:{assetTitle: 'Dummy',
      //			    	  assetPrereq: 'CT ID'}
      //			  }
    });
  }

  listenForJustificationButton() {
    $(document).on("change", "#businessJustification", function () {
      $("#promptDiv").toggle();
      $("#prompt").attr("required")
        ? $("#prompt").attr("required", false)
        : $("#prompt").attr("required", true);
    });
  }

  listenForSaveRequestableAsset() {
    var $this = this;
    $(document).on("click", "#saveRequestableAsset", function () {
      console.log($(this).val());
      $("#saveRequestableAsset").attr("disabled", true);
      $this.saveRequestableAsset($(this).val());
    });
  }

  saveRequestableAsset(mode) {
    console.log("saveRequestableAsset mode:" + mode);
    $("#asset_title").attr("disabled", false); // So it can be passed to the ajax call.
    var ibmer = $("#hasBpEntry").is(":checked");
    var form = $("#requestableAssetListForm");
    var formValid = form[0].checkValidity();
    if (formValid) {
      $("#saveRequestableAsset").addClass("spinning");
      var formData = form.serialize();
      var inputData = $("input").serialize();
      console.log(inputData);
      formData += "&mode=" + mode;
      console.log(formData);
      $.ajax({
        url: "ajax/saveRequestableAsset.php",
        type: "POST",
        data: formData,
        success: function (result) {
          $("#saveRequestableAsset").removeClass("spinning");
          $("#updateRequestableAsset").removeClass("spinning");
          console.log(result);
          var resultObj = JSON.parse(result);
          if (resultObj.success == true) {
            $("#requestableAssetListForm")[0].reset();
          }
          console.log(typeof this.table);
          $(".greyablePage").addClass("overlay");
          location.reload();
        },
      });
    } else {
      console.log("invalid fields follow");
      console.log($(form).find(":invalid"));
    }
  }
}

const RequestableAsset = new requestableAsset();

const RequestableAssetTable = new requestableAssetTable();
RequestableAsset.table = RequestableAssetTable.table;
RequestableAsset.tableObj = RequestableAssetTable;

const EditAssetBox = new editAssetBox(RequestableAsset);

export { RequestableAsset as default };