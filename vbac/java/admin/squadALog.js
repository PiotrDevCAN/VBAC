/*
 *
 *
 *
 */

let squadALogTable = await cacheBustImport('./modules/tables/squadALog.js');

class squadALog {

    table;
    tableObj;

    constructor() {
        $('[data-toggle="tooltip"]').tooltip();
    }
}

const SquadALog = new squadALog();

const SquadALogTable = new squadALogTable();
squadALog.table = SquadALogTable.table;
squadALog.tableObj = SquadALogTable;

export { SquadALog as default };