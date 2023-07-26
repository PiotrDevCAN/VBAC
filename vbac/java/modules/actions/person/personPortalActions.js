/*
 *
 *
 *
 */

let actionsContainer = await cacheBustImport('./modules/actions/actionsContainer.js');

// full
let action = await cacheBustImport('./modules/actions/person/reportAction.js');
let offboarding = await cacheBustImport('./modules/actions/person/reportOffboarding.js');
let offboarded = await cacheBustImport('./modules/actions/person/reportOffboarded.js');
let pes = await cacheBustImport('./modules/actions/person/reportPes.js');
let revalidation = await cacheBustImport('./modules/actions/person/reportRevalidation.js');
let mgrsCbn = await cacheBustImport('./modules/actions/person/reportMgrsCbn.js');
let all = await cacheBustImport('./modules/actions/reportAll.js');
let removeOffb = await cacheBustImport('./modules/actions/person/reportRemoveOffb.js');
let reload = await cacheBustImport('./modules/actions/reportReload.js');
let reset = await cacheBustImport('./modules/actions/reportReset.js');

let reportSave = await cacheBustImport('./modules/actions/reportSave.js');
let reportSaveConfirm = await cacheBustImport('./modules/actions/reportSaveConfirm.js');
// let reportPerson = await cacheBustImport('./modules/actions/reportPerson.js');

class personPortalActions extends actionsContainer {

  title = 'Person Portal';
  visibleColumns = [0, 1, 2, 6, 25];
  offColumns = [6, 8, 11, 12, 16, 27, 37];
  offOrderingColumnIdx = 16;
  PESColumns = [6, 21, 22, 23, 25, 27, 35, 38];
  revColumns = [6, 8, 15, 16, 26, 27, 37];
  mgrsColumns = [0, 6, 9, 16, 25, 27, 44];
  statusColumnIdx = 27;
  resetColumns = [0, 1, 2, 6, 25];

  constructor(parent) {
    super(parent);
    const ReportAction = new action(this);
    const ReportOffboarding = new offboarding(this);
    const ReportOffboarded = new offboarded(this);
    const ReportPes = new pes(this);
    const ReportRevalidation = new revalidation(this);
    const ReportMgrsCbn = new mgrsCbn(this);
    const ReportAll = new all(this);
    const ReportRemoveOffb = new removeOffb(this);
    const ReportReload = new reload(this);
    const ReportReset = new reset(this);

    const ReportSave = new reportSave(this);
    const ReportSaveConfirm = new reportSaveConfirm(this);
    // const ReportPerson = new reportPerson(this);
  }
}

export { personPortalActions as default };