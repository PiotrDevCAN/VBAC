/*
 *
 *
 *
 */

let personFinderTable = await cacheBustImport('./modules/tables/personFinder.js');
let transferPersonBox = await cacheBustImport('./modules/boxes/person/transferPersonBox.js');

class personFinder {

    table;
    tableObj;

    constructor() {

    }
}

const PersonFinder = new personFinder();

const PersonFinderTable = new personFinderTable();
PersonFinder.table = PersonFinderTable.table;
PersonFinder.tableObj = PersonFinderTable;

const TransferPersonBox = new transferPersonBox(PersonFinder);

export { PersonFinder as default };