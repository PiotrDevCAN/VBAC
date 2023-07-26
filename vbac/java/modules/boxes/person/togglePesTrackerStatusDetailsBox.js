/*
 *
 *
 *
 */

let box = await cacheBustImport('./modules/boxes/box.js');

class togglePesTrackerStatusDetailsBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ togglePesTrackerStatusDetailsBox.constructor');

        super(parent);
        this.listenForbtnTogglePesTrackerStatusDetails();

        console.log('--- Function --- togglePesTrackerStatusDetailsBox.constructor');
    }

    listenForbtnTogglePesTrackerStatusDetails() {
        $(document).on("click", ".btnTogglePesTrackerStatusDetails", function (e) {
            $(this).parent().children(".pesProcessStatusDisplay").toggle();
        });
    }
}

export { togglePesTrackerStatusDetailsBox as default };