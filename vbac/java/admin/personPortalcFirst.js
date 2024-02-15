/*
 *
 *
 *
 */

let personcFirstTable = await cacheBustImport('./modules/tables/personcFirst.js');
class personPortalcFirst {

    table;
    tableObj;

    constructor() {
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover();
    }
}

const PersonPortal = new personPortalcFirst();

const PersonTable = new personcFirstTable();
PersonPortal.table = PersonTable.table;
PersonPortal.tableObj = PersonTable;

export { personPortalcFirst as default };