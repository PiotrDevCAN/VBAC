/**
 *
 */

let OKTAUserEntry = await cacheBustImport('./cdi/OKTAUserEntry.js');

class OKTAUserList {

    table;

    constructor() {
        this.initialisePillsTables();
    }

    initialisePillsTables() {
        var $this = this;
        var tables = $('.dataTable');
        tables.each((i, table) => {
            var tableId = $(table).attr('id');
            var groupName = $(table).data('group');
            $this.initialiseTable(tableId, groupName);
        });
    }

    initialiseTable(tableId, groupName) {
        // DataTable
        this.table = $('#' + tableId).DataTable({
            autoWidth: false,
            processing: true,
            responsive: false,
            dom: 'Blfrtip',
            ajax: {
                "url": "ajax/populateOktaGroupMembers.php",
                "type": "POST",
                "data": {
                    "group": groupName
                },
                beforeSend: function (jqXHR, settings) {
                    $.each(xhrPool, function (idx, jqXHR) {
                        console.log('abort jqXHR');
                        jqXHR.abort();  // basically, cancel any existing request, so this one is the only one running
                        xhrPool.splice(idx, 1);
                    });
                    xhrPool.push(jqXHR);
                }
            },
            columns: [
                { data: "NAME", "defaultContent": "" },
                { data: "EMAIL_ADDRESS", "defaultContent": "" },
            ]
        });
    }
}

const OktaUserList = new OKTAUserList();
OKTAUserEntry.table = OktaUserList.table;

export { OktaUserList as default };