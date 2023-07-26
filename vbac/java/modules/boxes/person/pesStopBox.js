/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class pesStopBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ pesStopBox.constructor');

        super(parent);
        this.listenForStopPes();

        console.log('--- Function --- pesStopBox.constructor');
    }

    listenForStopPes() {
        var $this = this;
        $(document).on("click", ".btnPesStop", function (e) {
            $(this).addClass("spinning");
            var cnum = $(this).data("cnum");
            var notesid = $(this).data("notesid");
            var email = $(this).data("email");
            var now = new Date();
            var passportFirst = $(this).data("passportfirst");
            var passportSurname = $(this).data("psm_passportSurname");

            $.ajax({
                url: "ajax/sendPesStopRequestedEmail.php",
                data: {
                    psm_cnum: cnum,
                },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);

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
                },
            });
        });
    }
}

export { pesStopBox as default };