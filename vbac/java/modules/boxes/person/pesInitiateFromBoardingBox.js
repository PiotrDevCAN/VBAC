/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');
let initiatePes = await cacheBustImport('./modules/functions/initiatePes.js');

class pesInitiateFromBoardingBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ pesInitiateFromBoardingBox.constructor');

        super(parent);
        this.listenForRegularInitiatePesFromBoarding();
        this.listenForVendorInitiatePesFromBoarding();

        console.log('--- Function --- pesInitiateFromBoardingBox.constructor');
    }

    listenForRegularInitiatePesFromBoarding() {
        var $this = this;
        $(document).on("click", "#initiateRegularPes", function (e) {
            $(this).addClass("spinning");
            var cnum = $("#person_uid").val();
            initiatePes(cnum);
        });
    }

    listenForVendorInitiatePesFromBoarding() {
        var $this = this;
        $(document).on("click", "#initiateVendorPes", function (e) {
            $(this).addClass("spinning");
            var cnum = $("#resource_uid").val();
            initiatePes(cnum);
        });
    }
}

export { pesInitiateFromBoardingBox as default };