/*
 *
 *
 *
 */

let pesInitiateFromBoardingBox = await cacheBustImport('./modules/boxes/person/pesInitiateFromBoardingBox.js');
let pesDescriptionBox = await cacheBustImport('./modules/boxes/person/pesDescriptionBox.js');

let RegularOnboardEntry = await cacheBustImport('./modules/forms/regularOnboardEntry.js');
let VendorOnboardEntry = await cacheBustImport('./modules/forms/vendorOnboardEntry.js');

let FormMessageArea = await cacheBustImport('./modules/helpers/formMessageArea.js');

let knownCNUMs = await cacheBustImport('./modules/dataSources/knownCNUMs.js');
let knownWorkerIDs = await cacheBustImport('./modules/dataSources/knownWorkerIDs.js');
let knownExternalEmails = await cacheBustImport('./modules/dataSources/knownExternalEmails.js');
let knownIBMEmails = await cacheBustImport('./modules/dataSources/knownIBMEmails.js');
let knownKyndrylEmails = await cacheBustImport('./modules/dataSources/knownKyndrylEmails.js');

class onBoarding {

    regularFormInitialized;
    vendorFormInitialized;

    constructor() {
        this.initialiseTabs();
        this.listenForTabSelect();

        FormMessageArea.showMessageArea();

        let knownCNUMsPromise = knownCNUMs.getCNUMs();
        let knownWorkerIDsPromise = knownWorkerIDs.getWorkerIDs();
        let knownExternalEmailsPromise = knownExternalEmails.getEmails();
        let knownIBMEmailsPromise = knownIBMEmails.getEmails();
        let knownKyndrylEmailsPromise = knownKyndrylEmails.getEmails();

        const promises = [
            knownCNUMsPromise, 
            knownWorkerIDsPromise,
            knownExternalEmailsPromise, 
            knownIBMEmailsPromise, 
            knownKyndrylEmailsPromise
        ];
        Promise.allSettled(promises)
            .then((results) => {
                results.forEach((result) => console.log(result.status));
                FormMessageArea.clearMessageArea();
            });
    }

    initialiseTabs() {
        $('#myTabs a').click(function (e) {
            e.preventDefault()
            $(this).tab('show');
        });
    }

    listenForTabSelect() {
        $('.nav-tabs a').on('shown.bs.tab', function (e) {
            let selectedTabId = $(e.target).attr('id');
            switch (selectedTabId) {
                case 'showRegularForm':
                    if (this.regularFormInitialized !== true) {
                        RegularOnboardEntry.initialiseForm();
                        this.regularFormInitialized = true;
                    }
                    break;
                case 'showVendorForm':
                    if (this.vendorFormInitialized !== true) {
                        VendorOnboardEntry.initialiseForm();
                        this.vendorFormInitialized = true;
                    }
                    break;
                default:
                    break;
            }
        });
    }
}

const OnBoarding = new onBoarding();

const PesInitiateFromBoardingBox = new pesInitiateFromBoardingBox(OnBoarding);
const PesDescriptionBox = new pesDescriptionBox(OnBoarding);

$('#myTabs a[href="#regularTab"]').tab('show'); // Select tab by name

export { OnBoarding as default };