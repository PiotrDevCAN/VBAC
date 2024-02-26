/*
 *
 *
 *
 */

let agileTribeTable = await cacheBustImport('./modules/tables/agileTribe.js');
let editTribeBox = await cacheBustImport('./modules/boxes/editTribeBox.js');

class agileTribe {

  table;
  version;

  constructor() {
    this.version = $('#version').prop('checked') ? 'Original' : 'New';
    $('#version').bootstrapToggle();
    $('#version').change({ tribe: this.table }, function (event) {
      console.log(event);
      console.log(event.data);
      event.data.tribe.ajax.reload();
    });
  }
}

const AgileTribe = new agileTribe();

const AgileTribeTable = new agileTribeTable(AgileTribe.version);
AgileTribe.table = AgileTribeTable.table;
AgileTribe.tableObj = AgileTribeTable;

const EditTribeBox = new editTribeBox(AgileTribe);

export { AgileTribe as default };