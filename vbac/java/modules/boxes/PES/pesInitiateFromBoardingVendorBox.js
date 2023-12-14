/*
 *
 *
 *
 */

let pesInitiateBox = await cacheBustImport('./modules/boxes/PES/pesInitiateBox.js');

class pesInitiateFromBoardingVendorBox extends pesInitiateBox {

    static initButton = '#initiateVendorPes';
    static cnumFieldId = "#resource_uid";
    static workerIdFieldId = "#resource_worker_id";

    constructor(parent) {
        console.log('+++ Function +++ pesInitiateFromBoardingVendorBox.constructor');

        super(parent, pesInitiateFromBoardingVendorBox);

        console.log('--- Function --- pesInitiateFromBoardingVendorBox.constructor');
    }
}

export { pesInitiateFromBoardingVendorBox as default };