/*
 *
 *
 *
 */

let pesInitiateBox = await cacheBustImport('./modules/boxes/PES/pesInitiateBox.js');

class pesInitiateFromBoardingRegularBox extends pesInitiateBox {

    static initButton = '#initiateRegularPes';
    static cnumFieldId = "#person_uid";
    static workerIdFieldId = "#person_workerid";

    constructor(parent) {
        console.log('+++ Function +++ pesInitiateFromBoardingRegularBox.constructor');

        super(parent, pesInitiateFromBoardingRegularBox);

        console.log('--- Function --- pesInitiateFromBoardingRegularBox.constructor');
    }
}

export { pesInitiateFromBoardingRegularBox as default };