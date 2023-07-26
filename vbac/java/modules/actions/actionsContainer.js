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
  }
}

export { actionsContainer as default };