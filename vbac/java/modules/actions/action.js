/*
 *
 *
 *
 * 
 * 
 */

class action {

    parent;
    table;
    tableObj;
    title;
  
    constructor(parent) {
      this.parent = parent;
      this.table = parent.table;
      this.tableObj = parent.tableObj;
      this.title = parent.title;

      if (this.constructor == action) {
        throw new Error('Cannot create a instance of Abstract class');
      }
    }

    disableRemoveOffboarding() {
      $("#reportRemoveOffb").attr("disabled", true);
    }

    enableRemoveOffboarding() {
      $("#reportRemoveOffb").attr("disabled", false);
    }
}

export { action as default };