/*
 *
 *
 *
 */

let initialiseStartEndDate = await cacheBustImport('./modules/functions/initialiseStartEndDate_Vendor.js');
let initialiseOtherDates = await cacheBustImport('./modules/functions/initialiseOtherDates_Vendor.js');
let initialiseFormSelect2 = await cacheBustImport('./modules/functions/initialiseFormSelect2_Vendor.js');

let saveBoardingForm = await cacheBustImport('./modules/functions/saveVendorBoarding.js');

let editPersonBox = await cacheBustImport('./modules/boxes/person/editPersonBox.js');

class editVendorPersonBox extends editPersonBox {

    static formId = 'boardingFormNotIbmer';
    static editButtonClass = 'btnEditVendorPerson';
    static saveButtonId = 'saveVendorBoarding';
    static updateButtonId = 'updateVendorBoarding';
    static resetButtonId = 'resetVendorBoarding';
    static initiatePesButtonId = 'initiateVendorPes';

    static FMCnumFieldId = 'resource_FM_CNUM';
    static FMOriginalCnumFieldId = 'person_original_fm';
    static FMCheckMsg = 'resourceFmPanelBodyCheckMsg';
    static FMConfirmButtonId = 'confirmFmChangeResource';
    static FMResetButtonId = 'resetFmChangeResource';

    static initialiseStartEndDate = initialiseStartEndDate;
    static initialiseOtherDates = initialiseOtherDates;
    static initialiseFormSelect2 = initialiseFormSelect2;

    static saveBoardingForm = saveBoardingForm;

    constructor(parent) {
        console.log('+++ Function +++ editVendorPersonBox.constructor');

        super(parent, editVendorPersonBox);

        console.log('--- Function --- editVendorPersonBox.constructor');
    }
}

export { editVendorPersonBox as default };