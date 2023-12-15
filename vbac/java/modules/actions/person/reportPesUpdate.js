/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportPesUpdate extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportPesUpdate();
    }

    listenForReportPesUpdate() {
        var $this = this;
        $(document).on("click", "#reportPesUpdate", function (e) {
            var cnums = [];
            var workerIds = [];
            $this.table.$('tr.selected').each(function () {
                var children = this.children;
                var cnumChild = children.item(0);
                var workerIdChild = children.item(1);
                var cnum = cnumChild.innerHTML;
                var workerid = workerIdChild.innerHTML;
                cnums.push(cnum);
                workerIds.push(workerid);
            });
            if (cnums.length > 0 && workerIds.length > 0) {
                $.ajax({
                    url: "ajax/setPesProgressing.php",
                    type: "POST",
                    data: {
                        cnum: cnums,
                        workerid: workerIds
                    },
                    success: function (result) {
                        var resultObj = JSON.parse(result);
                        $this.table.ajax.reload();
                        var message = resultObj.success ? "<br />Status Update Successful" : "<br />Status Update Failed";
                        message += "<br />" + resultObj.messages;
                        $('#messageModalBody').html(message);
                        $('#messageModal').modal('show');
                    },
                });
            } else {
                $('#messageModalBody').html("<p>Select Person records to update first</p>");
                $('#messageModal').modal('show');
            }
        });
    }
}

export { reportPesUpdate as default };