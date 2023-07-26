/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class dlpLicenseDeleteBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ dlpLicenseDeleteBox.constructor');

        super(parent);
        this.listenForDeleteDlp();

        console.log('--- Function --- dlpLicenseDeleteBox.constructor');
    }

    listenForDeleteDlp() {
        var $this = this;
        $(document).on("click", ".btnDlpLicenseDelete", function (e) {
            var cnum = $(this).data("cnum");
            var hostname = $(this).data("hostname");
            var transferred = $(this).data("transferred");
            $.ajax({
                url: "ajax/dlpDelete.php",
                data: { cnum: cnum, hostname: hostname, transferred: transferred },
                type: "POST",
                success: function (result) {
                    var resultObj = JSON.parse(result);
                    $this.tableObj.table.ajax.reload();
                    delete licences[resultObj.cnum]; // Remove this entry from the licences object.
                },
            });
        });
    }
}

export { dlpLicenseDeleteBox as default };