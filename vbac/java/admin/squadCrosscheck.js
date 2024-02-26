/*
 *
 *
 *
 */

let squadCrosscheckTable = await cacheBustImport('./modules/tables/squadCrosscheck.js');
let actions = await cacheBustImport('./modules/actions/squad/personSquadsActions.js');

let editAgileNumberBox = await cacheBustImport('./modules/boxes/person/editAgileNumberBox.js');
let clearSquadNumberBox = await cacheBustImport('./modules/boxes/person/clearSquadNumberBox.js');

class squadCrosscheck {

    table;
    tableObj;

    constructor() {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    }
}

const PersonPortal = new squadCrosscheck();

const PersonTable = new squadCrosscheckTable();
PersonPortal.table = PersonTable.table;
PersonPortal.tableObj = PersonTable;

// pass table to actions
const Actions = new actions(PersonPortal);

const EditAgileNumberBox = new editAgileNumberBox(PersonPortal);
const ClearSquadNumberBox = new clearSquadNumberBox(PersonPortal);

export { squadCrosscheck as default };