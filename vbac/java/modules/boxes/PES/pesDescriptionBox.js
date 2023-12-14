/*
 *
 *
 *
 */

let pesDescriptionHover = await cacheBustImport('./modules/functions/pesDescriptionHover.js');
let box = await cacheBustImport('./modules/boxes/box.js');

class pesDescriptionBox extends box {

    constructor(parent) {
        console.log('+++ Function +++ pesDescriptionBox.constructor');

        super(parent);
        this.listenForPESStatusDescription();

        console.log('--- Function --- pesDescriptionBox.constructor');
    }

    listenForPESStatusDescription() {
        $(document).on("click", ".btnPesDescription", function (e) {
            e.preventDefault();
        });
        pesDescriptionHover();
    }
}

export { pesDescriptionBox as default };