/*
 *
 *
 *
 */

class actionsContainer {

  parent;
  table;
  tableObj;

  constructor(parent) {
    this.parent = parent;
    this.table = parent.table;
    this.tableObj = parent.tableObj;

    if (this.constructor == actionsContainer) {
      throw new Error('Cannot create a instance of Abstract class');
    }
  }
}

export { actionsContainer as default };