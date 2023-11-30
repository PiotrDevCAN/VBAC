/**
 *
 */

let OKTATableLoader = await cacheBustImport('./modules/functions/OKTATableLoader.js');
let OKTAUserEntry = await cacheBustImport('./cdi/OKTAUserEntry.js');

class OKTAUserList {

    initiatedTables = [];

    constructor() {

    }

    async initialisePillsTables() {
        var $this = this;
        var promises = [];
        var tables = $('.dataTable');
        tables.each((i, table) => {
            var tableId = $(table).attr('id');
            var groupName = $(table).data('group');
            var tablePromise = OKTATableLoader(tableId, groupName);
            promises.push(tablePromise);
        });

        await Promise.allSettled(promises)
            .then((results) => {
                results.forEach((result) => {
                    var table = result.value;
                    var tableId = table.table().node().id;
                    $this.initiatedTables[tableId] = table;
                });
            });
    }
}

const OktaUserList = new OKTAUserList();
OktaUserList.initialisePillsTables()
    .then(() => {
        OKTAUserEntry.tables = OktaUserList.initiatedTables;
    });

export { OktaUserList as default };