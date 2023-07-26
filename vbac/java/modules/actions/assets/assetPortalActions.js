/*
 *
 *
 *
 */

let actionsContainer = await cacheBustImport('./modules/actions/actionsContainer.js');

// Asset Portal
let reporAll = await cacheBustImport('./modules/actions/assets/reportAll.js');
let reporAwaitingIAM = await cacheBustImport('./modules/actions/assets/reporAwaitingIAM.js');
let reportExportable = await cacheBustImport('./modules/actions/assets/reportExportable.js');
let reporExported = await cacheBustImport('./modules/actions/assets/reporExported.js');
let reporRaisedBAU = await cacheBustImport('./modules/actions/assets/reporRaisedBAU.js');
let reporRaisedNonBAU = await cacheBustImport('./modules/actions/assets/reporRaisedNonBAU.js');
let reporUserRaised = await cacheBustImport('./modules/actions/assets/reporUserRaised.js');
let reporShowUID = await cacheBustImport('./modules/actions/assets/reporShowUID.js');

let exportBauForOrderIt = await cacheBustImport('./modules/actions/assets/exportBauForOrderIt.js');
let exportNonBauForOrderIt = await cacheBustImport('./modules/actions/assets/exportNonBauForOrderIt.js');
let mapVarbToOrderIt = await cacheBustImport('./modules/actions/assets/mapVarbToOrderIt.js');
let setOrderItStatus = await cacheBustImport('./modules/actions/assets/setOrderItStatus.js');

let reload = await cacheBustImport('./modules/actions/reportReload.js');
let reset = await cacheBustImport('./modules/actions/reportReset.js');

class assetPortalActions extends actionsContainer {

  title = 'Asset Request Portal';
  resetColumns = [0, 1, 2, 3, 4, 5, 6, 7];

  constructor(parent) {
    super(parent);
    const ReporAll = new reporAll(this);
    const ReporAwaitingIAM = new reporAwaitingIAM(this);
    const ReportExportable = new reportExportable(this);
    const ReporExported = new reporExported(this);
    const ReporRaisedBAU = new reporRaisedBAU(this);
    const ReporRaisedNonBAU = new reporRaisedNonBAU(this);
    const ReporUserRaised = new reporUserRaised(this);
    const ReporShowUID = new reporShowUID(this);
    
    const ExportBauForOrderIt = new exportBauForOrderIt(this);
    const ExportNonBauForOrderIt = new exportNonBauForOrderIt(this);
    const MapVarbToOrderIt = new mapVarbToOrderIt(this);
    const SetOrderItStatus = new setOrderItStatus(this);

    const ReportReload = new reload(this);
    const ReportReset = new reset(this);
  }
}

export { assetPortalActions as default };