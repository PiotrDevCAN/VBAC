/*
 *
 *
 *
 */

let action = await cacheBustImport('./modules/actions/action.js');

class reportPerson extends action {

    constructor(parent) {
        super(parent);
        this.listenForReportPerson();
    }

    listenForReportPerson() {
        var $this = this;
        $(document).on("click", "#reportPerson", function (e) {
            $this.enableRemoveOffboarding();
            $this.table
                .columns([
                    0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 15, 16, 17, 18, 19,
                    20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36,
                    37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47,
                ])
                .visible(true, false);
            $this.table
                .columns([
                    2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 33, 34, 24,
                ])
                .visible(true);
            $this.table.columns.draw();
        });
    }
}

export { reportPerson as default };