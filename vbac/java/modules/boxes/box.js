/*
 *
 *
 *
 */

class box {

  parent;
  table;
  tableObj;

  constructor(parent) {
    this.parent = parent;
    this.table = parent.table;
    this.tableObj = parent.tableObj;
  }
}

export { box as default };