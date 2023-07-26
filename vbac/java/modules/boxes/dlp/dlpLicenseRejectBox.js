/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');
let approveRejectDlp = await cacheBustImport('./modules/functions/approveRejectDlp.js');

class dlpLicenseRejectBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ dlpLicenseRejectBox.constructor');

        super(parent);
        this.listenForRejectDlp();

        console.log('--- Function --- dlpLicenseRejectBox.constructor');
    }

    listenForRejectDlp() {
        var $this = this;
        $(document).on("click", ".btnDlpLicenseReject", function (e) {
            approveRejectDlp($(this), $this.table, "rejected");
        });
    }
}

export { dlpLicenseRejectBox as default };