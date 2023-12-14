/*
 *
 *
 *
 */

let pesInitiateBox = await cacheBustImport('./modules/boxes/box.js');

class pesInitiateFromPesTrackerBox extends pesInitiateBox {

    static initButton = '.btnPesInitiate';
    static cnumFieldId = "this";
    static workerIdFieldId = "this";

    constructor(parent) {
        console.log('+++ Function +++ pesInitiateFromPesTrackerBox.constructor');

        super(parent, pesInitiateFromPesTrackerBox);

        console.log('--- Function --- pesInitiateFromPesTrackerBox.constructor');
    }
}

export { pesInitiateFromPesTrackerBox as default };