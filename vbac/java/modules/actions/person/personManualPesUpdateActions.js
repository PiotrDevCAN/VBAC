/*
 *
 *
 *
 */

let actionsContainer = await cacheBustImport('./modules/actions/actionsContainer.js');

// linked
let pesUpdate = await cacheBustImport('./modules/actions/person/reportPesUpdate.js');
let reload = await cacheBustImport('./modules/actions/reportReload.js');
let reset = await cacheBustImport('./modules/actions/reportReset.js');

class personManualPesUpdateActions extends actionsContainer {

  title = 'Manual Status Override';
  resetColumns = [0, 1, 2, 3, 4, 5];

  constructor(parent) {
    super(parent);
    const ReportPesUpdate = new pesUpdate(this);
    const ReportReload = new reload(this);
    const ReportReset = new reset(this);
  }
}

export { personManualPesUpdateActions as default };