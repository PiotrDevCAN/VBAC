/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class pesCancelBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ pesCancelBox.constructor');

        super(parent);
        this.listenForCancelPes();

        console.log('--- Function --- pesCancelBox.constructor');
    }

    listenForCancelPes() {
        var $this = this;
        $(document).on("click", ".btnPesCancel", function (e) {
            $(this).addClass("spinning");
            var cnum = $(this).data("cnum");
            var workerId = $(this).data("workerid");
            var now = new Date();
            var passportFirst = $(this).data("passportfirst");
            var passportSurname = $(this).data("passportSurname");

            $.ajax({
                url: "ajax/savePesStatus.php",
                data: {
                    psm_cnum: cnum,
                    psm_worker_id: workerId,
                    psm_status: "Cancel Requested",
                    psm_detail: "PES Cancel Requested",
                    PES_DATE_RESPONDED: now.toLocaleDateString("en-US"),
                    psm_passportFirst: passportFirst,
                    psm_passportSurname: passportSurname,
                },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    $("#savePesStatus").attr("disabled", false);

                    if (typeof $this.tableObj.table != "undefined") {
                        // We came from the PERSON PORTAL
                        $this.tableObj.table.ajax.reload();
                    } else {
                        // We came from the PES TRACKER
                        var cnum = resultObj.cnum;
                        var formattedEmail = resultObj.formattedEmailField;
                        $("#pesTrackerTable tr." + cnum)
                            .children(".formattedEmailTd:first")
                            .children(".formattedEmailDiv:first")
                            .html(formattedEmail);
                    }

                    $("#amendPesStatusModal").modal("hide");
                },
            });
        });
    }
}

export { pesCancelBox as default };