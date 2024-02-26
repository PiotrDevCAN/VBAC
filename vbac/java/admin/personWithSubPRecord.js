/*
 *
 *
 *
 */

let personWithSubPTable = await cacheBustImport('./modules/tables/personWithSubPRecord.js');

class personWithSubPRecord {

  table;
  tableObj;

  constructor() {

  }
}

const PersonWithSubP = new personWithSubPRecord();

const PersonWithSubPTable = new personWithSubPTable(document.tableType);

export { PersonWithSubP as default };