/*
 *
 *
 *
 */

let actionsContainer = await cacheBustImport('./modules/actions/actionsContainer.js');

// full
let action = await cacheBustImport('./modules/actions/squad/reportAction.js');
let all = await cacheBustImport('./modules/actions/reportAll.js');
let removeAssigned = await cacheBustImport('./modules/actions/squad/reportRemoveAssigned.js');
let reload = await cacheBustImport('./modules/actions/reportReload.js');
let reset = await cacheBustImport('./modules/actions/reportReset.js');

class personSquadsActions extends actionsContainer {

  title = 'Agile Tribes/Squads assignment crosscheck';
  visibleColumns = [0, 1, 2, 4, 7];
  statusColumnIdx = 4;
  resetColumns = [0, 1, 2, 4, 7];

  constructor(parent) {
    super(parent);
    const ReportAction = new action(this);
    const ReportAll = new all(this);
    const ReportRemoveAssigned = new removeAssigned(this);
    const ReportReload = new reload(this);
    const ReportReset = new reset(this);
  }
}

export { personSquadsActions as default };