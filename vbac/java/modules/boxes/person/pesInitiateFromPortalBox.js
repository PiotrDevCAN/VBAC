/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');
let initiatePes = await cacheBustImport('./modules/functions/initiatePes.js');

class pesInitiateFromPortalBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ pesInitiateFromPortalBox.constructor');

        super(parent);
        this.listenForInitiatePesFromPortal();

        console.log('--- Function --- pesInitiateFromPortalBox.constructor');
    }

    listenForInitiatePesFromPortal() {
        var $this = this;
        $(document).on("click", ".btnPesInitiate", function (e) {
            $("#portalTitle").text("Person Portal - PES Report");
            $(this).addClass("spinning");
            var cnum = $(this).data("cnum");
            initiatePes(cnum, $this.tableObj.table);
        });
    }
}

export { pesInitiateFromPortalBox as default };