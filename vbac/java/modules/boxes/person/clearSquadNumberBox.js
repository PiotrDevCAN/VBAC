/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class clearSquadNumberBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ clearSquadNumberBox.constructor');

        super(parent);
        this.listenForClearAgileNumber();

        console.log('--- Function --- clearSquadNumberBox.constructor');
    }

    listenForClearAgileNumber() {
        var $this = this;
        $(document).on("click", ".btnClearSquadNumber", function (e) {
            $(this).addClass("spinning").attr("disabled", true);
            var cnum = $(this).data("cnum");
            var workerId = $(this).data("workerid");
            var version = $(this).data("version");
            $.ajax({
                url: "ajax/clearSquadNumber.php",
                data: {
                    cnum: cnum,
                    workerid: workerId,
                    version: version
                },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    if (resultObj.success) {
                        $(".spinning").removeClass("spinning").attr("disabled", false);
                        $this.tableObj.table.ajax.reload();
                    } else {
                        $("#editAgileSquadModal .modal-body").html(resultObj.messages);
                        $("#editAgileSquadModal").modal("show");
                    }
                },
            });
        });
    }
}

export { clearSquadNumberBox as default };