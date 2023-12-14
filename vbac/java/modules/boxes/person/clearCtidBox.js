/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class clearCtidBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ clearCtidBox.constructor');

        super(parent);
        this.listenForClearCtid();

        console.log('--- Function --- clearCtidBox.constructor');
    }

    listenForClearCtid() {
        var $this = this;
        $(document).on("click", ".btnClearCtid", function (e) {
            $(this).addClass("spinning");
            var cnum = $(this).data("cnum");
            var workerId = $(this).data("workerid");
            $.ajax({
                url: "ajax/clearCtid.php",
                type: "POST",
                data: {
                    cnum: cnum,
                    workerid: workerId
                },
                success: function (result) {
                    console.log(result);
                    var resultObj = JSON.parse(result);
                    $this.tableObj.table.ajax.reload();
                },
            });
        });
    }
}

export { clearCtidBox as default };