/**
 *
 */

let assetPortalTable = await cacheBustImport('./modules/tables/assetPortal.js');

let actions = await cacheBustImport('./modules/actions/assets/assetPortalActions.js');

let manageAssetStatusBox = await cacheBustImport('./modules/boxes/assets/manageAssetStatusBox.js');
let assetReturnedBox = await cacheBustImport('./modules/boxes/assets/assetReturnedBox.js');
let editUidBox = await cacheBustImport('./modules/boxes/assets/editUidBox.js');
let addToJustificationBox = await cacheBustImport('./modules/boxes/assets/addToJustificationBox.js');
let amendOrderItNumberBox = await cacheBustImport('./modules/boxes/assets/amendOrderItNumberBox.js');

class assetPortal {

  table;
  tableObj;

  constructor() {

    this.countRequestsForPortal();

    // pass table to actions
    // const Actions = new actions(this);
  }

  countRequestsForPortal() {
    $("#countAll").html("**");
    $("#countAwaitingIam").html("**");
    $("#countPmoForExport").html("**");
    $("#countNonPmoForExport").html("**");
    $("#countBauForExport").html("**");
    $("#countNonBauExport").html("**");
    $("#countPmoExported").html("**");
    $("#countBauRaised").html("**");
    $("#countNonBauRaised").html("**");

    $.ajax({
      url: "ajax/countRequestsForPortal.php",
      type: "GET",
      success: function (result) {
        var resultObj = JSON.parse(result);
        $("#countAll").html(resultObj.all);
        $("#countAwaitingIam").html(resultObj.awaitingIam);
        $("#countPmoForExport").html(resultObj.pmoForExport);
        $("#countNonPmoForExport").html(resultObj.nonPmoForExport);
        $("#countBauForExport").html(resultObj.bauForExport);
        $("#countNonBauExport").html(resultObj.nonBauForExport);
        $("#countPmoExported").html(resultObj.pmoExported);
        $("#countBauRaised").html(resultObj.bauRaised);
        $("#countNonBauRaised").html(resultObj.nonBauRaised);
      },
    });
  }
}

const AssetPortal = new assetPortal();

const AssetPortalTable = new assetPortalTable();
AssetPortal.table = AssetPortalTable.table;
AssetPortal.tableObj = AssetPortalTable;

// pass table to actions
const Actions = new actions(AssetPortal);

const ManageAssetStatusBox = new manageAssetStatusBox(AssetPortal);
const AssetReturnedBox = new assetReturnedBox(AssetPortal);
const EditUidBox = new editUidBox(AssetPortal);
const AddToJustificationBox = new addToJustificationBox(AssetPortal);
const AmendOrderItNumberBox = new amendOrderItNumberBox(AssetPortal);

export { AssetPortal as default };