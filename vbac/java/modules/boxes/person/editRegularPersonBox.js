/*
 *
 *
 *
 */

let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate_Regular.js');
let initialiseOtherDates = await cacheBustImport('./modules/functions/initialiseOtherDates_Regular.js');
let initialiseFormSelect2 = await cacheBustImport('./modules/functions/initialiseFormSelect2_Regular.js');
let postActions = await cacheBustImport('./modules/functions/postActions_Regular.js');

let saveBoardingForm = await cacheBustImport('./modules/functions/saveRegularBoarding.js');

let editPersonBox = await cacheBustImport('./modules/boxes/person/editPersonBox.js');

class editRegularPersonBox extends editPersonBox {

    static formId = 'boardingFormIbmer';
    static editButtonClass = 'btnEditRegularPerson';
    static saveButtonId = 'saveRegularBoarding';
    static updateButtonId = 'updateRegularBoarding';
    static resetButtonId = 'resetRegularBoarding';
    static initiatePesButtonId = 'initiateRegularPes';

    static FMCnumFieldId = 'person_FM_CNUM';
    static FMOriginalCnumFieldId = 'person_original_fm';
    static FMCheckMsg = 'personFmPanelBodyCheckMsg';
    static FMConfirmButtonId = 'confirmFmChangePerson';
    static FMResetButtonId = 'resetFmChangePerson';

    static initialiseStartEndDate = initialiseStartEndDate;
    static initialiseOtherDates = initialiseOtherDates;
    static initialiseFormSelect2 = initialiseFormSelect2;

    static postLoadActions = postActions;

    static saveBoardingForm = saveBoardingForm;

    constructor(parent) {
        console.log('+++ Function +++ editRegularPersonBox.constructor');

        super(parent, editRegularPersonBox);

        console.log('--- Function --- editRegularPersonBox.constructor');
    }
}

export { editRegularPersonBox as default };