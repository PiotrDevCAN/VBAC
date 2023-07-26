/*
 *
 *
 *
 */

let actionsContainer = await cacheBustImport('./modules/actions/actionsContainer.js');

// Dlp Licenses
let reporActiveRecords = await cacheBustImport('./modules/actions/dlp/reporActiveRecords.js');
let reporRequiresApproval = await cacheBustImport('./modules/actions/dlp/reporRequiresApproval.js');
let reporTransferredLicenses = await cacheBustImport('./modules/actions/dlp/reporTransferredLicenses.js');
let reporRejectedRecords = await cacheBustImport('./modules/actions/dlp/reporRejectedRecords.js');
let reporAllRecords = await cacheBustImport('./modules/actions/dlp/reporAllRecords.js');

class dlpActions extends actionsContainer {

  title = 'Licenses';
  resetColumns = [0, 1, 2, 3, 4, 5, 6, 7];

  constructor(parent) {
    super(parent);
    const ReporActiveRecords = new reporActiveRecords(this);
    const ReporRequiresApproval = new reporRequiresApproval(this);
    const ReporTransferredLicenses = new reporTransferredLicenses(this);
    const ReporRejectedRecords = new reporRejectedRecords(this);
    const ReporAllRecords = new reporAllRecords(this);
  }
}

export { dlpActions as default };