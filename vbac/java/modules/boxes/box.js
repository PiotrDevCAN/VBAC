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

    if (this.constructor == box) {
      throw new Error('Cannot create a instance of Abstract class');
    }
  }
}

export { box as default };