/*
 *
 *
 *
 */

let actionsContainer = await cacheBustImport('./modules/actions/actionsContainer.js');

// lite
let action = await cacheBustImport('./modules/actions/person/reportAction.js');
let pes = await cacheBustImport('./modules/actions/person/reportPes.js');
let revalidation = await cacheBustImport('./modules/actions/person/reportRevalidation.js');
let mgrsCbn = await cacheBustImport('./modules/actions/person/reportMgrsCbn.js');
let squads = await cacheBustImport('./modules/actions/person/reportSquads.js');
let all = await cacheBustImport('./modules/actions/reportAll.js');
let removeOffb = await cacheBustImport('./modules/actions/person/reportRemoveOffb.js');
let reload = await cacheBustImport('./modules/actions/reportReload.js');
let reset = await cacheBustImport('./modules/actions/reportReset.js');

let reportSave = await cacheBustImport('./modules/actions/reportSave.js');
let reportSaveConfirm = await cacheBustImport('./modules/actions/reportSaveConfirm.js');
// let reportPerson = await cacheBustImport('./modules/actions/reportPerson.js');

class personPortalLiteActions extends actionsContainer {

  title = 'Person Portal - Lite';
  // visibleColumns = [0, 1, 5, 9, 22, 40];
  // // offColumns = [5, 9, 10, 11, 14, 24];
  // // offOrderingColumnIdx = 14;
  // PESColumns = [5, 18, 19, 20, 22, 24, 30, 31];
  // revColumns = [5, 8, 13, 14, 23, 24];
  // mgrsColumns = [0, 5, 9, 14, 22, 24, 40, 43];
  // statusColumnIdx = 24;
  // resetColumns = [0, 2, 3, 4, 5];

  visibleColumns = [0, 1, 2, 6, 22];
  // offColumns = [6, 8, 11, 12, 16, 27, 37];
  // offOrderingColumnIdx = 16;
  PESColumns = [6, 18, 19, 20, 22, 23, 25, 27, 35, 38];
  revColumns = [6, 8, 15, 16, 26, 27, 37];
  mgrsColumns = [0, 6, 9, 16, 25, 27, 44];
  statusColumnIdx = 27;
  resetColumns = [0, 1, 2, 6, 25];

  constructor(parent) {
    super(parent);
    const ReportAction = new action(this);
    const ReportPes = new pes(this);
    const ReportRevalidation = new revalidation(this);
    const ReportMgrsCbn = new mgrsCbn(this);
    const ReportSquads = new squads(this);
    const ReportAll = new all(this);
    const ReportRemoveOffb = new removeOffb(this);
    const ReportReload = new reload(this);
    const ReportReset = new reset(this);

    const ReportSave = new reportSave(this);
    const ReportSaveConfirm = new reportSaveConfirm(this);
    // const ReportPerson = new reportPerson(this);
  }
}

export { personPortalLiteActions as default };