/*
 *
 *
 *
 */

let pesInitiateBox = await cacheBustImport('./modules/boxes/PES/pesInitiateBox.js');

class pesInitiateFromPortalBox extends pesInitiateBox {

    static initButton = '.btnPesInitiate';
    static cnumFieldId = 'this';
    static workerIdFieldId = 'this';

    constructor(parent) {
        console.log('+++ Function +++ pesInitiateFromPortalBox.constructor');

        super(parent, pesInitiateFromPortalBox);

        console.log('--- Function --- pesInitiateFromPortalBox.constructor');
    }
}

export { pesInitiateFromPortalBox as default };