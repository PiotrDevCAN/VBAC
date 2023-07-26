/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');
let initiatePes = await cacheBustImport('./modules/functions/initiatePes.js');

class pesInitiateFromPesTrackerBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ pesInitiateFromPesTrackerBox.constructor');

        super(parent);
        this.listenForInitiatePesFromTracker();

        console.log('--- Function --- pesInitiateFromPesTrackerBox.constructor');
    }

    listenForInitiatePesFromTracker() {
        var $this = this;
        $(document).on("click", ".btnPesInitiate", function (e) {
            $("#portalTitle").text("Person Portal - PES Report");
            $(this).addClass("spinning");
            var cnum = $(this).data("cnum");
            initiatePes(cnum);
        });
    }
}

export { pesInitiateFromPesTrackerBox as default };