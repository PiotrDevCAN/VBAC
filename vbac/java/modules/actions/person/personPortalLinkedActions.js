/*
 *
 *
 *
 */
let actionsContainer = await cacheBustImport('./modules/actions/actionsContainer.js');

// linked
let action = await cacheBustImport('./modules/actions/person/reportAction.js');
let pes = await cacheBustImport('./modules/actions/person/reportPes.js');
let all = await cacheBustImport('./modules/actions/reportAll.js');
let reload = await cacheBustImport('./modules/actions/reportReload.js');
let reset = await cacheBustImport('./modules/actions/reportReset.js');

class personPortalLinkedActions extends actionsContainer {

  title = 'Linked Portal';
  visibleColumns = [0, 1, 5, 9, 25, 37, 46, 47];
  PESColumns = [5, 21, 22, 23, 25, 27, 35, 38];
  resetColumns = [0, 1, 2, 3, 4, 25];

  constructor(parent) {
    super(parent);
    const ReportAction = new action(this);
    const ReportPes = new pes(this);
    const ReportAll = new all(this);
    const ReportReload = new reload(this);
    const ReportReset = new reset(this);
  }
}

export { personPortalLinkedActions as default };