/*
 *
 *
 *
 */

class index {
    constructor() {
        this.listenForOnBoarding();
        this.listenForOffBoarding();
    }

    listenForOnBoarding() {
        $(document).on("click", "#onBoardingBtn", function () {
            window.open("pb_onboard.php", "_self");
        });
    }

    listenForOffBoarding() {
        $(document).on("click", "#offBoardingBtn", function () {
            window.open("pb_offboard.php", "_self");
        });
    }
}

const Index = new index();

export { Index as default };