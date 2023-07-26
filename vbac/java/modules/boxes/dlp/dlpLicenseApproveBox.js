/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');
let approveRejectDlp = await cacheBustImport('./modules/functions/approveRejectDlp.js');

class dlpLicenseApproveBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ dlpLicenseApproveBox.constructor');

        super(parent);
        this.listenForApproveDlp();

        console.log('--- Function --- dlpLicenseApproveBox.constructor');
    }

    listenForApproveDlp() {
        var $this = this;
        $(document).on("click", ".btnDlpLicenseApprove", function (e) {
            var record = $(this);
            $("#dlpInstallVerfied").prop("checked", false);
            $(document).on('hidden.bs.modal', '#confirmVerified', function (event) {
                if ($("#dlpInstallVerfied").is(":checked")) {
                    approveRejectDlp(record, $this.table, "approved");
                }
            });
            $("#confirmVerified").modal("show");
        });
    }
}

export { dlpLicenseApproveBox as default };