/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class pesProgressingBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ pesProgressingBox.constructor');

        super(parent);
        this.listenForPesProgress();

        console.log('--- Function --- pesProgressingBox.constructor');
    }

    listenForPesProgress() {
        var $this = this;
        $(document).on("click", ".btnPesProgressing", function (e) {
            $(this).addClass("spinning");
            var cnum = $(this).data("cnum");
            var workerId = $(this).data("workerid");
            var currentStatus = $(this).data("pesstatus");
            var status = $(this).data("newpesstatus");
            $.ajax({
                url: "ajax/setPesProgressing.php",
                type: "POST",
                data: {
                    cnum: cnum,
                    workerid: workerId,
                    currentStatus: currentStatus,
                    status: status
                },
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    $this.tableObj.table.ajax.reload();
                },
            });
        });
    }
}

export { pesProgressingBox as default };