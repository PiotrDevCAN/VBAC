/*
 *
 *
 *
 */

let statusCrosscheckTable = await cacheBustImport('./modules/tables/statusCrosscheck.js');
class statusCrosscheck {

    table;
    tableObj;

    constructor() {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    }
}

const PersonPortal = new statusCrosscheck();

const PersonTable = new statusCrosscheckTable();
PersonPortal.table = PersonTable.table;
PersonPortal.tableObj = PersonTable;

export { statusCrosscheck as default };